<?php
session_start();
require_once 'db.php';

// Security Check: Ensure only admins access this
// if ($_SESSION['role'] !== 'admin') { header("Location: landing.php"); exit(); }

$message = "";
$admin_id = $_SESSION['user_id'] ?? 1; // Fallback to 1 for testing
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'All';

// --- 1. HANDLE BULK ACTIONS & AUDIT LOGGING ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bulk_action'])) {
    $product_ids = $_POST['selected_products'] ?? [];
    $action = $_POST['bulk_action']; // 'active' or 'rejected'
    
    if (!empty($product_ids)) {
        try {
            $pdo->beginTransaction();

            // A. Update Product Statuses
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            $update_sql = "UPDATE products SET status = ? WHERE id IN ($placeholders)";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute(array_merge([$action], $product_ids));

            // B. Write to Audit Logs for each item
            $log_sql = "INSERT INTO audit_logs (admin_id, action_type, target_id, details) VALUES (?, ?, ?, ?)";
            $log_stmt = $pdo->prepare($log_sql);
            
            foreach($product_ids as $id) {
                $details = "Bulk moderation action performed via Terminal.";
                $log_stmt->execute([$admin_id, $action, $id, $details]);
            }

            $pdo->commit();
            $message = count($product_ids) . " items successfully " . ($action == 'active' ? 'Approved' : 'Rejected');
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "System Error: " . $e->getMessage();
        }
    }
}

// --- 2. FETCH PENDING ITEMS WITH FILTERS ---
$query = "SELECT p.*, u.username, u.hub_location 
          FROM products p 
          JOIN users u ON p.farmer_id = u.id 
          WHERE p.status = 'pending'";
$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR u.username LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category !== 'All') {
    $query .= " AND p.category = ?";
    $params[] = $category;
}
$query .= " ORDER BY p.created_at ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pending_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Control Terminal | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-checkbox:checked + .product-card { border-color: #eab308; background-color: rgba(234, 179, 8, 0.03); }
    </style>
</head>
<body class="bg-[#0b0f1a] text-slate-300 min-h-screen pb-40">

    <nav class="h-20 border-b border-white/5 flex items-center px-10 justify-between sticky top-0 bg-[#0b0f1a]/80 backdrop-blur-xl z-40">
        <div class="flex items-center gap-4">
            <div class="bg-yellow-500 text-slate-900 p-2 rounded-lg"><i class="fas fa-shield-check"></i></div>
            <h1 class="font-black uppercase tracking-tighter text-white">Review <span class="text-yellow-500">Terminal</span></h1>
        </div>
        <div class="flex items-center gap-6">
            <a href="admin_audit.php" class="text-[10px] font-black uppercase text-slate-500 hover:text-white transition-all">Audit Logs</a>
            <a href="admin_dashboard.php" class="bg-slate-800 text-white px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-slate-700 transition-all">Exit</a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-12">
        
        <form method="GET" class="flex flex-col md:flex-row gap-4 mb-10">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-600"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Filter by product name or farmer..." 
                       class="w-full bg-slate-900 border border-white/5 rounded-2xl pl-12 pr-6 py-4 text-sm font-bold outline-none focus:border-yellow-500/50">
            </div>
            <select name="category" class="bg-slate-900 border border-white/5 rounded-2xl px-8 py-4 text-xs font-black uppercase tracking-widest outline-none appearance-none cursor-pointer">
                <option value="All">All Categories</option>
                <option <?= $category == 'Tubers' ? 'selected' : '' ?>>Tubers</option>
                <option <?= $category == 'Grains' ? 'selected' : '' ?>>Grains</option>
                <option <?= $category == 'Fruits' ? 'selected' : '' ?>>Fruits</option>
            </select>
            <button type="submit" class="bg-yellow-500 text-slate-900 px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-yellow-400">Filter</button>
        </form>

        <?php if($message): ?>
            <div class="bg-blue-600/10 border border-blue-600/20 text-blue-400 p-6 rounded-[2rem] mb-10 flex items-center gap-4 font-bold text-xs uppercase tracking-widest">
                <i class="fas fa-info-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>

        <form id="bulkForm" method="POST">
            <div class="flex justify-between items-center mb-6 px-4">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-500"><?= count($pending_items) ?> Pending Submissions</p>
                <button type="button" onclick="toggleAll()" class="text-[10px] font-black uppercase text-yellow-500 hover:underline">Toggle All</button>
            </div>

            <div class="grid gap-4">
                <?php if(empty($pending_items)): ?>
                    <div class="text-center py-24 bg-slate-900/20 rounded-[3rem] border border-dashed border-white/5">
                        <i class="fas fa-check-circle text-4xl text-slate-700 mb-4"></i>
                        <p class="text-slate-500 font-bold text-xs uppercase tracking-widest italic">All yields have been moderated.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($pending_items as $item): ?>
                    <label class="block relative cursor-pointer">
                        <input type="checkbox" name="selected_products[]" value="<?= $item['id'] ?>" class="card-checkbox hidden" onchange="updateUI()">
                        <div class="product-card bg-slate-900/50 border border-white/5 p-6 rounded-[2.5rem] flex flex-col md:flex-row items-center gap-8 transition-all hover:border-white/10">
                            <div class="relative">
                                <img src="<?= $item['image_url'] ?>" class="w-24 h-24 rounded-3xl object-cover shadow-2xl">
                                <div class="absolute -top-2 -left-2 w-6 h-6 bg-slate-800 border-2 border-slate-700 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full opacity-0 check-dot"></div>
                                </div>
                            </div>
                            
                            <div class="flex-1 text-center md:text-left">
                                <p class="text-[9px] font-black text-yellow-500 uppercase tracking-[0.2em] mb-1"><?= $item['category'] ?></p>
                                <h3 class="text-lg font-black text-white"><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="flex items-center justify-center md:justify-start gap-4 mt-1">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">Farmer: <?= htmlspecialchars($item['username']) ?></span>
                                    <span class="text-slate-700">•</span>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase"><?= $item['hub_location'] ?></span>
                                </div>
                            </div>

                            <div class="text-center md:text-right px-8 border-l border-white/5">
                                <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Price</p>
                                <p class="text-xl font-black text-white"><?= number_format($item['price']) ?> <span class="text-[10px] text-yellow-500">XAF</span></p>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="bulkBar" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-white rounded-[3rem] p-4 flex items-center gap-10 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.6)] border border-slate-200 opacity-0 translate-y-20 transition-all pointer-events-none">
                <div class="pl-8">
                    <p id="selectionCount" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">0 Selected</p>
                </div>
                <div class="flex gap-2">
                    <button type="submit" name="bulk_action" value="active" class="bg-green-600 text-white px-8 py-5 rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 shadow-xl shadow-green-900/20">Approve Selected</button>
                    <button type="submit" name="bulk_action" value="rejected" class="bg-red-50 text-red-600 px-8 py-5 rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-red-100">Reject</button>
                </div>
            </div>
        </form>
    </main>

    <script>
        function updateUI() {
            const checked = document.querySelectorAll('input[name="selected_products[]"]:checked');
            const bar = document.getElementById('bulkBar');
            const countLabel = document.getElementById('selectionCount');
            
            if (checked.length > 0) {
                bar.classList.remove('opacity-0', 'translate-y-20', 'pointer-events-none');
                countLabel.innerText = `${checked.length} Items Selected`;
            } else {
                bar.classList.add('opacity-0', 'translate-y-20', 'pointer-events-none');
            }
        }

        function toggleAll() {
            const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(c => c.checked = !allChecked);
            updateUI();
        }
    </script>
</body>
</html>
 <?php
session_start();
require_once 'db.php';

// Access Control: Ensure only managers can access
// if ($_SESSION['role'] !== 'manager') { header("Location: dashboard.php"); exit(); }

$message = "";

// Handle Stock Updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_stock'])) {
    $item_id = $_POST['item_id'];
    $new_qty = $_POST['quantity'];
    
    $stmt = $pdo->prepare("UPDATE hub_inventory SET current_stock = ?, last_restocked = CURRENT_TIMESTAMP WHERE id = ?");
    if ($stmt->execute([$new_qty, $item_id])) {
        $message = "Inventory successfully synchronized.";
    }
}

// Fetch all inventory items
$stmt = $pdo->query("SELECT * FROM hub_inventory ORDER BY item_name ASC");
$inventory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Inventory | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b border-slate-200 h-20 flex items-center px-8 justify-between sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <div class="bg-slate-900 p-2 rounded-lg">
                <i class="fas fa-warehouse text-white text-xs"></i>
            </div>
            <span class="font-black uppercase tracking-widest text-[10px]">Regional Logistics Manager</span>
        </div>
        <a href="admin_inquiries.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900">Support Queue</a>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-12">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900">Hub <span class="text-green-800">Inventory</span></h1>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-2">Manage essential agricultural inputs</p>
        </div>

        <?php if($message): ?>
            <div class="bg-green-800 text-white p-5 rounded-2xl mb-8 flex items-center gap-3">
                <i class="fas fa-sync-alt animate-spin"></i>
                <p class="text-[10px] font-black uppercase tracking-widest"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($inventory as $item): ?>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-900"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Last Update: <?php echo date('d M', strtotime($item['last_restocked'])); ?></p>
                    </div>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase"><?php echo $item['unit_type']; ?></span>
                </div>

                <div class="mb-8">
                    <div class="flex justify-between mb-2">
                        <span class="text-[10px] font-black uppercase text-slate-400">Current Stock</span>
                        <span class="text-sm font-black <?php echo $item['current_stock'] < 20 ? 'text-red-600' : 'text-green-800'; ?>">
                            <?php echo $item['current_stock']; ?> units
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-green-800 h-full" style="width: <?php echo min(($item['current_stock'] / 100) * 100, 100); ?>%"></div>
                    </div>
                </div>

                <form action="inventory_manager.php" method="POST" class="flex gap-2">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="number" name="quantity" value="<?php echo $item['current_stock']; ?>" class="w-20 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold outline-none focus:border-green-800">
                    <button type="submit" name="update_stock" class="flex-1 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-green-800 transition-all">
                        Sync
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>
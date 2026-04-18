 <?php
session_start();
require_once 'db.php';

// Access Control: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

try {
    // 1. Fetch Stats for this specific user
    $stats_query = "SELECT 
        COUNT(*) as total_items,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_items,
        SUM(price * quantity) as total_value
        FROM products WHERE farmer_id = ?";
    $stmt = $pdo->prepare($stats_query);
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();

    // 2. Fetch all products for this user
    $products_query = "SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC";
    $stmt = $pdo->prepare($products_query);
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-slate-50 flex">

    <aside class="w-72 bg-[#064e3b] min-h-screen text-white p-8 flex flex-col fixed left-0 top-0">
        <div class="mb-12">
            <span class="block font-extrabold text-2xl tracking-tighter uppercase leading-none">Agri-Tech <span class="text-[#8b5e34]">CM</span></span>
            <span class="text-[8px] font-black uppercase tracking-[0.4em] text-green-300 opacity-60">National OS</span>
        </div>

        <nav class="space-y-4 flex-1">
            <a href="landing.php" class="flex items-center gap-4 text-sm font-bold opacity-60 hover:opacity-100 transition-all">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            <a href="products.php" class="flex items-center gap-4 text-sm font-bold bg-white/10 p-4 rounded-2xl">
                <i class="fas fa-box w-5"></i> My Products
            </a>
            <a href="orders.php" class="flex items-center gap-4 text-sm font-bold opacity-60 hover:opacity-100 transition-all">
                <i class="fas fa-shopping-cart w-5"></i> Orders
            </a>
        </nav>

        <div class="pt-8 border-t border-white/10">
            <a href="logout.php" class="flex items-center gap-4 text-sm font-bold text-red-400">
                <i class="fas fa-power-off w-5"></i> Logout
            </a>
        </div>
    </aside>

    <main class="ml-72 flex-1 p-12">
        
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Inventory Management</h1>
                <p class="text-slate-500 font-medium">Manage your harvests and live market listings.</p>
            </div>
            <a href="add_product.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-green-800 transition-all shadow-xl">
                <i class="fas fa-plus"></i> List New Harvest
            </a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm stat-card">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Total Listings</p>
                <p class="text-4xl font-black text-slate-900"><?= $stats['total_items'] ?? 0 ?></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm stat-card">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Active on Market</p>
                <p class="text-4xl font-black text-green-700"><?= $stats['active_items'] ?? 0 ?></p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm stat-card">
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Inventory Value</p>
                <p class="text-4xl font-black text-slate-900"><?= number_format($stats['total_value'] ?? 0) ?> <span class="text-sm font-bold text-slate-400">XAF</span></p>
            </div>
        </div>

        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-slate-400 tracking-widest">Product Details</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-slate-400 tracking-widest">Quantity</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-slate-400 tracking-widest">Price (Unit)</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-slate-400 tracking-widest">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-800">
                                            <i class="fas fa-leaf"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900"><?= htmlspecialchars($product['product_name']) ?></p>
                                            <p class="text-xs text-slate-400"><?= htmlspecialchars($product['category'] ?? 'Produce') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 font-bold text-slate-700"><?= $product['quantity'] ?> KG</td>
                                <td class="px-8 py-6 font-bold text-slate-900"><?= number_format($product['price']) ?> XAF</td>
                                <td class="px-8 py-6">
                                    <?php if ($product['status'] == 'active'): ?>
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Live</span>
                                    <?php else: ?>
                                        <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 text-right space-x-2">
                                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="p-2 text-slate-400 hover:text-green-700"><i class="fas fa-edit"></i></a>
                                    <button class="p-2 text-slate-400 hover:text-red-500" onclick="confirmDelete(<?= $product['id'] ?>)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-slate-400 font-medium">
                                No products found. Click "List New Harvest" to begin.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to remove this listing?")) {
                window.location.href = "delete_product.php?id=" + id;
            }
        }
    </script>
</body>
</html>
<?php
session_start();
require_once 'db.php';

// Fetch ONLY active products
try {
    $stmt = $pdo->query("
        SELECT p.*, u.hub_location, u.username as farmer_name 
        FROM products p 
        JOIN users u ON p.farmer_id = u.id 
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC
    ");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marketplace | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">

    <nav class="h-20 bg-white border-b border-slate-100 flex items-center px-10 justify-between">
        <a href="landing.php" class="font-black text-xl uppercase tracking-tighter">Agri-Tech <span class="text-green-800">CM</span></a>
        <a href="add_product.php" class="bg-green-800 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest">Sell My Yield</a>
    </nav>

    <main class="max-w-7xl mx-auto py-16 px-6">
        <h2 class="text-4xl font-black text-slate-900 mb-12">Active <span class="text-green-800">Trade Floor</span></h2>
        
        <div class="grid md:grid-cols-3 gap-10">
            <?php foreach($products as $p): ?>
            <div class="bg-white rounded-[3rem] overflow-hidden border border-slate-100 hover:shadow-2xl transition-all">
                <img src="<?php echo $p['image_url']; ?>" class="w-full h-64 object-cover">
                <div class="p-8">
                    <div class="flex items-center gap-2 text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        <i class="fas fa-location-dot text-green-700"></i> <?php echo $p['hub_location']; ?>
                    </div>
                    <h3 class="text-xl font-black mb-4"><?php echo $p['name']; ?></h3>
                    <div class="flex justify-between items-center bg-slate-50 p-5 rounded-2xl">
                        <span class="text-lg font-black"><?php echo number_format($p['price']); ?> <small class="text-[10px] uppercase text-green-700">XAF</small></span>
                        <a href="product_detail.php?id=<?php echo $p['id']; ?>" class="text-green-800"><i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>
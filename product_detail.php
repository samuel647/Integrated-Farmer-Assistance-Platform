 <?php
session_start();
require_once 'db.php';

// Initialize Filter Variables
$category = $_GET['category'] ?? 'All';
$search = $_GET['search'] ?? '';

try {
    // Build Dynamic Query
    $query = "SELECT p.*, u.hub_location, u.username as farmer_name 
              FROM products p 
              JOIN users u ON p.farmer_id = u.id 
              WHERE p.status = 'active'";
    $params = [];

    if ($category !== 'All') {
        $query .= " AND p.category = ?";
        $params[] = $category;
    }

    if (!empty($search)) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National Trade Floor | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .filter-pill:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b border-slate-200 h-20 flex items-center px-6 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto w-full flex justify-between items-center">
            <a href="landing.php" class="flex items-center gap-3">
                <div class="bg-green-800 p-2 rounded-xl">
                    <i class="fas fa-leaf text-white"></i>
                </div>
                <span class="font-black text-slate-900 uppercase text-lg tracking-tighter">Agri-Tech <span class="text-yellow-500">CM</span></span>
            </a>
            <div class="flex items-center gap-6">
                <a href="user_dashboard.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-green-800">My Account</a>
                <a href="landing.php" class="bg-slate-100 text-slate-900 px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest">Exit Market</a>
            </div>
        </div>
    </nav>

    <header class="bg-white border-b border-slate-100 py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl font-black text-slate-900 mb-8 tracking-tight">National <span class="text-green-800">Trade Floor</span></h1>
            
            <form action="marketplace.php" method="GET" class="grid lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2 relative">
                    <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search for cocoa, corn, tubers..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-14 pr-6 py-5 text-sm font-bold outline-none focus:border-green-800 transition-all">
                </div>
                
                <select name="category" onchange="this.form.submit()" 
                        class="bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 text-sm font-bold outline-none focus:border-green-800 appearance-none">
                    <option value="All">All Categories</option>
                    <option value="Tubers" <?php if($category == 'Tubers') echo 'selected'; ?>>Tubers & Roots</option>
                    <option value="Grains" <?php if($category == 'Grains') echo 'selected'; ?>>Grains & Cereals</option>
                    <option value="Vegetables" <?php if($category == 'Vegetables') echo 'selected'; ?>>Fresh Vegetables</option>
                    <option value="Fruits" <?php if($category == 'Fruits') echo 'selected'; ?>>Tropical Fruits</option>
                </select>

                <button type="submit" class="bg-green-800 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-green-700 transition-all shadow-xl shadow-green-900/10">
                    Apply Filters
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $product): ?>
                <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden flex flex-col hover:shadow-2xl transition-all group">
                    <div class="relative h-56 overflow-hidden">
                        <img src="<?php echo $product['image_url'] ?: 'https://images.unsplash.com/photo-1595113316349-9fa4ee24f884?auto=format&fit=crop&q=80'; ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 left-4 bg-yellow-500 text-green-950 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest">
                            <?php echo htmlspecialchars($product['category']); ?>
                        </div>
                    </div>
                    
                    <div class="p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-location-dot text-green-700"></i>
                            <?php echo htmlspecialchars($product['hub_location']); ?>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-xs text-slate-400 font-medium mb-8 line-clamp-2"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="mt-auto border-t border-slate-50 pt-6 flex justify-between items-center">
                            <div>
                                <p class="text-[9px] font-black text-slate-300 uppercase mb-1">Price per unit</p>
                                <p class="text-xl font-black text-slate-900"><?php echo number_format($product['price']); ?> <span class="text-[10px] text-green-700">XAF</span></p>
                            </div>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="bg-slate-900 text-white p-4 rounded-2xl hover:bg-green-800 transition-all">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-32 text-center">
                    <div class="bg-white border border-dashed border-slate-200 rounded-[3rem] p-20 inline-block">
                        <i class="fas fa-seedling text-4xl text-slate-200 mb-4"></i>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs italic">No yields match your current search.</p>
                        <a href="marketplace.php" class="text-green-800 text-[10px] font-black uppercase mt-4 block underline">Clear all filters</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="py-12 text-center border-t border-slate-100 bg-white mt-20">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">© 2026 Agri-Tech Cameroon • Bulk Trade Network</p>
    </footer>

</body>
</html>
<?php
session_start();
require_once 'db.php';

// 1. ACCESS CONTROL: Ensure user is logged in AND is not an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// 2. IDENTITY MAPPING
$username = $_SESSION['username'];
$user_id  = $_SESSION['user_id'];
$user_hub = $_SESSION['hub'];

// 3. DATABASE INTEGRATION
try {
    // Fetch Count of Active Listings
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE farmer_id = ? AND status = 'active'");
    $stmt->execute([$user_id]);
    $my_products_count = $stmt->fetchColumn() ?: 0;

    // Fetch 5 Most Recent Product Listings
    $stmt_list = $pdo->prepare("SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt_list->execute([$user_id]);
    $my_listings = $stmt_list->fetchAll();

    // Fetch Recent Logistics/Seed Requests
    $stmt_req = $pdo->prepare("SELECT * FROM seed_requests WHERE user_id = ? ORDER BY requested_at DESC LIMIT 3");
    $stmt_req->execute([$user_id]);
    $my_requests = $stmt_req->fetchAll();

} catch (PDOException $e) {
    // Fail-safe defaults if tables aren't ready
    $my_products_count = 0;
    $my_listings = [];
    $my_requests = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .user-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .user-card:hover { transform: translateY(-5px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); }
        .sidebar-link.active { background: #0f172a; color: white; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2); }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    <aside class="w-full md:w-80 bg-white border-r border-slate-200 p-8 flex flex-col z-20">
        <div class="flex items-center gap-3 mb-12">
            <div class="bg-green-800 p-2.5 rounded-2xl shadow-lg shadow-green-900/20">
                <i class="fas fa-leaf text-white text-lg"></i>
            </div>
            <div>
                <span class="block font-black text-xl tracking-tight text-slate-900 uppercase leading-none">Agri-Tech <span class="text-yellow-500">CM</span></span>
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.3em]">Farmer Portal</span>
            </div>
        </div>

        <nav class="space-y-2 flex-1">
            <a href="user_dashboard.php" class="sidebar-link active flex items-center gap-4 px-6 py-4 rounded-2xl font-bold text-sm transition-all">
                <i class="fas fa-house-user w-5 text-yellow-500"></i> My Overview
            </a>
            <a href="marketplace.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 font-bold text-sm transition-all">
                <i class="fas fa-store w-5"></i> Live Market
            </a>
            <a href="add_product.php" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 font-bold text-sm transition-all">
                <i class="fas fa-plus-circle w-5"></i> Sell Harvest
            </a>
            <a href="" class="sidebar-link flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 hover:bg-slate-50 font-bold text-sm transition-all">
                <i class="fas fa-truck-ramp-box w-5"></i> Logistics
            </a>
        </nav>

        <div class="mt-10 p-6 bg-slate-900 rounded-[2rem] text-white overflow-hidden relative">
            <i class="fas fa-seedling absolute -right-4 -bottom-4 text-6xl text-white/5"></i>
            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Primary Hub</p>
            <p class="font-bold text-sm mb-4"><?php echo htmlspecialchars($user_hub); ?></p>
            <div class="w-full bg-slate-800 h-1 rounded-full overflow-hidden">
                <div class="bg-yellow-500 h-full w-[100%]"></div>
            </div>
            <p class="text-[9px] text-yellow-500 font-bold mt-3 uppercase">Hub Active • Ver. 2.4</p>
        </div>
    </aside>

    <main class="flex-1 p-6 md:p-12 lg:p-16 max-w-7xl">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.4em] mb-1">Welcome Back,</p>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight capitalize"><?php echo htmlspecialchars($username); ?></h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="add-product.php" class="bg-green-800 text-white px-8 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-green-900/20 hover:bg-green-700 transition-all">
                    New Listing
                </a>
                <a href="logout.php" class="w-14 h-14 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-red-500 hover:bg-red-50 hover:border-red-100 transition-all" title="Logout">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="user-card bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-6">
                    <i class="fas fa-wheat-awn text-xl"></i>
                </div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Active Listings</p>
                <h2 class="text-4xl font-black text-slate-900 leading-none"><?php echo $my_products_count; ?></h2>
            </div>

            <div class="user-card bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-600 mb-6">
                    <i class="fas fa-clock-rotate-left text-xl"></i>
                </div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Pending Orders</p>
                <h2 class="text-4xl font-black text-slate-900 leading-none">00</h2>
            </div>

            <div class="user-card bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl text-white">
                <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center text-yellow-500 mb-6">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Wallet Balance</p>
                <div class="flex items-end gap-2">
                    <h2 class="text-4xl font-black text-yellow-500 leading-none">0</h2>
                    <span class="text-slate-400 font-bold text-[10px] uppercase tracking-widest pb-1">FCFA</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-black text-slate-900 uppercase tracking-tighter text-sm">Recent Listings</h3>
                    <a href="my-products.php" class="text-[10px] font-bold text-green-700 uppercase hover:underline">View All</a>
                </div>
                <div class="divide-y divide-slate-50">
                    <?php if (empty($my_listings)): ?>
                        <div class="p-12 text-center">
                            <i class="fas fa-box-open text-slate-200 text-4xl mb-4 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No listings found</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($my_listings as $item): ?>
                        <div class="flex items-center justify-between p-6 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                    <i class="fas fa-image"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase"><?php echo number_format($item['price']); ?> FCFA</p>
                                </div>
                            </div>
                            <span class="text-[8px] font-black px-3 py-1 bg-green-50 text-green-600 rounded-full uppercase tracking-widest border border-green-100">
                                <?php echo htmlspecialchars($item['status']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50">
                    <h3 class="font-black text-slate-900 uppercase tracking-tighter text-sm">Logistics & Inputs</h3>
                </div>
                <div class="p-8">
                    <div class="relative pl-8 border-l-2 border-slate-100 space-y-10">
                        <?php if (empty($my_requests)): ?>
                             <div class="relative">
                                <div class="absolute -left-[41px] top-0 w-5 h-5 bg-slate-200 rounded-full border-4 border-white"></div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Active Requests</p>
                                <h4 class="text-sm font-bold text-slate-300 mt-1 italic">Apply for seeds or fertilizer in the Logistics tab.</h4>
                            </div>
                        <?php else: ?>
                            <?php foreach($my_requests as $req): ?>
                            <div class="relative">
                                <div class="absolute -left-[41px] top-0 w-5 h-5 bg-yellow-500 rounded-full border-4 border-white"></div>
                                <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest"><?php echo htmlspecialchars($req['status']); ?></p>
                                <h4 class="text-sm font-bold text-slate-900"><?php echo htmlspecialchars($req['input_type']); ?></h4>
                                <p class="text-[10px] text-slate-400 font-medium">Req #<?php echo $req['id']; ?> • <?php echo htmlspecialchars($user_hub); ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
<?php
session_start();
require_once 'db.php';

// Security: Lock the door. Only Admins allowed.
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$error_msg = "";
$m_status = 0;
$totalUsers = 0;
$pendingProducts = 0;
$activeProducts = 0;
$totalRevenue = 0;
$recent_logs = [];
$all_users = [];

try {
    // 1. System Health Checks
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'system_settings'")->rowCount();
    if($tableCheck > 0) {
        $m_stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
        $m_status = $m_stmt->fetchColumn();
    }

    // 2. Data Aggregation
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0;
    
    if($pdo->query("SHOW TABLES LIKE 'products'")->rowCount() > 0) {
        $pendingProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'pending'")->fetchColumn() ?: 0;
        $activeProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn() ?: 0;
    }

    if($pdo->query("SHOW TABLES LIKE 'transactions'")->rowCount() > 0) {
        $totalRevenue = $pdo->query("SELECT SUM(amount_paid) FROM transactions WHERE payment_status = 'released'")->fetchColumn() ?: 0;
    }

    // 3. Personnel & Activity Fetching
    if($pdo->query("SHOW TABLES LIKE 'audit_logs'")->rowCount() > 0) {
        $recent_logs = $pdo->query("SELECT a.*, u.username FROM audit_logs a JOIN users u ON a.admin_id = u.id ORDER BY a.created_at DESC LIMIT 5")->fetchAll();
    }
    
    $all_users = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC LIMIT 10")->fetchAll();

} catch (PDOException $e) {
    $error_msg = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HQ | Agri-Tech Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #05080f; }
        .glass-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .stat-gradient { background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 100%); }
    </style>
</head>
<body class="text-slate-200">

    <?php if($error_msg): ?>
    <div class="bg-red-500/10 border-b border-red-500/20 text-red-500 p-4 text-center text-xs font-bold uppercase tracking-widest animate-pulse">
        <i class="fas fa-server mr-2"></i> System Sync Error: <?= $error_msg ?>
    </div>
    <?php endif; ?>

    <div class="flex min-h-screen">
        <aside class="w-80 bg-[#020408] border-r border-white/5 flex flex-col p-8 fixed h-full shadow-2xl z-50">
            <div class="flex items-center gap-4 mb-14 px-2">
                <div class="h-10 w-10 bg-yellow-500 rounded-2xl flex items-center justify-center text-slate-950 shadow-[0_0_20px_rgba(234,179,8,0.3)]">
                    <i class="fas fa-leaf text-lg"></i>
                </div>
                <div>
                    <h2 class="font-black text-xl tracking-tighter text-white">COMMAND</h2>
                    <p class="text-[9px] font-bold text-yellow-500 tracking-[0.3em] uppercase">Control Center</p>
                </div>
            </div>

            <nav class="space-y-1">
                <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-4 ml-4">Main Menu</p>
                <a href="#" class="flex items-center gap-4 bg-yellow-500/10 text-yellow-500 p-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">
                    <i class="fas fa-grid-2 w-5"></i> Dashboard
                </a>
                <a href="#personnel" class="flex items-center gap-4 text-slate-400 p-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-white/5 hover:text-white transition-all">
                    <i class="fas fa-user-shield w-5 text-slate-600"></i> Personnel
                </a>
                <a href="#" class="flex items-center gap-4 text-slate-400 p-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-white/5 hover:text-white transition-all">
                    <i class="fas fa-box w-5 text-slate-600"></i> Inventory
                </a>
                <a href="admin_settings.php" class="flex items-center gap-4 text-slate-400 p-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-white/5 hover:text-white transition-all">
                    <i class="fas fa-sliders w-5 text-slate-600"></i> System Config
                </a>
            </nav>

            <div class="mt-auto pt-8 border-t border-white/5">
                <div class="bg-slate-900/50 p-4 rounded-2xl mb-6 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-yellow-500 to-orange-500"></div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-white"><?= $_SESSION['username'] ?? 'Root Admin' ?></p>
                        <p class="text-[8px] font-bold text-slate-500 uppercase">System Level 4</p>
                    </div>
                </div>
                <a href="logout.php" class="flex items-center justify-center gap-3 w-full py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-red-500 hover:bg-red-500/10 transition-all border border-red-500/10">
                    <i class="fas fa-power-off"></i> Terminate
                </a>
            </div>
        </aside>

        <main class="flex-1 ml-80 p-12 bg-[#05080f]">
            <header class="flex justify-between items-end mb-12">
                <div>
                    <h1 class="text-4xl font-black text-white tracking-tighter">Operational <span class="text-yellow-500">Intelligence</span></h1>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                        <span class="h-1.5 w-1.5 bg-green-500 rounded-full animate-pulse"></span> Systems Online / Network Stable
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <button class="glass-card px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-yellow-500/50 transition-all">
                        <i class="fas fa-download mr-2 text-yellow-500"></i> Export Report
                    </button>
                    <div class="glass-card px-6 py-3 rounded-xl flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-[8px] font-black text-slate-500 uppercase tracking-tighter">System Time</p>
                            <p class="text-xs font-black text-white"><?= date('H:i:s') ?></p>
                        </div>
                        <i class="fas fa-globe text-yellow-500 opacity-50"></i>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-4 gap-6 mb-12">
                <div class="glass-card stat-gradient p-8 rounded-[2rem] hover:scale-[1.02] transition-transform cursor-pointer">
                    <div class="h-10 w-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center mb-6">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Global Members</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= number_format($totalUsers) ?></h3>
                </div>

                <div class="glass-card stat-gradient p-8 rounded-[2rem] hover:scale-[1.02] transition-transform cursor-pointer">
                    <div class="h-10 w-10 rounded-xl bg-yellow-500/10 text-yellow-500 flex items-center justify-center mb-6 text-xl animate-pulse">
                        <i class="fas fa-bell"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase text-yellow-500 tracking-widest">Awaiting Review</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $pendingProducts ?></h3>
                </div>

                <div class="glass-card stat-gradient p-8 rounded-[2rem] hover:scale-[1.02] transition-transform cursor-pointer">
                    <div class="h-10 w-10 rounded-xl bg-green-500/10 text-green-500 flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Active Listings</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= $activeProducts ?></h3>
                </div>

                <div class="glass-card stat-gradient p-8 rounded-[2rem] hover:scale-[1.02] transition-transform cursor-pointer">
                    <div class="h-10 w-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center mb-6">
                        <i class="fas fa-vault"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Total Volume</p>
                    <h3 class="text-4xl font-black text-white mt-1"><?= number_format($totalRevenue) ?><span class="text-sm ml-1 text-slate-500 font-medium">XAF</span></h3>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-10">
                <div class="col-span-2 glass-card rounded-[2.5rem] overflow-hidden shadow-2xl" id="personnel">
                    <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
                        <div>
                            <h3 class="text-lg font-black uppercase tracking-tighter text-white">Personnel Management</h3>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Adjust access tiers</p>
                        </div>
                        <button class="bg-white text-slate-950 px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-yellow-500 transition-colors">Add Staff</button>
                    </div>
                    <div class="overflow-x-auto px-4 pb-4">
                        <table class="w-full text-left">
                            <thead class="text-[9px] font-black uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="px-6 py-6">Identity</th>
                                    <th class="px-6 py-6">Clearance</th>
                                    <th class="px-6 py-6 text-right">Operations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach($all_users as $user): ?>
                                <tr class="group hover:bg-white/[0.01] transition-all">
                                    <td class="px-6 py-6 flex items-center gap-4">
                                        <div class="h-9 w-9 rounded-xl bg-slate-800 flex items-center justify-center font-black text-xs text-yellow-500 border border-white/5">
                                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-white"><?= htmlspecialchars($user['username']) ?></p>
                                            <p class="text-[10px] font-bold text-slate-500"><?= htmlspecialchars($user['email']) ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border <?= $user['role'] == 'admin' ? 'border-yellow-500/20 bg-yellow-500/5 text-yellow-500' : 'border-slate-700 bg-slate-800/50 text-slate-400' ?> text-[9px] font-black uppercase">
                                            <span class="h-1 w-1 rounded-full bg-current"></span>
                                            <?= $user['role'] ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <form action="update_role.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <?php if($user['role'] !== 'admin'): ?>
                                                <button name="new_role" value="admin" class="text-[9px] font-black uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all text-slate-400 hover:text-white underline decoration-yellow-500">Promote</button>
                                            <?php else: ?>
                                                <button name="new_role" value="farmer" class="text-[9px] font-black uppercase tracking-widest text-red-500 hover:text-red-400">Demote</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] p-8">
                    <div class="flex items-center justify-between mb-10">
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] text-white">Audit Feed</h4>
                        <i class="fas fa-fingerprint text-slate-600 text-lg"></i>
                    </div>
                    <div class="space-y-6">
                        <?php if(empty($recent_logs)): ?>
                            <div class="text-center py-10">
                                <i class="fas fa-database text-slate-800 text-3xl mb-4"></i>
                                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">No activity recorded</p>
                            </div>
                        <?php endif; ?>
                        <?php foreach($recent_logs as $log): ?>
                        <div class="relative pl-6 border-l border-white/5 pb-2">
                            <div class="absolute -left-[5px] top-0 h-2 w-2 rounded-full bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.5)]"></div>
                            <p class="text-[10px] font-bold text-white mb-1 uppercase tracking-tight"><?= htmlspecialchars($log['action_type']) ?></p>
                            <div class="flex justify-between items-center text-[9px] font-bold text-slate-500 uppercase">
                                <span><?= htmlspecialchars($log['username']) ?></span>
                                <span><?= date('H:i', strtotime($log['created_at'])) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-10 bg-yellow-500 p-6 rounded-[2rem] text-slate-900 shadow-[0_20px_40px_rgba(234,179,8,0.15)]">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">System Status</p>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="h-3 w-3 rounded-full <?= $m_status ? 'bg-red-600' : 'bg-green-600' ?> animate-pulse"></span>
                            <h5 class="text-lg font-black uppercase tracking-tighter"><?= $m_status ? 'Maintenance' : 'Live Mode' ?></h5>
                        </div>
                        <a href="admin_settings.php" class="block w-full text-center bg-slate-950 text-white py-3 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all">Configure Hub</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
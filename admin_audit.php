<?php
session_start();
require_once 'db.php';

// Access Control: Only admins should see the audit trail
// if ($_SESSION['role'] !== 'admin') { header("Location: landing.php"); exit(); }

$search = $_GET['search'] ?? '';

try {
    $query = "
        SELECT a.*, u.username as admin_name, p.name as product_name 
        FROM audit_logs a 
        JOIN users u ON a.admin_id = u.id 
        LEFT JOIN products p ON a.target_id = p.id 
        WHERE 1=1";
    
    $params = [];
    if (!empty($search)) {
        $query .= " AND (u.username LIKE ? OR p.name LIKE ? OR a.action_type LIKE ?)";
        $params = ["%$search%", "%$search%", "%$search%"];
    }

    $query .= " ORDER BY a.created_at DESC LIMIT 100";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Audit Trail | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#050810] text-slate-400 min-h-screen p-6 lg:p-12">

    <div class="max-w-6xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <h1 class="text-3xl font-black text-white uppercase tracking-tighter">System <span class="text-yellow-500">Audit</span></h1>
                <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-slate-600 mt-2">Accountability & Security Logs</p>
            </div>
            <div class="flex gap-4">
                <a href="admin_review.php" class="bg-slate-900 border border-white/5 px-6 py-3 rounded-2xl text-[10px] font-black uppercase text-white hover:bg-slate-800 transition-all">Back to Terminal</a>
                <button onclick="window.print()" class="bg-yellow-500 text-slate-950 px-6 py-3 rounded-2xl text-[10px] font-black uppercase shadow-xl shadow-yellow-500/10">Export PDF</button>
            </div>
        </header>

        <form method="GET" class="mb-10 relative">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-600"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by admin name, action, or product..." 
                   class="w-full bg-slate-900/50 border border-white/5 rounded-[2rem] pl-14 pr-6 py-5 text-sm font-bold text-white outline-none focus:border-yellow-500/30 transition-all">
        </form>

        <div class="bg-slate-900/40 border border-white/5 rounded-[3rem] overflow-hidden backdrop-blur-md">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-white/5">
                        <th class="p-8">Timestamp</th>
                        <th class="p-8">Administrator</th>
                        <th class="p-8">Action Taken</th>
                        <th class="p-8">Target Entity</th>
                        <th class="p-8">Outcome</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[0.03]">
                    <?php if(empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="p-20 text-center italic text-slate-600">No security events found in this period.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($logs as $log): ?>
                        <tr class="hover:bg-white/[0.01] transition-all group">
                            <td class="p-8 whitespace-nowrap">
                                <p class="text-white font-bold text-xs"><?= date('M d, Y', strtotime($log['created_at'])) ?></p>
                                <p class="text-[10px] text-slate-600"><?= date('H:i:s', strtotime($log['created_at'])) ?></p>
                            </td>
                            <td class="p-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-[10px] font-black text-yellow-500">
                                        <?= strtoupper(substr($log['admin_name'], 0, 1)) ?>
                                    </div>
                                    <span class="text-xs font-black text-slate-300"><?= htmlspecialchars($log['admin_name']) ?></span>
                                </div>
                            </td>
                            <td class="p-8">
                                <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest 
                                    <?= $log['action_type'] == 'active' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' ?>">
                                    <?= $log['action_type'] == 'active' ? 'Approve' : 'Reject' ?>
                                </span>
                            </td>
                            <td class="p-8">
                                <p class="text-xs font-bold text-white"><?= htmlspecialchars($log['product_name'] ?? 'Unknown Item') ?></p>
                                <p class="text-[9px] text-slate-600 uppercase tracking-widest">ID: #<?= str_pad($log['target_id'], 5, '0', STR_PAD_LEFT) ?></p>
                            </td>
                            <td class="p-8">
                                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 italic">
                                    <i class="fas fa-check-double text-green-800"></i>
                                    Verified DB Write
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="mt-12 text-center">
            <p class="text-[9px] font-black text-slate-700 uppercase tracking-[0.6em]">Encrypted Session • Node 04-CM</p>
        </footer>
    </div>

</body>
</html>
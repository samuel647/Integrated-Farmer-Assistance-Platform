 <?php
session_start();
require_once 'db.php';

// Simple Admin Access Control (In a real app, check if user['role'] == 'manager')
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch all inquiries from the database
try {
    $stmt = $pdo->query("SELECT * FROM support_tickets ORDER BY submitted_at DESC");
    $inquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    $inquiries = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Management | Admin Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-slate-900 text-white h-20 flex items-center px-8 justify-between sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <i class="fas fa-user-shield text-yellow-500 text-xl"></i>
            <span class="font-black uppercase tracking-widest text-xs">Admin Coordination Panel</span>
        </div>
        <a href="user_dashboard.php" class="text-[10px] font-black uppercase tracking-widest bg-white/10 px-4 py-2 rounded-lg hover:bg-white/20">Exit Admin</a>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h1 class="text-4xl font-black text-slate-900">Support <span class="text-green-800">Queue</span></h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-2">Managing national inquiries and feedback</p>
            </div>
            <div class="bg-white px-6 py-4 rounded-2xl border border-slate-200 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Tickets</p>
                <p class="text-2xl font-black text-slate-900"><?php echo count($inquiries); ?></p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Date</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">User</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Subject</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Message Preview</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if(empty($inquiries)): ?>
                        <tr>
                            <td colspan="5" class="p-20 text-center text-slate-300 font-bold uppercase tracking-widest text-xs">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i> No pending inquiries
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($inquiries as $ticket): ?>
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="p-6">
                                <span class="text-[10px] font-bold text-slate-500">
                                    <?php echo date('M d, H:i', strtotime($ticket['submitted_at'])); ?>
                                </span>
                            </td>
                            <td class="p-6">
                                <p class="text-sm font-black text-slate-900"><?php echo htmlspecialchars($ticket['name']); ?></p>
                                <p class="text-[10px] text-slate-400 font-bold lowercase"><?php echo htmlspecialchars($ticket['email']); ?></p>
                            </td>
                            <td class="p-6">
                                <span class="bg-green-50 text-green-800 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border border-green-100">
                                    <?php echo htmlspecialchars($ticket['subject']); ?>
                                </span>
                            </td>
                            <td class="p-6 max-w-xs">
                                <p class="text-xs text-slate-500 truncate font-medium"><?php echo htmlspecialchars($ticket['message']); ?></p>
                            </td>
                            <td class="p-6 text-right">
                                <button class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-green-800 transition-all">
                                    Respond
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
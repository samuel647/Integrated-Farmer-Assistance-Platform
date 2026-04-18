 <?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 1;

// Fetch Transaction History
try {
    $stmt = $pdo->prepare("
        SELECT t.*, p.name as product_name, u.username as buyer_name 
        FROM transactions t 
        JOIN products p ON t.product_id = p.id 
        JOIN users u ON t.buyer_id = u.id 
        WHERE t.seller_id = ? 
        ORDER BY t.transaction_date DESC
    ");
    $stmt->execute([$user_id]);
    $sales = $stmt->fetchAll();
    
    // Calculate Total Earnings
    $total_stmt = $pdo->prepare("SELECT SUM(amount_paid) FROM transactions WHERE seller_id = ? AND payment_status = 'released'");
    $total_stmt->execute([$user_id]);
    $total_earned = $total_stmt->fetchColumn() ?? 0;

} catch (PDOException $e) {
    $sales = [];
    $total_earned = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Hub | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b border-slate-200 h-20 flex items-center px-6 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto w-full flex justify-between items-center">
            <a href="user_dashboard.php" class="flex items-center gap-2 text-[10px] font-black text-green-800 uppercase tracking-widest">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <span class="font-black text-slate-900 uppercase text-[10px] tracking-[0.3em]">Financial Management</span>
            <div class="w-10"></div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-2xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Total Released</p>
                <h2 class="text-3xl font-black text-yellow-500"><?php echo number_format($total_earned); ?> <span class="text-sm">XAF</span></h2>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">In Escrow</p>
                <h2 class="text-3xl font-black text-slate-900">45,000 <span class="text-sm">XAF</span></h2>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Trust Rating</p>
                <div class="flex items-center gap-2">
                    <h2 class="text-3xl font-black text-green-800">4.9</h2>
                    <div class="text-yellow-500 text-xs">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">Recent Sales History</h3>
        
        <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Product</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Buyer</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400">Amount</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($sales as $sale): ?>
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="p-6 font-bold text-slate-900"><?php echo htmlspecialchars($sale['product_name']); ?></td>
                        <td class="p-6 text-sm text-slate-500 font-medium">@<?php echo htmlspecialchars($sale['buyer_name']); ?></td>
                        <td class="p-6 font-black text-slate-900"><?php echo number_format($sale['amount_paid']); ?> XAF</td>
                        <td class="p-6 text-right">
                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest 
                                <?php echo $sale['payment_status'] == 'released' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                <?php echo $sale['payment_status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
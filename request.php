 <?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item = filter_var($_POST['item_requested'], FILTER_SANITIZE_STRING);
    $qty = filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT);
    $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
    $user_id = $_SESSION['user_id'] ?? 1;

    if (!empty($item) && !empty($qty)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO input_requests (user_id, item_requested, quantity, location) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $item, $qty, $location]);
            $message = "Your request for $item has been submitted to the regional hub.";
        } catch (PDOException $e) {
            $error = "Submission failed.";
        }
    }
}

// Fetch existing requests for this user
// $stmt = $pdo->prepare("SELECT * FROM input_requests WHERE user_id = ? ORDER BY request_date DESC");
// $stmt->execute([$_SESSION['user_id']]);
// $my_requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Logistics | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .step-active { border-color: #166534; color: #166534; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen pb-20">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="user_dashboard.php" class="flex items-center gap-2 text-xs font-black text-green-800 uppercase tracking-widest hover:translate-x-[-5px] transition-all">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <span class="font-black text-slate-900 uppercase text-[10px] tracking-widest">Supply Logistics</span>
            <div class="w-10"></div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 mb-2">Request <span class="text-green-800">Resources</span></h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em]">Order seeds, fertilizers, and equipment</p>
        </div>

        <?php if($message): ?>
            <div class="bg-green-800 text-white p-6 rounded-[2rem] mb-10 flex items-center gap-4 shadow-xl shadow-green-800/20">
                <i class="fas fa-truck-fast text-yellow-500 text-xl"></i>
                <p class="text-[11px] font-black uppercase tracking-widest"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <div class="lg:col-span-1">
                <form action="requests.php" method="POST" class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Select Resource</label>
                        <select name="item_requested" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-green-800 transition-all">
                            <option>NPK 15-15-15 Fertilizer</option>
                            <option>Urea Fertilizer</option>
                            <option>Hybrid Maize Seeds</option>
                            <option>Organic Compost</option>
                            <option>Hand Tools Set</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Quantity (Units/Bags)</label>
                        <input type="number" name="quantity" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-green-800 transition-all" placeholder="e.g. 10">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Delivery Location</label>
                        <input type="text" name="location" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold outline-none focus:border-green-800 transition-all" placeholder="e.g. Buea Town Hub">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-green-800 transition-all">
                        Submit Request
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">Active Logistics Tracking</h3>
                
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h4 class="text-lg font-black text-slate-900">NPK Fertilizer (5 Bags)</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Order ID: #LOG-88291</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest">In Transit</span>
                    </div>

                    <div class="relative flex justify-between">
                        <div class="absolute top-4 left-0 w-full h-0.5 bg-slate-100 -z-0"></div>
                        <div class="z-10 bg-white border-2 border-green-800 w-8 h-8 rounded-full flex items-center justify-center text-green-800">
                            <i class="fas fa-check text-[10px]"></i>
                        </div>
                        <div class="z-10 bg-white border-2 border-green-800 w-8 h-8 rounded-full flex items-center justify-center text-green-800">
                            <i class="fas fa-truck text-[10px]"></i>
                        </div>
                        <div class="z-10 bg-white border-2 border-slate-200 w-8 h-8 rounded-full flex items-center justify-center text-slate-300">
                            <i class="fas fa-house-chimney text-[10px]"></i>
                        </div>
                    </div>
                    <div class="flex justify-between mt-4 text-[9px] font-black uppercase tracking-widest text-slate-400">
                        <span>Confirmed</span>
                        <span class="text-green-800">Shipping</span>
                        <span>Arrival</span>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm opacity-60">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-black text-slate-900">Hybrid Maize Seeds</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Delivered Feb 22, 2026</p>
                        </div>
                        <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest">Complete</span>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
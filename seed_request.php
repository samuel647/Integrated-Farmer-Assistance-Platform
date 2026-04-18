 <?php
session_start();
require_once 'db.php';

// 1. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$user_hub = $_SESSION['hub'];
$success = "";
$error = "";

// 2. PROCESS REQUEST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_type = $_POST['input_type'];
    $quantity = (int)$_POST['quantity'];
    $farm_size = htmlspecialchars($_POST['farm_size']);
    $notes = htmlspecialchars($_POST['notes']);

    if (!empty($input_type) && $quantity > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO seed_requests (user_id, input_type, quantity, farm_size, notes, status, hub) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
            if ($stmt->execute([$user_id, $input_type, $quantity, $farm_size, $notes, $user_hub])) {
                $success = "Application submitted to the $user_hub Hub for verification.";
            }
        } catch (PDOException $e) {
            $error = "Submission failed: " . $e->getMessage();
        }
    } else {
        $error = "Please specify input type and quantity.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Inputs | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfdfa; }
        .text-gradient { background: linear-gradient(to right, #8b5e34, #634832); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .form-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.8); }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 25s linear infinite; }
    </style>
</head>
<body class="text-slate-900">

    <div class="bg-[#064e3b] text-white py-3 overflow-hidden whitespace-nowrap">
        <div class="inline-block animate-marquee uppercase text-[10px] font-black tracking-[0.2em]">
            <span class="mx-12"><i class="fas fa-truck-fast mr-3 text-yellow-500"></i> NEXT DISTRIBUTION: 24th OCT</span>
            <span class="mx-12"><i class="fas fa-seedling mr-3 text-green-400"></i> COCOA F1 STOCK: STABLE</span>
            <span class="mx-12"><i class="fas fa-circle text-[6px] mr-3 text-yellow-500"></i> HUB: <?= strtoupper($user_hub) ?> ACTIVE</span>
        </div>
    </div>

    <nav class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="bg-[#064e3b] p-3 rounded-2xl"><i class="fas fa-leaf text-white text-xl"></i></div>
            <a href="landing.php" class="block font-extrabold text-2xl tracking-tighter uppercase leading-none text-slate-900">Agri-Tech <span class="text-[#8b5e34]">CM</span></a>
        </div>
        <a href="user_dashboard.php" class="text-[11px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> My Dashboard
        </a>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid lg:grid-cols-5 gap-16">
            
            <div class="lg:col-span-2">
                <span class="bg-[#8b5e34]/10 text-[#8b5e34] px-4 py-2 rounded-full font-black text-[10px] uppercase tracking-widest">Input Support Program</span>
                <h1 class="text-5xl font-black text-slate-900 tracking-tighter mt-6 mb-8 leading-tight">Apply for <br><span class="text-gradient">Certified</span> Inputs.</h1>
                <p class="text-slate-500 leading-relaxed mb-8">
                    Submit your request for high-yield F1 Hybrid seeds and fertilizers. Requests are processed by your regional hub manager and assigned based on farm size and historical yield data.
                </p>

                <div class="space-y-6">
                    <div class="flex gap-4 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-700 shrink-0">
                            <i class="fas fa-shield-halved text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Quality Guaranteed</h4>
                            <p class="text-xs text-slate-400 mt-1">All seeds are certified by the National Agricultural Research Institute.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-700 shrink-0">
                            <i class="fas fa-map-location-dot text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Hub Pickup</h4>
                            <p class="text-xs text-slate-400 mt-1">Once approved, collect your items at the <strong><?= $user_hub ?> Hub</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <?php if($success): ?>
                    <div class="bg-green-800 text-white p-8 rounded-[2.5rem] mb-10 shadow-2xl shadow-green-900/20">
                        <i class="fas fa-check-circle text-3xl mb-4 text-green-300"></i>
                        <h3 class="text-xl font-bold italic mb-2">Request Received!</h3>
                        <p class="text-green-100 text-sm opacity-90"><?= $success ?></p>
                    </div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="bg-red-50 border border-red-100 text-red-700 p-6 rounded-3xl mb-10 text-sm font-bold uppercase tracking-widest">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="seed_request.php" method="POST" class="form-card p-10 md:p-14 rounded-[4rem] shadow-xl">
                    <div class="grid md:grid-cols-2 gap-8 mb-10">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Input Category</label>
                            <select name="input_type" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none focus:border-[#8b5e34] transition-all">
                                <option value="Cocoa F1 Hybrid Seeds">Cocoa F1 Hybrid Seeds</option>
                                <option value="Arabica Coffee Seedlings">Arabica Coffee Seedlings</option>
                                <option value="NPK 20-10-10 Fertilizer">NPK 20-10-10 Fertilizer</option>
                                <option value="Organic Compost">Organic Compost</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Quantity Required</label>
                            <input type="number" name="quantity" required placeholder="e.g. 50" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none focus:border-[#8b5e34] transition-all">
                        </div>
                    </div>

                    <div class="space-y-2 mb-10">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Farm Size (Hectares)</label>
                        <input type="text" name="farm_size" required placeholder="e.g. 2.5 Hectares" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none focus:border-[#8b5e34] transition-all">
                    </div>

                    <div class="space-y-2 mb-10">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Additional Notes</label>
                        <textarea name="notes" rows="4" placeholder="Mention previous yield or specific soil needs..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none focus:border-[#8b5e34] transition-all"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#064e3b] text-white py-6 rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-2xl hover:bg-slate-900 transition-all">
                        Submit Official Request
                    </button>
                    
                    <p class="text-center text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-6">
                        Verified National Request • Secure ID: <?= $user_id ?>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-slate-950 text-white py-12 px-6 mt-24 text-center">
        <p class="text-[10px] font-bold tracking-[0.6em] text-slate-500 uppercase">Agri-Tech CM • Secure Input Distribution Network</p>
    </footer>

</body>
</html>
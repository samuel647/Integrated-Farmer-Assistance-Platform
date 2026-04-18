<?php 
session_start();
require_once 'db.php'; 

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? $_SESSION['username'] : "";
$user_role = $is_logged_in ? $_SESSION['role'] : "";
$user_hub = $is_logged_in ? $_SESSION['hub'] : "";

try {
    // System Stats for the Impact Section
    $total_listings = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn() ?: "450+";
    $total_farmers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'farmer'")->fetchColumn() ?: "1,200";

    $stmtProducts = $pdo->query("
        SELECT p.*, u.hub_location 
        FROM products p 
        JOIN users u ON p.farmer_id = u.id 
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC LIMIT 3
    ");
    $recent_products = $stmtProducts->fetchAll();
} catch (Exception $e) {
    $recent_products = [];
    $total_listings = "150+";
    $total_farmers = "800+";
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agri-Tech CM | National Digital Agriculture OS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfdfa; }
        .text-gradient { background: linear-gradient(to right, #064e3b, #166534); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-img-container { border-radius: 40px 150px 40px 40px; overflow: hidden; box-shadow: 0 50px 100px -20px rgba(6, 78, 59, 0.3); }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 25s linear infinite; }
        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.5); }
    </style>
</head>
<body class="text-slate-900">

    <div class="bg-[#064e3b] text-white py-3 overflow-hidden whitespace-nowrap">
        <div class="inline-block animate-marquee uppercase text-[10px] font-black tracking-[0.2em]">
            <span class="mx-12"><i class="fas fa-circle text-[6px] mr-3 text-yellow-500"></i> COCOA: 2,640 XAF/KG <span class="text-green-400">+2.4%</span></span>
            <span class="mx-12"><i class="fas fa-circle text-[6px] mr-3 text-yellow-500"></i> COFFEE: 1,820 XAF/KG <span class="text-red-400">-0.8%</span></span>
            <span class="mx-12"><i class="fas fa-truck-fast mr-3"></i> HUB STATUS: MAMFE ACTIVE</span>
            <span class="mx-12"><i class="fas fa-leaf mr-3"></i> SEED DISTRIBUTION: 85% COMPLETE</span>
        </div>
    </div>

    <nav class="sticky top-0 w-full z-50 bg-white/90 backdrop-blur-xl border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-[#064e3b] p-3 rounded-2xl"><i class="fas fa-leaf text-white text-xl"></i></div>
                <div>
                    <span class="block font-extrabold text-2xl tracking-tighter uppercase leading-none">Agri-Tech <span class="text-[#8b5e34]">CM</span></span>
                    <span class="text-[8px] font-black uppercase tracking-[0.4em] text-slate-400">National OS</span>
                </div>
            </div>
            
            <div class="hidden lg:flex items-center gap-10 font-bold text-[11px] uppercase tracking-widest text-slate-500">
                <a href="#impact" class="hover:text-[#064e3b]">Impact</a>
                <a href="seed_request.php" class="hover:text-[#064e3b]">Request Seeds</a>
                <a href="marketplace.php" class="hover:text-[#064e3b]">Live Trades</a>
                <a href="#contact" class="hover:text-[#064e3b]">Contact</a>
                
                <?php if($is_logged_in): ?>
                    <div class="flex items-center gap-4">
                        <a href="<?= ($user_role === 'admin') ? 'admin_dashboard.php' : 'landing.php'; ?>" class="bg-green-800 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-green-700 transition-all uppercase">
                            Dashboard
                        </a>
                        <a href="logout.php" class="text-red-600 hover:text-red-800 transition-all uppercase">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="bg-slate-900 text-white px-8 py-4 rounded-xl shadow-xl hover:scale-105 transition-all">
                        Join Initiative
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="relative pt-20 pb-32 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative z-10">
                <?php if($is_logged_in): ?>
                    <div class="inline-flex items-center gap-3 bg-green-50 px-5 py-3 rounded-2xl mb-8 border border-green-100">
                        <div class="w-10 h-10 rounded-full bg-green-800 flex items-center justify-center text-white font-black text-xs">
                            <?= strtoupper(substr($user_name, 0, 2)) ?>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-green-800 tracking-widest">Active Session</p>
                            <p class="text-sm font-bold text-slate-900">Welcome, <?= $user_name ?> <span class="text-slate-400 mx-2">•</span> Hub: <?= $user_hub ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="inline-flex items-center gap-2 bg-green-50 px-4 py-2 rounded-full mb-6 text-[10px] font-black uppercase tracking-widest text-green-800">
                        <span class="w-2 h-2 bg-green-600 rounded-full animate-ping"></span>
                        Verified National Infrastructure
                    </div>
                <?php endif; ?>

                <h1 class="text-6xl lg:text-[5.5rem] font-black text-slate-900 leading-[0.9] tracking-tighter mb-8">
                    Modernizing <br><span class="text-gradient italic">Cameroon's</span> <br>Agriculture.
                </h1>
                <p class="text-xl text-slate-500 max-w-lg leading-relaxed mb-10">
                    An end-to-end Operating System connecting farmers to global exporters via secure regional hubs.
                </p>
                
                <div class="flex gap-4">
                    <a href="seed_request.php" class="bg-[#064e3b] text-white px-10 py-5 rounded-2xl font-black uppercase text-[12px] tracking-widest shadow-2xl hover:bg-green-800 transition-all">Request Seeds</a>
                    <a href="marketplace.php" class="bg-white border border-slate-200 px-10 py-5 rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-slate-50 transition-all">Trade Now</a>
                </div>
            </div>
            
            <div class="hero-img-container">
                <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=1000" class="w-full h-[600px] object-cover" alt="Agri-Tech Infrastructure">
            </div>
        </div>
    </header>

    <section id="impact" class="py-24 bg-[#064e3b] text-white rounded-[5rem] mx-4">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-12 text-center">
            <div>
                <p class="text-5xl font-black mb-2"><?= $total_farmers ?></p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Active Farmers</p>
            </div>
            <div>
                <p class="text-5xl font-black mb-2">4</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Regional Hubs</p>
            </div>
            <div>
                <p class="text-5xl font-black mb-2">40%</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Middlemen Reduction</p>
            </div>
            <div>
                <p class="text-5xl font-black mb-2">24h</p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-green-300">Payment Settlements</p>
            </div>
        </div>
    </section>

    <section id="seeds" class="py-32 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
            <div class="flex-1">
                <span class="text-[#8b5e34] font-black text-[10px] uppercase tracking-[0.5em]">Input Distribution</span>
                <h2 class="text-5xl font-black text-slate-900 tracking-tighter mt-4 mb-8">Certified Seeds <br>& Fertilizers.</h2>
                <p class="text-slate-500 text-lg mb-8 leading-relaxed">
                    Verified farmers can request high-yield hybrid cocoa seeds and organic fertilizers directly through the OS.
                </p>
                <ul class="space-y-4 mb-10">
                    <li class="flex items-center gap-3 font-bold text-sm"><i class="fas fa-check-circle text-green-600"></i> F1 Hybrid Cocoa Pods</li>
                    <li class="flex items-center gap-3 font-bold text-sm"><i class="fas fa-check-circle text-green-600"></i> NPK National Reserve Fertilizer</li>
                    <li class="flex items-center gap-3 font-bold text-sm"><i class="fas fa-check-circle text-green-600"></i> Satellite Soil Mapping Analysis</li>
                </ul>
                <a href="seed_request.php" class="inline-block bg-[#8b5e34] text-white px-10 py-5 rounded-2xl font-black uppercase text-[12px] tracking-widest shadow-xl hover:opacity-90 transition-all">Start Seed Request</a>
            </div>
            
            <div class="flex-1 grid grid-cols-2 gap-4">
                <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl">
                    <i class="fas fa-seedling text-4xl text-green-600 mb-6"></i>
                    <h4 class="font-black text-lg">Cocoa F1</h4>
                    <p class="text-xs text-slate-400 mt-2">Available at Mamfe Hub</p>
                </div>
                <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl mt-12">
                    <i class="fas fa-flask text-4xl text-yellow-600 mb-6"></i>
                    <h4 class="font-black text-lg">Soil Care</h4>
                    <p class="text-xs text-slate-400 mt-2">National Reserve</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-32 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-16">
                <div class="lg:w-1/3">
                    <span class="text-green-800 font-black text-[10px] uppercase tracking-[0.5em]">Network Support</span>
                    <h2 class="text-5xl font-black text-slate-900 tracking-tighter mt-4 mb-6">Get in <span class="italic text-green-800">Touch</span>.</h2>
                    <p class="text-slate-500 leading-relaxed mb-8">Have questions about seed distribution or hub logistics? Our technical team is available 24/7.</p>
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-[#064e3b]">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400">Hotline</p>
                                <p class="font-bold">+237 651 485 065</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-[#064e3b]">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400">Official Email</p>
                                <p class="font-bold">achalesamuel9@gmail.com</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-[#064e3b]">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400">Headquarters</p>
                                <p class="font-bold">Main St, Mamfe Regional Hub</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-2/3 bg-slate-50 p-12 rounded-[4rem] border border-slate-100">
                    <?php if(isset($_GET['contact'])): ?>
                        <?php if($_GET['contact'] == 'success'): ?>
                            <div class="mb-8 p-4 bg-green-100 border border-green-200 text-green-800 rounded-2xl font-bold text-sm">
                                <i class="fas fa-check-circle mr-2"></i> Inquiry transmitted successfully. Our team will contact you shortly.
                            </div>
                        <?php elseif($_GET['contact'] == 'error'): ?>
                            <div class="mb-8 p-4 bg-red-100 border border-red-200 text-red-800 rounded-2xl font-bold text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Systems error. Please try again later.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="contact_process.php" method="POST" class="grid md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2">Full Name</label>
                            <input type="text" name="full_name" required class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 outline-none focus:border-green-800 font-bold text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2">Phone Number</label>
                            <input type="tel" name="phone" required class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 outline-none focus:border-green-800 font-bold text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2">Email Address</label>
                            <input type="email" name="email" required class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 outline-none focus:border-green-800 font-bold text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2">Subject</label>
                            <input type="text" name="subject" required class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 outline-none focus:border-green-800 font-bold text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-2">Message</label>
                            <textarea name="message" rows="4" required class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 outline-none focus:border-green-800 font-bold text-sm"></textarea>
                        </div>
                        <button type="submit" class="md:col-span-2 bg-slate-900 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-[0.3em] shadow-xl hover:bg-green-800 transition-all">Transmit Inquiry</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-950 text-white pt-24 pb-12 px-6 rounded-t-[5rem]">
        <div class="max-w-7xl mx-auto flex flex-col items-center text-center">
            <h2 class="text-7xl font-black tracking-tighter leading-none mb-10 uppercase italic">Digitizing <br><span class="text-[#8b5e34]">The Harvest.</span></h2>
            <p class="max-w-lg text-slate-400 mb-12">Powered by Silicon Tech.</p>
            <p class="text-[10px] font-bold tracking-[0.6em] text-slate-500 uppercase">© 2026 Agri-Tech CM • Secure Agrarian Network</p>
        </div>
    </footer>

</body>
</html>
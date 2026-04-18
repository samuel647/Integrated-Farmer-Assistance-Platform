<?php
session_start();
require_once 'db.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $msg = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    if (!empty($name) && !empty($email) && !empty($msg)) {
        try {
            // Logic: Save to the 'support_tickets' table we created
            $stmt = $pdo->prepare("INSERT INTO support_tickets (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $msg]);
            
            $message = "Inquiry Sent! A hub coordinator will review your request shortly.";
        } catch (PDOException $e) {
            $error = "System error: Could not save your inquiry. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .contact-card:hover { transform: translateY(-10px); transition: all 0.4s ease; }
        .input-focus:focus { border-color: #166534; box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.05); }
    </style>
</head>
<body class="bg-slate-50">

    <nav class="bg-white border-b border-slate-200 h-20 flex items-center px-6 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto w-full flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3">
                <div class="bg-green-800 p-2 rounded-xl">
                    <i class="fas fa-leaf text-white"></i>
                </div>
                <span class="font-black text-slate-900 uppercase text-lg tracking-tighter">Agri-Tech <span class="text-yellow-500">CM</span></span>
            </a>
            <a href="user_dashboard.php" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-green-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Back to Hub
            </a>
        </div>
    </nav>

    <header class="bg-white py-20 px-6 border-b border-slate-100">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-black text-slate-900 mb-4 tracking-tight">How can we <span class="text-green-800">Help?</span></h1>
            <p class="text-slate-400 text-sm font-bold uppercase tracking-[0.3em]">Direct Support for Cameroon's Farmers</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 -mt-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="contact-card bg-green-800 p-10 rounded-[3rem] text-white shadow-2xl">
                <i class="fas fa-headset text-3xl text-yellow-500 mb-6"></i>
                <h3 class="text-xl font-black mb-2">Technical Support</h3>
                <p class="text-green-200 text-xs font-medium leading-relaxed mb-8">System login issues or marketplace errors?</p>
                <p class="text-sm font-bold tracking-widest uppercase italic">support@agritech.cm</p>
            </div>

            <div class="contact-card bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm">
                <i class="fas fa-truck-fast text-3xl text-green-800 mb-6"></i>
                <h3 class="text-xl font-black text-slate-900 mb-2">Logistics Desk</h3>
                <p class="text-slate-400 text-xs font-medium leading-relaxed mb-8">Track your fertilizer and seed deliveries.</p>
                <p class="text-sm font-bold text-green-800 tracking-widest uppercase italic">+237 6XX XXX XXX</p>
            </div>

            <div class="contact-card bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm">
                <i class="fas fa-map-location-dot text-3xl text-green-800 mb-6"></i>
                <h3 class="text-xl font-black text-slate-900 mb-2">Regional Hubs</h3>
                <p class="text-slate-400 text-xs font-medium leading-relaxed mb-8">Douala • Yaoundé • Bamenda • Buea</p>
                <p class="text-sm font-bold text-green-800 tracking-widest uppercase italic text-[10px]">Active National Network</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 py-24">
            <div>
                <h2 class="text-3xl font-black text-slate-900 mb-8 tracking-tight">Send an <span class="text-green-800">Inquiry</span></h2>
                
                <?php if($message): ?>
                    <div class="bg-green-800 text-white p-6 rounded-2xl mb-8 flex items-center gap-4 animate-bounce">
                        <i class="fas fa-check-circle text-yellow-500"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest"><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="bg-red-500 text-white p-6 rounded-2xl mb-8 flex items-center gap-4">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest"><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Full Name</label>
                            <input type="text" name="name" required placeholder="e.g. John Ndi" 
                                   class="input-focus w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Email Address</label>
                            <input type="email" name="email" required placeholder="name@example.com" 
                                   class="input-focus w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none transition-all">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Subject</label>
                        <input type="text" name="subject" required placeholder="How can we help?" 
                               class="input-focus w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Your Message</label>
                        <textarea name="message" rows="6" required placeholder="Type your message here..." 
                                  class="input-focus w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold outline-none transition-all"></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-2xl font-black uppercase tracking-[0.3em] text-[11px] hover:bg-green-800 transition-all shadow-xl shadow-slate-900/10 active:scale-95">
                        Submit Support Inquiry
                    </button>
                </form>
            </div>

            <div class="space-y-12">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Common <span class="text-green-800">Questions</span></h2>
                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-3">How do I verify my farm?</h4>
                        <p class="text-slate-400 text-xs leading-relaxed font-medium">Verification is handled at your local regional hub. Bring your ID and land documentation to the nearest Agri-Tech coordinator.</p>
                    </div>
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-3">Is there a fee for listing?</h4>
                        <p class="text-slate-400 text-xs leading-relaxed font-medium">Listing is free for all registered Cameroonian farmers. A small 2% commission is only applied upon a successful bulk sale.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-100 py-12 text-center">
        <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.5em]">© 2026 Agri-Tech CM • Digital Support Network</p>
    </footer>

</body>
</html>
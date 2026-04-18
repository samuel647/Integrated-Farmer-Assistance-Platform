 <?php
session_start();
require_once 'db.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!empty($email)) {
        // DATABASE LOGIC:
        // 1. Check if email exists in 'users' table.
        // 2. Generate token: bin2hex(random_bytes(32))
        // 3. Insert into 'password_resets' table.
        // 4. Send email with link: reset-password.php?token=...
        
        // Mock success for your preview
        $message = "Recovery instructions have been sent to your registered email.";
    } else {
        $error = "Please enter a valid professional email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Access | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .hero-bg {
            background: linear-gradient(rgba(10, 45, 20, 0.94), rgba(10, 45, 20, 0.9)), 
                        url('https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&q=80');
            background-size: cover; background-position: center;
        }

        .input-focus:focus {
            border-color: #166534;
            box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-0 md:p-6">

    <div class="w-full max-w-6xl bg-white shadow-2xl md:rounded-[3rem] overflow-hidden flex flex-col md:flex-row min-h-screen md:min-h-[700px]">
        
        <div class="hidden md:flex md:w-5/12 hero-bg p-16 flex-col justify-between text-white border-r border-white/10">
            <div>
                <div class="flex items-center gap-3 mb-16">
                    <div class="bg-green-800 p-2 rounded-xl">
                        <i class="fas fa-leaf text-white text-xl"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tighter uppercase">Agri-Tech <span class="text-yellow-500">CM</span></span>
                </div>
                <h2 class="text-5xl font-extrabold leading-tight mb-8 text-yellow-500">Restore <br><span class="text-white italic font-medium">Access.</span></h2>
                <p class="text-lg text-gray-300 font-light leading-relaxed">
                    Security is our priority. Please provide your registered email to receive a secure recovery token.
                </p>
            </div>
            
            <div class="bg-yellow-500/10 border border-yellow-500/20 p-6 rounded-2xl">
                <p class="text-[10px] text-yellow-500 font-black uppercase tracking-widest mb-1">Help Desk</p>
                <p class="text-xs text-gray-400 font-bold leading-relaxed italic">
                    "Trade never sleeps. We'll get you back into the marketplace in minutes."
                </p>
            </div>
        </div>

        <div class="w-full md:w-7/12 p-8 md:p-20 flex flex-col justify-center bg-white">
            <div class="mb-12">
                <a href="login.php" class="inline-flex items-center gap-2 text-xs font-black text-green-800 uppercase tracking-widest mb-8 hover:translate-x-[-4px] transition-transform">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
                <h1 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">Forgot Password?</h1>
                <p class="text-slate-500 font-medium text-xs uppercase tracking-[0.2em]">Enter your account email below</p>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-600 p-5 mb-8 rounded-r-2xl">
                    <p class="text-red-700 text-[10px] font-black uppercase tracking-widest"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <?php if($message): ?>
                <div class="bg-green-50 border-l-4 border-green-600 p-5 mb-8 rounded-r-2xl">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-paper-plane text-green-600"></i>
                        <p class="text-green-700 text-[10px] font-black uppercase tracking-widest"><?php echo $message; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="forgot-password.php" method="POST" class="space-y-8">
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Professional Email</label>
                    <div class="relative">
                        <i class="far fa-envelope absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="email" name="email" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl px-16 py-6 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="e.g. manager@hub.cm">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full bg-green-800 text-white py-6 rounded-2xl font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl hover:bg-green-700 transition-all active:scale-[0.98]">
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="mt-16 text-center">
                <p class="text-[9px] text-slate-300 font-black uppercase tracking-[0.5em]">© 2026 Agri-Tech CM • Secure Recovery Protocol</p>
            </div>
        </div>
    </div>

</body>
</html>
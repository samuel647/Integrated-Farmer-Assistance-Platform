<?php
session_start();
require_once 'db.php';

// 1. SESSION GATEWAY: If already logged in, skip the login page entirely
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}

$error = "";
$success_msg = "";

// 2. Check for registration success message from signup.php
if (isset($_GET['registration']) && $_GET['registration'] == 'success') {
    $success_msg = "Account created successfully. You can now login.";
}

// 3. Process Login Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_identity = htmlspecialchars(trim($_POST['login_identity']));
    $password = $_POST['password'];

    if (!empty($login_identity) && !empty($password)) {
        try {
            // Find the user by username OR email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$login_identity, $login_identity]);
            $user = $stmt->fetch();

            // Verify password using BCRYPT
            if ($user && password_verify($password, $user['password'])) {
                
                // Security: Prevent session fixation
                session_regenerate_id(true);

                // Set session data
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role']; 
                $_SESSION['hub']      = $user['hub_location'];

                // 4. UPDATED REDIRECTION: Send farmers/traders to user_dashboard.php
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
                
            } else {
                $error = "Access denied. Invalid identity or password.";
            }
        } catch (PDOException $e) {
            $error = "System error. Please contact technical support.";
        }
    } else {
        $error = "Please provide your credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .hero-bg {
            background: linear-gradient(rgba(10, 45, 20, 0.9), rgba(10, 45, 20, 0.85)), 
                        url('https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80');
            background-size: cover;
            background-position: center;
        }

        .input-focus:focus {
            border-color: #166534;
            box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.05);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-0 md:p-6">

    <div class="w-full max-w-6xl bg-white shadow-2xl md:rounded-[3rem] overflow-hidden flex flex-col md:flex-row min-h-screen md:min-h-[750px]">
        
        <div class="hidden md:flex md:w-1/2 hero-bg p-16 flex-col justify-between text-white border-r border-white/10">
            <div>
                <div class="flex items-center gap-3 mb-16">
                    <div class="bg-green-800 p-2 rounded-xl border border-green-700/50">
                        <i class="fas fa-leaf text-white text-xl"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tighter uppercase">Agri-Tech <span class="text-yellow-500">CM</span></span>
                </div>
                <h2 class="text-5xl font-extrabold leading-tight mb-8">Empowering <br><span class="text-yellow-500 italic font-medium">National Trade.</span></h2>
                <p class="text-lg text-gray-200 font-light leading-relaxed max-w-md">
                    Access the secure gateway to manage your agricultural yields, regional logistics, and verified trade hub operations.
                </p>
            </div>
            
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center border border-yellow-500/30">
                        <i class="fas fa-lock text-yellow-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-yellow-500">Secure Authentication</p>
                        <p class="text-xs text-gray-400">Industry-standard BCRYPT protection applied</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-20 flex flex-col justify-center bg-white">
            
            <div class="mb-12">
                <h1 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">System Login</h1>
                <p class="text-slate-500 font-medium text-xs uppercase tracking-[0.2em]">Identify yourself to continue</p>
            </div>

            <?php if($success_msg): ?>
                <div class="bg-green-50 border-l-4 border-green-600 p-5 mb-8 rounded-r-2xl">
                    <p class="text-green-700 text-[10px] font-black uppercase tracking-widest"><?php echo $success_msg; ?></p>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-600 p-5 mb-8 rounded-r-2xl animate-pulse">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-circle-exclamation text-red-600"></i>
                        <p class="text-red-700 text-[10px] font-black uppercase tracking-widest"><?php echo $error; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-8">
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Username or Email</label>
                    <div class="relative">
                        <i class="far fa-user absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="login_identity" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl pl-14 pr-6 py-5 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="Enter your credentials">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">Security Key</label>
                        <a href="#" class="text-[10px] font-black text-green-800 uppercase tracking-widest hover:text-yellow-600 transition-all">Forgot?</a>
                    </div>
                    <div class="relative">
                        <i class="fas fa-shield-halved absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" id="password_input" name="password" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl pl-14 pr-16 py-5 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 hover:text-green-800 transition-colors">
                            <i id="toggle_icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full bg-green-800 text-white py-6 rounded-2xl font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl hover:bg-green-700 transition-all active:scale-[0.98]">
                        Authenticate Access
                    </button>
                </div>
            </form>

            <div class="mt-16 pt-10 border-t border-slate-50 flex flex-col gap-6 text-center">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    New Hub? <a href="signup.php" class="text-green-800 font-black ml-2 hover:underline decoration-yellow-500 underline-offset-4">Register Now</a>
                </p>
                <p class="text-[10px] text-slate-300 font-black uppercase tracking-[0.5em]">©️ 2026 Agri-Tech Solutions</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password_input');
            const icon = document.getElementById('toggle_icon');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $hub_location = $_POST['hub_location']; 

    if (!empty($username) && !empty($email) && !empty($password) && !empty($hub_location)) {
        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            try {
                // 1. Securely hash the password (Modern Standard)
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // 2. Insert into the 'users' table
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, hub_location, role) VALUES (?, ?, ?, ?, 'user')");
                
                if ($stmt->execute([$username, $email, $hashed_password, $hub_location])) {
                    // Redirect to login with success flag
                    header("Location: login.php?registration=success");
                    exit();
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry error
                    $error = "Username or Email already exists in the system.";
                } else {
                    $error = "Database Error: " . $e->getMessage();
                }
            }
        }
    } else {
        $error = "Please complete all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Hub | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .hero-bg {
            background: linear-gradient(rgba(10, 45, 20, 0.92), rgba(10, 45, 20, 0.88)), 
                        url('https://images.unsplash.com/photo-1595113316349-9fa4ee24f884?auto=format&fit=crop&q=80');
            background-size: cover; background-position: center;
        }

        .input-focus:focus {
            border-color: #166534;
            box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.05);
        }
        
        .match-success { border-color: #166534 !important; background-color: #f0fdf4 !important; }
        .match-error { border-color: #dc2626 !important; background-color: #fef2f2 !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-0 md:p-6">

    <div class="w-full max-w-6xl bg-white shadow-2xl md:rounded-[3rem] overflow-hidden flex flex-col md:flex-row min-h-screen md:min-h-[850px]">
        
        <div class="hidden md:flex md:w-5/12 hero-bg p-16 flex-col justify-between text-white border-r border-white/10">
            <div>
                <div class="flex items-center gap-3 mb-16">
                    <div class="bg-green-800 p-2 rounded-xl border border-green-700/50">
                        <i class="fas fa-leaf text-white text-xl"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tighter uppercase">Agri-Tech <span class="text-yellow-500">CM</span></span>
                </div>
                <h2 class="text-5xl font-extrabold leading-tight mb-8">Join the <br><span class="text-yellow-500 italic font-medium">Trade Network.</span></h2>
                <p class="text-lg text-gray-200 font-light leading-relaxed">
                    Register your hub to gain access to premium logistics tracking and regional market data.
                </p>
            </div>
            
            <div class="bg-white/5 backdrop-blur-md p-8 rounded-[2rem] border border-white/10 text-center">
                <p class="text-xs text-yellow-500 font-black uppercase tracking-widest mb-2">Automated Onboarding</p>
                <p class="text-[10px] text-gray-400">Your credentials will be protected using modern hashing standards.</p>
            </div>
        </div>

        <div class="w-full md:w-7/12 p-8 md:p-20 flex flex-col justify-center bg-white">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">Create Account</h1>
                <p class="text-slate-500 font-medium text-xs uppercase tracking-[0.2em]">Regional Hub Registration</p>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 border-l-4 border-red-600 p-4 mb-8 rounded-r-xl">
                    <p class="text-red-700 text-[10px] font-black uppercase tracking-widest"><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Username</label>
                    <div class="relative">
                        <i class="far fa-user absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="username" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl pl-14 pr-6 py-5 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="e.g. Mamfe_Hub">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Email</label>
                    <div class="relative">
                        <i class="far fa-envelope absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="email" name="email" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl px-16 py-5 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="manager@domain.cm">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Regional Hub Location</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <select name="hub_location" required 
                                 class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl px-16 py-5 text-sm font-bold text-slate-900 outline-none transition-all appearance-none">
                            <option value="" disabled selected>Select your region</option>
                            <option value="Mamfe">Mamfe Hub</option>
                            <option value="Bamenda">Bamenda Hub</option>
                            <option value="Douala">Douala Hub</option>
                            <option value="Yaoundé">Yaoundé Hub</option>
                            <option value="Kribi">Kribi Hub</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" name="password" id="pass" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl px-16 py-5 pr-14 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="••••••••">
                        <button type="button" onclick="togglePass('pass', 'icon1')" class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400">
                            <i id="icon1" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-[0.3em] text-slate-400 mb-3">Confirm</label>
                    <div class="relative">
                        <i class="fas fa-shield-alt absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" name="confirm_password" id="confirm_pass" required 
                               class="input-focus w-full bg-slate-50 border border-slate-200 rounded-2xl px-16 py-5 pr-14 text-sm font-bold text-slate-900 outline-none transition-all" 
                               placeholder="••••••••">
                        <button type="button" onclick="togglePass('confirm_pass', 'icon2')" class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400">
                            <i id="icon2" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="md:col-span-2 pt-4">
                    <button type="submit" 
                            class="w-full bg-green-800 text-white py-6 rounded-2xl font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl hover:bg-green-700 transition-all active:scale-[0.98]">
                        Complete Registration
                    </button>
                </div>
            </form>

            <div class="mt-12 text-center">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                    Already a member? <a href="login.php" class="text-green-800 font-black ml-2 hover:underline decoration-yellow-500 underline-offset-4">Sign In</a>
                </p>
                <p class="mt-8 text-[9px] text-slate-300 font-black uppercase tracking-[0.5em]">©️ 2026 Agri-Tech Solutions</p>
            </div>
        </div>
    </div>

    <script>
        function togglePass(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        const pass = document.getElementById('pass');
        const confirmPass = document.getElementById('confirm_pass');

        function checkMatch() {
            if (confirmPass.value.length > 0) {
                if (pass.value === confirmPass.value) {
                    confirmPass.classList.add('match-success');
                    confirmPass.classList.remove('match-error');
                } else {
                    confirmPass.classList.add('match-error');
                    confirmPass.classList.remove('match-success');
                }
            } else {
                confirmPass.classList.remove('match-success', 'match-error');
            }
        }

        pass.addEventListener('input', checkMatch);
        confirmPass.addEventListener('input', checkMatch);
    </script>
</body>
</html>
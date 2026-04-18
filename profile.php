 <?php
session_start();
require_once 'db.php';

// Access Control
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";
$username = $_SESSION['user'];

// DATABASE LOGIC: Fetch current user data
/*
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user_data = $stmt->fetch();
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle Profile Update Logic Here
    $message = "Your profile settings have been updated successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | Agri-Tech CM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .input-focus:focus { border-color: #166534; box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.05); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 px-6 h-20 flex items-center justify-between">
        <a href="user_dashboard.php" class="flex items-center gap-2 text-xs font-black text-green-800 uppercase tracking-widest hover:translate-x-[-5px] transition-all">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-green-800 flex items-center justify-center text-[10px] text-white font-bold">
                <?php echo strtoupper(substr($username, 0, 1)); ?>
            </div>
            <span class="font-black text-slate-900 uppercase text-[10px] tracking-widest"><?php echo htmlspecialchars($username); ?></span>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-16">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Account <span class="text-green-800">Security</span></h1>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.4em]">Manage your digital hub identity</p>
        </div>

        <?php if($message): ?>
            <div class="bg-green-50 border border-green-100 p-6 rounded-[2rem] mb-10 flex items-center gap-4">
                <i class="fas fa-shield-check text-green-600 text-xl"></i>
                <p class="text-green-800 text-[10px] font-black uppercase tracking-widest"><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            
            <div class="md:col-span-1 space-y-8">
                <div class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-[2.5rem] mx-auto mb-6 flex items-center justify-center text-3xl text-slate-300 relative">
                        <i class="fas fa-user"></i>
                        <button class="absolute -bottom-2 -right-2 bg-green-800 text-white w-10 h-10 rounded-2xl flex items-center justify-center border-4 border-white">
                            <i class="fas fa-camera text-xs"></i>
                        </button>
                    </div>
                    <h2 class="text-lg font-black text-slate-900 leading-tight"><?php echo htmlspecialchars($username); ?></h2>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">Verified Farmer</p>
                </div>

                <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-4">Trust Score</p>
                    <div class="flex items-center gap-4">
                        <span class="text-3xl font-black text-yellow-500">98%</span>
                        <div class="flex-1 bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-yellow-500 h-full w-[98%]"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <form action="profile.php" method="POST" class="bg-white p-10 md:p-14 rounded-[3rem] border border-slate-200 shadow-sm space-y-10">
                    
                    <div class="space-y-6">
                        <h3 class="text-[11px] font-black text-green-800 uppercase tracking-[0.3em] border-b border-slate-50 pb-4">Personal Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Full Name</label>
                                <input type="text" class="input-focus w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900" value="<?php echo htmlspecialchars($username); ?>">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Email Address</label>
                                <input type="email" class="input-focus w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900" value="user@agritech.cm">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-[11px] font-black text-green-800 uppercase tracking-[0.3em] border-b border-slate-50 pb-4">Hub Logistics</h3>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Primary Hub Location</label>
                            <select class="input-focus w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900">
                                <option>Buea Central Hub</option>
                                <option>Douala Export Center</option>
                                <option>Yaoundé Supply Hub</option>
                                <option>Bamenda Regional Hub</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-[11px] font-black text-red-600 uppercase tracking-[0.3em] border-b border-slate-50 pb-4">Security</h3>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Change Password</label>
                            <input type="password" class="input-focus w-full bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-800 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.3em] text-[11px] shadow-2xl hover:bg-slate-900 transition-all">
                        Update Account Information
                    </button>
                </form>
            </div>

        </div>
    </main>

</body>
</html>
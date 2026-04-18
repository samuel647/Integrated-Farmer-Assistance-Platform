<?php
require_once 'db.php';

// Security: Only admins can flip the switch
// if ($_SESSION['role'] !== 'admin') { header("Location: landing.php"); exit(); }

$message = "";
if (isset($_POST['toggle_maintenance'])) {
    $new_status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'maintenance_mode'");
    if ($stmt->execute([$new_status])) {
        $message = "System status updated successfully.";
    }
}

// Fetch current status
$status = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0f172a] text-white min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-slate-900 border border-white/5 p-10 rounded-[3rem] text-center">
        <div class="w-20 h-20 mx-auto mb-8 rounded-3xl flex items-center justify-center text-3xl <?= $status ? 'bg-red-500/20 text-red-500 animate-pulse' : 'bg-green-500/20 text-green-500' ?>">
            <i class="fas <?= $status ? 'fa-power-off' : 'fa-globe' ?>"></i>
        </div>

        <h2 class="text-2xl font-black uppercase tracking-tighter mb-2">Platform <span class="text-yellow-500">Status</span></h2>
        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-10">Toggle public access to the marketplace</p>

        <?php if($message): ?>
            <p class="text-xs text-green-500 font-bold mb-6"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="new_status" value="<?= $status ? 0 : 1 ?>">
            <button type="submit" name="toggle_maintenance" class="w-full py-5 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all <?= $status ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500' ?>">
                <?= $status ? 'Deactivate Maintenance' : 'Activate Maintenance' ?>
            </button>
        </form>

        <a href="admin_dashboard.php" class="inline-block mt-8 text-[9px] font-black text-slate-600 uppercase tracking-widest hover:text-white">Return to Dashboard</a>
    </div>

</body>
</html>
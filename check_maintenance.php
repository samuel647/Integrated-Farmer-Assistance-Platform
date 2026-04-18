 <?php
// We don't require db.php here because this file will be called INSIDE db.php
$m_stmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
$is_maintenance = $m_stmt->fetchColumn();

if ($is_maintenance == 1) {
    // Determine if the current user is an admin to allow them to bypass
    $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    
    // Get the current filename to avoid infinite redirect loops
    $currentPage = basename($_SERVER['PHP_SELF']);

    // If maintenance is ON, and user is NOT admin, and not already on the maintenance/login page
    if (!$isAdmin && $currentPage !== 'maintenance.php' && $currentPage !== 'login.php') {
        header("Location: maintenance.php");
        exit();
    }
}
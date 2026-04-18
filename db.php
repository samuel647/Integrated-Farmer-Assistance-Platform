<?php
$host = 'localhost';
$db   = 'cameroon_agritech'; // Verified database name
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// --- NEW MAINTENANCE LOGIC START ---

// 1. Ensure session is active for all pages
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// 2. Define protected access rules
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['maintenance.php', 'login.php', 'signup.php'];

try {
    // UPDATED: Now looking at 'app_settings' instead of 'system_settings'
    $m_stmt = $pdo->query("SELECT setting_value FROM app_settings WHERE setting_key = 'maintenance_mode'");
    $is_maintenance = $m_stmt->fetchColumn();

    if ($is_maintenance == 1) {
        // Only block if user is NOT an admin
        $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
        
        // If it's maintenance mode, and not an admin, and not already on an allowed page: REDIRECT
        if (!$isAdmin && !in_array($current_page, $public_pages)) {
            header("Location: maintenance.php");
            exit();
        }
    }
} catch (PDOException $e) {
    // This prevents the site from crashing if the table is missing
}

// --- NEW MAINTENANCE LOGIC END ---
?>
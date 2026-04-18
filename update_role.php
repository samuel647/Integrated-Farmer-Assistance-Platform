 <?php
session_start();
require_once 'db.php';

/**
 * SECURITY GATEKEEPER
 * We ensure only logged-in Admins can execute this script. 
 * If a regular farmer tries to access this URL directly, they are kicked out.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized Access: High-level privilege required to modify personnel roles.");
}

// Check if the form was actually submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id']) && isset($_POST['new_role'])) {
    
    $target_user_id = $_POST['user_id'];
    $requested_role = $_POST['new_role'];

    /**
     * SELF-PRESERVATION CHECK
     * We prevent the current admin from accidentally revoking their own admin status.
     * Without this, you could lock yourself out of the system forever.
     */
    if ($target_user_id == $_SESSION['user_id'] && $requested_role !== 'admin') {
        header("Location: admin_dashboard.php?error=cannot_demote_self");
        exit();
    }

    try {
        // 1. Update the User Role
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$requested_role, $target_user_id]);

        // 2. Log the Action (Audit Trail)
        // This ensures there is a record of WHO promoted WHOM.
        $log_stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action_type) VALUES (?, ?)");
        $action_text = "Changed User ID: " . $target_user_id . " to role: " . $requested_role;
        $log_stmt->execute([$_SESSION['user_id'], $action_text]);

        // 3. Success Redirection
        header("Location: admin_dashboard.php?success=role_updated");
        exit();

    } catch (PDOException $e) {
        // If the database fails (e.g., missing audit_logs table), show the error
        header("Location: admin_dashboard.php?error=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    // If someone tries to access this script without a POST request, send them back
    header("Location: admin_dashboard.php");
    exit();
}
<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all inputs
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone     = htmlspecialchars(trim($_POST['phone']));
    $subject   = htmlspecialchars(trim($_POST['subject']));
    $message   = htmlspecialchars(trim($_POST['message']));

    if (!empty($full_name) && !empty($phone) && !empty($message)) {
        try {
            $sql = "INSERT INTO contact_messages (full_name, email, phone, subject, message) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$full_name, $email, $phone, $subject, $message])) {
                header("Location: landing.php?contact=success#contact");
                exit();
            }
        } catch (PDOException $e) {
            header("Location: landing.php?contact=error#contact");
            exit();
        }
    } else {
        header("Location: landing.php?contact=empty#contact");
        exit();
    }
}
?>
<?php
session_start();


$host = "localhost"; 
$dbname = "user_auth";   
$db_username = "root";               
$db_password = "";                    

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];

    if (!empty($email) && !empty($new_password)) {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $found_user = $stmt->fetch();

        if ($found_user) {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($update_stmt->execute([$hashed_password, $email])) {
                $_SESSION['success_message'] = "Password updated successfully! Please login with your new password.";
                header("Location: login.php");
                exit();
            } else {
                echo "Failed to update the password. Please try again.";
            }
        } else {
            echo "No account found with that email address.";
        }
    } else {
        echo "Please fill in all fields.";
    }
} else {
    header("Location: forgot_password.html");
    exit();
}
?>

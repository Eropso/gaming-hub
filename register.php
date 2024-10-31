<?php
session_start();
include("database.php");
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'hub.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && $_POST['submit'] === 'register') {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($email)) {
        echo "Please enter a valid email.";
    } elseif (empty($password)) {
        echo "Please enter a valid password.";
    } else {
        // Register new user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (user, username, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $username, $hash);
        if (mysqli_stmt_execute($stmt)) {
            echo "You are registered!";

            // Generate OTP and send it
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $email;
            $_SESSION['redirect'] = $redirect; // Store redirect URL

            // Send OTP via email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'phpkuben@gmail.com';
                $mail->Password = 'srnq cqiy dqzu kyfl';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('from@example.com', 'EroZone');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Verification Code for EroZone Registration';
                $mail->Body = "Your Verification Code is <b>$otp</b>";
                $mail->AltBody = "Your Verification Code is $otp";

                $mail->send();

                // Redirect to OTP verification page
                header("Location: verify_otp.php");
                exit();
            } catch (Exception $e) {
                echo "Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>Login or Register</h2>

    <form action="" method="POST">
        <div>
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="submit" value="register">Sign Up / Sign In</button>
    </form>
</div>
</body>
</html>

<?php
session_start();
include("database.php");
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$redirect = 'hub.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && $_POST['submit'] === 'register') {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($email) || empty($username) || empty($password)) {
        echo "Please fill all fields.";
    } else {
        // Check if user already exists
        $sql_check = "SELECT * FROM users WHERE user = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            echo "User already exists. Please log in.";
        } else {
            // Register new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (user, username, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $email, $username, $hash);
            if (mysqli_stmt_execute($stmt)) {
                echo "You are registered!";

                // Generate OTP and send it
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['email'] = $email;

                // Send OTP via email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'phpkuben@gmail.com';
                    $mail->Password = 'srnq cqiy dqzu kyfl'; // Keep credentials secure
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>Register</h2>
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
        <button type="submit" name="submit" value="register">Sign Up</button>
    </form>
    <p>
        Already have an account? 
        <a href="login.php">Log in here</a>
    </p>
</div>
</body>
</html>

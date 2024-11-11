<?php
session_start();
include("database.php");
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$redirect = 'hub.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && $_POST['submit'] === 'login') {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($email) || empty($password)) {
        echo "Please fill all fields.";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE user = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // User exists, verify password
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                // Password correct, generate OTP
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['email'] = $email;
                $_SESSION['is_new_user'] = false;
                $_SESSION['user_id'] = $user['id']; 


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
                    $mail->Subject = 'Verification Code for EroZone Login';
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
                echo "Incorrect password. Please try again.";
            }
        } else {
            echo "User not found, check your credentials and make sure they are correct.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>Login</h2>
    <form action="" method="POST">
        <div>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="submit" value="login">Log In</button>

        <p>
            Not a user?
            <a href="register.php">Register yourself now</a>
        </p>
    </form>
</div>
</body>
</html>

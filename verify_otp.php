<?php
session_start();
include("database.php");

if (!isset($_SESSION['otp'])) {
    header("Location: login.php");
    exit();
}

// Verify OTP
if (isset($_POST['submit']) && $_POST['submit'] === 'verify_otp') {
    $entered_otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT);
    if ($entered_otp == $_SESSION['otp']) {
        // OTP verified, set session
        $_SESSION['loggedin'] = true;
        unset($_SESSION['otp']);

        // Redirect to the original game or hub
        $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : 'hub.php';
        header("Location: " . $redirect);
        exit();
    } else {
        echo "Invalid Verification Code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
    <h2>2-Step Verification</h2>

    <form action="" method="POST">
        <div>
            <input type="text" name="otp" placeholder="Enter 6-digit Code" required>
        </div>
        <button type="submit" name="submit" value="verify_otp">Verify</button>
    </form>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS | Reset Password</title>
    <link rel="stylesheet" type="text/css" href="../css/forgetPassoword.css">
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <p>Enter your new password.</p>
        <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div style="color: red;">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div style="color: green;">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <form action="password_reset_code.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
            <input type="hidden" name="password_token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            <label for="password">New Password<sup>*</sup></label>
            <div class="input-box">
                <input type="password" name="new_password" placeholder="Enter new password" required>
            </div>
            <label for="confirm-password">Confirm Password<sup>*</sup></label>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm password" required>
            </div>
            <div id="password-error" style="color: red;"></div>
            <button class="OTP-btn" type="submit" name="reset_password">Reset Password</button>
            <div class="back">
                Go back to <a href="login.php">Log In</a>
            </div>
        </form>
    </div>   
</body>
</html>
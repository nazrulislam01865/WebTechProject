<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus | Forget Password</title>
    <link rel="stylesheet" type="text/css" href="../css/forgetReset.css">
</head>
<body>
    <div class="container">
        <div class="leftSide">
            <h2>Welcome!</h2>
            <p>Enter your email to reset your password.</p>
            <a href="login.php" class="btn">Back</a>
        </div>
        <div class="rightSide">
            <h2>Forgot Password</h2>
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div style="color: red;">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <form id="forgotPasswordForm" action="password_reset_code.php" method="POST">
                <div class="input-box"> 
                    <input type="email" placeholder="Email" id="email" name="email" value="" style="width: 250px; max-width: 250px;">
                    <span id="emailError" class="error-text"></span>
                </div>
                <button type="submit" name="password_reset_link" class="forgot-btn" style="width: 250px; max-width: 250px;">Send Password Reset Link</button>
            </form>
        </div>
    </div>
</body>
</html>
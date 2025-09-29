<?php
session_start();
$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "gobus";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function send_password_reset($get_username, $get_email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'saruarmunna17@gmail.com';
        $mail->Password = 'rxro qyva fnvf tyrk'; // Provided App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('saruarmunna17@gmail.com', $get_username);
        $mail->addAddress($get_email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Notification';
        $mail->Body = '<h2>Hello ' . htmlspecialchars($get_username) . '</h2>
                       <p>Click the link below to reset your password.</p>
                       <a href="http://localhost/webtech/WebTechProject/html/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($get_email) . '">Reset Password</a>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

try {
    $conn = new mysqli($servername, $username, $password_db, $dbname);

    if ($conn->connect_error) {
        $_SESSION['error'] = "Database connection failed: " . $conn->connect_error;
        header("Location: forgetReset.php");
        exit(0);
    }

    if (isset($_POST['password_reset_link'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $token = bin2hex(random_bytes(32)); // Secure token generation

        $check_email = "SELECT username, email FROM users WHERE email='$email' LIMIT 1";
        $check_email_run = mysqli_query($conn, $check_email);

        if (mysqli_num_rows($check_email_run) > 0) {
            $row = mysqli_fetch_array($check_email_run);
            $get_username = $row['username'];
            $get_email = $row['email'];

            $update_token = "UPDATE users SET verify_token='$token' WHERE email='$get_email' LIMIT 1";
            $update_token_run = mysqli_query($conn, $update_token);

            if ($update_token_run) {
                if (send_password_reset($get_username, $get_email, $token)) {
                    $_SESSION['error'] = "We emailed you a password reset link.";
                    header("Location: forgetReset.php");
                    exit(0);
                } else {
                    $_SESSION['error'] = "Failed to send reset email. Check SMTP settings.";
                    header("Location: forgetReset.php");
                    exit(0);
                }
            } else {
                $_SESSION['error'] = "Failed to update token: " . mysqli_error($conn);
                header("Location: forgetReset.php");
                exit(0);
            }
        } else {
            $_SESSION['error'] = "Email does not exist.";
            header("Location: forgetReset.php");
            exit(0);
        }
    } elseif (isset($_POST['reset_password'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $token = mysqli_real_escape_string($conn, $_POST['password_token']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        if (!empty($token)) {
            if (!empty($email) && !empty($new_password) && !empty($confirm_password)) {
                $check_token = "SELECT verify_token FROM users WHERE verify_token='$token' AND email='$email' LIMIT 1";
                $check_token_run = mysqli_query($conn, $check_token);

                if (mysqli_num_rows($check_token_run) > 0) {
                    if ($new_password === $confirm_password) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_password = "UPDATE users SET password='$hashed_password', verify_token='' WHERE email='$email' LIMIT 1";
                        $update_password_run = mysqli_query($conn, $update_password);

                        if ($update_password_run) {
                            $_SESSION['success'] = "Password reset successfully.";
                            header("Location: login.php");
                            exit(0);
                        } else {
                            $_SESSION['error'] = "Failed to reset password: " . mysqli_error($conn);
                            header("Location: reset_password.php?token=$token&email=$email");
                            exit(0);
                        }
                    } else {
                        $_SESSION['error'] = "Passwords do not match.";
                        header("Location: reset_password.php?token=$token&email=$email");
                        exit(0);
                    }
                } else {
                    $_SESSION['error'] = "Invalid token or email.";
                    header("Location: reset_password.php?token=$token&email=$email");
                    exit(0);
                }
            } else {
                $_SESSION['error'] = "All fields are required.";
                header("Location: reset_password.php?token=$token&email=$email");
                exit(0);
            }
        } else {
            $_SESSION['error'] = "No token provided.";
            header("Location: reset_password.php");
            exit(0);
        }
    } else {
        $_SESSION['error'] = "Invalid request.";
        header("Location: forgetReset.php");
        exit(0);
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: forgetReset.php");
    exit(0);
}
?>
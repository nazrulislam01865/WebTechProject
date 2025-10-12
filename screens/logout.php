<?php
session_start();

// Database connection details
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "gobus";

$conn = null;

// Determine the logged-in type
$user_id = $_SESSION['user_id'] ?? null;
$company_id = $_SESSION['company_id'] ?? null;
$admin_id = $_SESSION['admin_id'] ?? null;

// Determine secure flag based on current connection
$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

try {
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Handle user logout
    if ($user_id) {
        $sql = "UPDATE users SET remember_token = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }
        // Destroy user cookie - match parameters from login (adjust secure if needed)
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, '/', '', $secure, true);
        }
    }
    // Handle bus company logout
    elseif ($company_id) {
        $sql = "UPDATE bus_companies SET remember_token = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $company_id);
            $stmt->execute();
            $stmt->close();
        }
        // Destroy company cookie
        if (isset($_COOKIE['remember_company'])) {
            setcookie('remember_company', '', time() - 3600, '/', '', $secure, true);
        }
    }
    // Handle admin logout
    elseif ($admin_id) {
        // Destroy admin cookie
        if (isset($_COOKIE['remember_admin'])) {
            setcookie('remember_admin', '', time() - 3600, '/', '', $secure, true);
        }
    }

    $conn->close();
} catch (Exception $e) {
    // Log error but continue
    error_log("Logout database error: " . $e->getMessage());
    if ($conn) {
        $conn->close();
    }
}

// Unset all session variables
$_SESSION = [];

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

if (headers_sent($file, $line)) {
    echo "<p>Error: Cannot redirect, headers already sent in $file on line $line</p>";
} else {
    header("Location: login.php");
    exit();
}
?>
<?php
session_start();
$error = "";
$phone = $_POST['phone'] ?? '';
$login_type = $_POST['login_type'] ?? 'user'; // Default to user login

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Validate phone and password
    if (!preg_match("/^\d{11}$/", $phone)) {
        $error = "Phone number must be 11 digits.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } else {
        // Admin login (default logic)
        if ($login_type === 'admin') {
            $admin_phone = '12345678901';
            $admin_password = 'admin123'; // In production, hash this and store securely

            if ($phone === $admin_phone && $password === $admin_password) {
                $_SESSION['admin_id'] = 1; // Arbitrary admin ID
                $_SESSION['admin_name'] = 'Admin';
                header("Location: ./admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid admin credentials.";
            }
        } else {
            // Database connection for user or bus company login
            $servername = "localhost";
            $username = "root";
            $password_db = "";
            $dbname = "gobus";

            try {
                $conn = new mysqli($servername, $username, $password_db, $dbname);

                if ($conn->connect_error) {
                    $error = "Database connection failed: " . $conn->connect_error;
                } else {
                    // User login
                    if ($login_type === 'user') {
                        $sql = "SELECT id, username, password FROM users WHERE phone = ?";
                        $stmt = $conn->prepare($sql);
                        if (!$stmt) {
                            $error = "Query preparation failed: " . $conn->error;
                        } else {
                            $stmt->bind_param("s", $phone);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $user = $result->fetch_assoc();
                                if (password_verify($password, $user['password'])) {
                                    $_SESSION['user_id'] = $user['id'];
                                    $_SESSION['username'] = $user['username'];
                                    header("Location: ./userDashboard.php");
                                    exit();
                                } else {
                                    $error = "Invalid phone number or password.";
                                }
                            } else {
                                $error = "Invalid phone number or password.";
                            }
                            $stmt->close();
                        }
                    }
                    // Bus Company login
                    elseif ($login_type === 'company') {
                        $sql = "SELECT id, company_name, password FROM bus_companies WHERE phone = ?";
                        $stmt = $conn->prepare($sql);
                        if (!$stmt) {
                            $error = "Query preparation failed: " . $conn->error;
                        } else {
                            $stmt->bind_param("s", $phone);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $company = $result->fetch_assoc();
                                if (password_verify($password, $company['password'])) {
                                    $_SESSION['company_id'] = $company['id'];
                                    $_SESSION['company_name'] = $company['company_name'];
                                    header("Location: ./ownerBus.php");
                                    exit();
                                } else {
                                    $error = "Invalid phone number or password.";
                                }
                            } else {
                                $error = "Invalid phone number or password.";
                            }
                            $stmt->close();
                        }
                    }
                    $conn->close();
                }
            } catch (mysqli_sql_exception $e) {
                $error = "Database error: " . $e->getMessage();
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
    <title>GoBus | Login</title>
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>
<body>
    <div class="container">
        <div class="leftSide">
            <h2>Welcome!</h2>
            <p>Enter your personal details to open an account.</p>
            <a href="signup.php" class="btn">SIGN UP</a>
        </div>
        <div class="rightSide">
            <h2>LOG IN</h2>
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <?php if (!empty($error)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <div class="login-type-radio">
                    <label class="radio-label">
                        <input type="radio" name="login_type" value="user" <?php echo $login_type === 'user' ? 'checked' : ''; ?>> User
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="login_type" value="company" <?php echo $login_type === 'company' ? 'checked' : ''; ?>> Bus Company
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="login_type" value="admin" <?php echo $login_type === 'admin' ? 'checked' : ''; ?>> Admin
                    </label>
                </div>
                <div class="input-box"> 
                    <input type="text" placeholder="Phone Number" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" style="width: 250px; max-width: 250px;">
                    <span id="phoneError" class="error-text"></span>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password" id="password" name="password" style="width: 250px; max-width: 250px;">
                    <span id="passwordError" class="error-text"></span>
                </div>
                <a href="forgetReset.php" class="forgot-btn">Forgot Password</a>
                <button type="submit" class="login-btn" style="width: 250px; max-width: 250px;">LOG IN</button>
            </form>
        </div>
    </div>
</body>
</html>
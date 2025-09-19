<!-- 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|Login</title>
        <link rel="stylesheet" type="text/css" href="../css/login.css">
    </head>
    <body>
        <?php
            $error = ""; // Variable to store error message
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $phone = $_POST['phone'];
                $password = $_POST['password'];

                // Database connection
                $servername = "localhost";
                $username = "root";
                $password_db = ""; 
                $dbname = "gobus"; 

                $conn = new mysqli($servername, $username, $password_db, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Prepare and execute query
                $sql = "SELECT * FROM users WHERE phone = ? AND password = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $phone, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Successful login, redirect to dashboard
                    header("Location: userDashboard.html");
                    exit();
                } else {
                    $error = "Invalid phone number or password.";
                }

                $stmt->close();
                $conn->close();
            }
        ?>
        <div class="container">
            <div class="leftSide">
                <h2>Welcome!</h2>
                <p>Enter your personal details to open an account.</p>
                <a href="signup.php" class="btn">SIGN UP</a>
            </div>
            <div class="rightSide">
                <h2>LOG IN</h2>
                <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm(event)">
                    <?php echo !empty($error) ? "<p class='error-message'>$error</p>" : ''; ?>
                    <div class="input-box"> 
                        <input type="text" placeholder="Phone Number" id="phone" name="phone" style="width: 250px; max-width: 250px;">
                        <span id="phoneError" class="error-text"></span>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Password" id="password" name="password" style="width: 250px; max-width: 250px;">
                        <span id="passwordError" class="error-text"></span>
                    </div>
                    <a href="forgotPassword.html" class="forgot-btn">Forgot Password</a>
                    <button type="submit" class="login-btn" style="width: 250px; max-width: 250px;">LOG IN</button>
                </form>
            </div>
        </div>
        <script src="../data/validForm.js"></script> 
    </body>
</html> -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus | Login</title>
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>
<body>
    <?php


    session_start();
    $error = "";
    $phone = $_POST['phone'] ?? '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];

        if (!preg_match("/^\d{11}$/", $phone)) {
            $error = "Phone number must be 11 digits.";
        } elseif (empty($password)) {
            $error = "Password is required.";
        } else {

            $servername = "localhost";
            $username = "root";
            $password_db = "";
            $dbname = "gobus";

            try {
                $conn = new mysqli($servername, $username, $password_db, $dbname);

                if ($conn->connect_error) {
                    $error = "Database connection failed: " . $conn->connect_error;
                } else {

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

                                if (headers_sent($file, $line)) {
                                    $error = "Cannot redirect, headers already sent in $file on line $line";
                                } else {
                                    header("Location: userDashboard.php");
                                    exit();
                                }
                            } else {
                                $error = "Invalid phone number or password.";
                            }
                        } else {
                            $error = "Invalid phone number or password.";
                        }

                        $stmt->close();
                    }
                    $conn->close();
                }
            } catch (mysqli_sql_exception $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
    ?>
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
                <div class="input-box"> 
                    <input type="text" placeholder="Phone Number" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" style="width: 250px; max-width: 250px;">
                    <span id="phoneError" class="error-text"></span>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password" id="password" name="password"  style="width: 250px; max-width: 250px;">
                    <span id="passwordError" class="error-text"></span>
                </div>
                <a href="forgotPassword.php" class="forgot-btn">Forgot Password</a>
                <button type="submit" class="login-btn" style="width: 250px; max-width: 250px;">LOG IN</button>
            </form>
        </div>
    </div>
</body>
</html>
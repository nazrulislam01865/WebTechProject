<!-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|Login</title>
        <link rel = "stylesheet" type="text/css" href="../css/login.css">
    </head>
    <body>
         <div class = "container">
            <div class="leftSide">
                <h2>Welcome!</h2>
                <p>Enter your personal details to open an account.</p>
                <a href="signup.html" class ="btn">SIGN UP</a>
            </div>
            <div class="rightSide">
                <h2>Log In</h2>
                <form>
                    <div class = "input-box"> 
                        <input type="text" placeholder ="Phone Number" required>
                    </div>
                    <div class = "input-box">
                        <input type="password" placeholder ="Password" required>
                    </div>
                    <a href="forgotPassword.html" class="forgot-btn">Forgot Password</a>
                    <button type="submit" class="login-btn">LOG IN</button>
                </form>
            </div>
        </div>
    </body>
</html> -->

<!-- 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|Login</title>
        <link rel = "stylesheet" type="text/css" href="../css/login.css">
    </head>
    <body>
         <div class = "container">
            <div class="leftSide">
                <h2>Welcome!</h2>
                <p>Enter your personal details to open an account.</p>
                <a href="signup.html" class ="btn">SIGN UP</a>
            </div>
            <div class="rightSide">
                <h2>Log In</h2>
                <form id="loginForm" onsubmit="return validateForm(event)">
                    <div class = "input-box"> 
                        <input type="text" placeholder ="Phone Number" required id="phone">
                    </div>
                    <div class = "input-box">
                        <input type="password" placeholder ="Password" required id="password">
                    </div>
                    <a href="forgotPassword.html" class="forgot-btn">Forgot Password</a>
                    <button type="submit" class="login-btn">LOG IN</button>
                </form>
            </div>
        </div>
        <script src="../data/admin_dashboard.js">
        </script>
    </body>
</html> -->



<!-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|Login</title>
        <link rel="stylesheet" type="text/css" href="../css/login.css">
    </head>
    <body>
        <?php
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
                    echo "<p style='color: red; text-align: center;'>Invalid phone number or password.</p>";
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
                <h2>Log In</h2>
                <form id="loginForm" action="userDashboard.html" method="POST" onsubmit="return validateForm(event)">
                    <div class="input-box"> 
                        <input type="text" placeholder="Phone Number" required id="phone" name="phone">
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Password" required id="password" name="password">
                    </div>
                    <a href="forgotPassword.html" class="forgot-btn">Forgot Password</a>
                    <button type="submit" class="login-btn">LOG IN</button>
                </form>
                
            </div>
        </div>
        <script src="../data/validForm.js">
        </script>
    </body>
</html> -->


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
                    $error = "<p style='color: red; text-align: center;'>Invalid phone number or password.</p>";
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
                <h2>Log In</h2>
                <?php echo $error; // Display server-side error message ?>
                <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm(event)">
                    <div class="input-box"> 
                        <input type="text" placeholder="Phone Number" required id="phone" name="phone">
                        <span id="phoneError" style="color: red; font-size: 0.8em;"></span>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Password" required id="password" name="password">
                        <span id="passwordError" style="color: red; font-size: 0.8em;"></span>
                    </div>
                    <a href="forgotPassword.html" class="forgot-btn">Forgot Password</a>
                    <button type="submit" class="login-btn">LOG IN</button>
                </form>
            </div>
        </div>
        <script src="../data/validForm.js">    
        </script>
    </body>
</html> -->


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
                <h2>Log In</h2>
                <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm(event)">
                    <?php echo !empty($error) ? "<p class='error-message'>$error</p>" : ''; ?>
                    <div class="input-box"> 
                        <input type="text" placeholder="Phone Number" required id="phone" name="phone" style="width: 250px; max-width: 250px;">
                        <span id="phoneError" class="error-text"></span>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Password" required id="password" name="password" style="width: 250px; max-width: 250px;">
                        <span id="passwordError" class="error-text"></span>
                    </div>
                    <a href="forgotPassword.html" class="forgot-btn">Forgot Password</a>
                    <button type="submit" class="login-btn" style="width: 250px; max-width: 250px;">LOG IN</button>
                </form>
            </div>
        </div>
        <script src="../data/validForm.js"></script>
    </body>
</html>
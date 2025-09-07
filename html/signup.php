<!-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|SignUp</title>
        <link rel = "stylesheet" type="text/css" href="../css/signup.css">
    </head>
    <body>
        <div class = "container">
            <div class="leftSide">
                <h2>Welcome!</h2>
                <p>Already have an account?</p>
                <a href="login.html" class="btn">LOG IN</a>
            </div>
            <div class="rightSide">
                <h2>Create Account</h2>
                <form>
                    <div class = "input-box">
                        <input type="text" placeholder ="Username" required>
                    </div>
                    <div class = "input-box">
                        <input type="email" placeholder ="Email" required>
                    </div>
                    <div class = "input-box">
                        <input type="text" placeholder ="Phone Number" required>
                    </div>
                    <div class = "input-box">
                        <input type="text" placeholder ="NID Number" required>
                    </div>
                    <div class = "input-box">
                        <input type="password" placeholder ="Password" required>
                    </div>
                    <div class = "input-box">
                        <input type="password" placeholder ="Confirm Password" required>
                    </div>
                    <button type="submit" class="signup-btn">SIGN UP</button>
                </form>
            </div>
        </div>
    </body>
</html> -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|SignUp</title>
        <link rel="stylesheet" type="text/css" href="../css/signup.css">
    </head>
    <body>
        <div class="container">
            <div class="leftSide">
                <h2>Welcome!</h2>
                <p>Already have an account?</p>
                <a href="login.php" class="btn">LOG IN</a>
            </div>
            <div class="rightSide">
                <h2>Create Account</h2>
                <?php
                // Initialize error array and form data
                $errors = [];
                $form_data = [
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'nid' => $_POST['nid'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'confirm_password' => $_POST['confirm_password'] ?? ''
                ];

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Database connection (XAMPP defaults)
                    $servername = "localhost";
                    $username = "root";
                    $password_db = "";
                    $dbname = "gobus";

                    $conn = new mysqli($servername, $username, $password_db, $dbname);

                    if ($conn->connect_error) {
                        $errors['general'] = "Database connection failed: " . $conn->connect_error;
                    } else {
                        // Server-side validation
                        if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $form_data['username'])) {
                            $errors['username'] = "Username must be 3-20 characters, alphanumeric with underscores.";
                        }
                        if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
                            $errors['email'] = "Invalid email format.";
                        }
                        if (!preg_match("/^\d{11}$/", $form_data['phone'])) {
                            $errors['phone'] = "Phone number must be 11 digits.";
                        }
                        if (!preg_match("/^\d{10}$/", $form_data['nid'])) {
                            $errors['nid'] = "NID number must be 10 digits.";
                        }
                        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $form_data['password'])) {
                            $errors['password'] = "Password must be at least 8 characters, with one uppercase, one lowercase, one number, and one special character.";
                        }
                        if ($form_data['password'] !== $form_data['confirm_password']) {
                            $errors['confirm_password'] = "Passwords do not match.";
                        }

                        // Check for duplicates
                        if (empty($errors)) {
                            $checks = [
                                "username" => $form_data['username'],
                                "email" => $form_data['email'],
                                "phone" => $form_data['phone'],
                                "nid" => $form_data['nid']
                            ];
                            foreach ($checks as $field => $value) {
                                $sql = "SELECT $field FROM users WHERE $field = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $value);
                                $stmt->execute();
                                if ($stmt->get_result()->num_rows > 0) {
                                    $errors[$field] = ucfirst($field) . " already exists.";
                                }
                                $stmt->close();
                            }
                        }

                        // If no errors, insert user into database
                        if (empty($errors)) {
                            $hashed_password = password_hash($form_data['password'], PASSWORD_DEFAULT);
                            $sql = "INSERT INTO users (username, email, phone, nid, password) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("sssss", $form_data['username'], $form_data['email'], $form_data['phone'], $form_data['nid'], $hashed_password);
                            if ($stmt->execute()) {
                                header("Location: login.php");
                                exit();
                            } else {
                                $errors['general'] = "Error creating account: " . $conn->error;
                            }
                            $stmt->close();
                        }
                        $conn->close();
                    }
                }
                ?>

                <!-- Display general errors (e.g., database issues) -->
                <?php if (isset($errors['general'])): ?>
                    <div style="color: red; text-align: center;">
                        <p><?php echo htmlspecialchars($errors['general']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="signup.php" method="POST">
                    <div class="input-box">
                        <input type="text" placeholder="Username" required name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>">
                        <?php if (isset($errors['username'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['username']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="email" placeholder="Email" required name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['email']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="text" placeholder="Phone Number" required name="phone" value="<?php echo htmlspecialchars($form_data['phone']); ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['phone']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="text" placeholder="NID Number" required name="nid" value="<?php echo htmlspecialchars($form_data['nid']); ?>">
                        <?php if (isset($errors['nid'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['nid']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Password" required name="password">
                        <?php if (isset($errors['password'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['password']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Confirm Password" required name="confirm_password">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="signup-btn">SIGN UP</button>
                </form>
            </div>
        </div>
    </body>
</html>


<!-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBus|SignUp</title>
        <link rel="stylesheet" type="text/css" href="../css/signup.css">
    </head>
    <body>
        <div class="container">
            <div class="leftSide">
                <h2>Welcome!</h2>
                <p>Already have an account?</p>
                <a href="index.php" class="btn">LOG IN</a>
            </div>
            <div class="rightSide">
                <h2>Create Account</h2>
                <?php
                // Include database connection
                $conn = require_once 'db.php';

                // Initialize error array and form data
                $errors = [];
                $form_data = [
                    'username' => $_POST['username'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'nid' => $_POST['nid'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'confirm_password' => $_POST['confirm_password'] ?? ''
                ];

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Server-side validation
                    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $form_data['username'])) {
                        $errors['username'] = "Username must be 3-20 characters, alphanumeric with underscores.";
                    }
                    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors['email'] = "Invalid email format.";
                    }
                    if (!preg_match("/^\d{10}$/", $form_data['phone'])) {
                        $errors['phone'] = "Phone number must be 10 digits.";
                    }
                    if (!preg_match("/^\d{10}$/", $form_data['nid'])) {
                        $errors['nid'] = "NID number must be 10 digits.";
                    }
                    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $form_data['password'])) {
                        $errors['password'] = "Password must be at least 8 characters, with one uppercase, one lowercase, one number, and one special character.";
                    }
                    if ($form_data['password'] !== $form_data['confirm_password']) {
                        $errors['confirm_password'] = "Passwords do not match.";
                    }

                    // Check for duplicates
                    if (empty($errors)) {
                        $checks = [
                            "username" => $form_data['username'],
                            "email" => $form_data['email'],
                            "phone" => $form_data['phone'],
                            "nid" => $form_data['nid']
                        ];
                        foreach ($checks as $field => $value) {
                            $sql = "SELECT $field FROM users WHERE $field = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $value);
                            $stmt->execute();
                            if ($stmt->get_result()->num_rows > 0) {
                                $errors[$field] = ucfirst($field) . " already exists.";
                            }
                            $stmt->close();
                        }
                    }

                    // If no errors, insert user into database
                    if (empty($errors)) {
                        $hashed_password = password_hash($form_data['password'], PASSWORD_DEFAULT);
                        $sql = "INSERT INTO users (username, email, phone, nid, password) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssss", $form_data['username'], $form_data['email'], $form_data['phone'], $form_data['nid'], $hashed_password);
                        if ($stmt->execute()) {
                            header("Location: index.php");
                            exit();
                        } else {
                            $errors['general'] = "Error creating account: " . $conn->error;
                        }
                        $stmt->close();
                    }
                    $conn->close();
                }
                ?>

                <!-- Display general errors (e.g., database issues) -->
                <?php if (isset($errors['general'])): ?>
                    <div style="color: red; text-align: center;">
                        <p><?php echo htmlspecialchars($errors['general']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="signup.php" method="POST">
                    <div class="input-box">
                        <input type="text" placeholder="Username" required name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>">
                        <?php if (isset($errors['username'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['username']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="email" placeholder="Email" required name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['email']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="text" placeholder="Phone Number" required name="phone" value="<?php echo htmlspecialchars($form_data['phone']); ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['phone']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="text" placeholder="NID Number" required name="nid" value="<?php echo htmlspecialchars($form_data['nid']); ?>">
                        <?php if (isset($errors['nid'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['nid']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Password" required name="password">
                        <?php if (isset($errors['password'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['password']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="input-box">
                        <input type="password" placeholder="Confirm Password" required name="confirm_password">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="signup-btn">SIGN UP</button>
                </form>
            </div>
        </div>
    </body>
</html> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus|User Account Settings</title>
    <link rel="stylesheet" type="text/css" href="../css/userAccountSettings.css">
</head>
<body>
    <?php
    // Start session
    session_start();

    // Enable strict MySQLi error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    $username = htmlspecialchars($_SESSION['username']);
    $errors = [];
    $success = "";
    $form_data = [
        'name' => $_POST['name'] ?? $username,
        'current_password' => $_POST['current_password'] ?? '',
        'new_password' => $_POST['new_password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];

    // Database connection
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "gobus";

    try {
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        if ($conn->connect_error) {
            $errors['general'] = "Database connection failed: " . $conn->connect_error;
        } else {
            // Handle form submission
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Server-side validation
                if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $form_data['name'])) {
                    $errors['name'] = "Username must be 3-20 characters, alphanumeric with underscores.";
                } else {
                    // Check if username is taken (exclude current user)
                    $sql = "SELECT username FROM users WHERE username = ? AND id != ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $form_data['name'], $_SESSION['user_id']);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows > 0) {
                        $errors['name'] = "Username already exists.";
                    }
                    $stmt->close();
                }

                if (!empty($form_data['current_password'])) {
                    // Verify current password
                    $sql = "SELECT password FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    if (!password_verify($form_data['current_password'], $user['password'])) {
                        $errors['current_password'] = "Current password is incorrect.";
                    }
                    $stmt->close();

                    // Validate new password if provided
                    if (!empty($form_data['new_password'])) {
                        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $form_data['new_password'])) {
                            $errors['new_password'] = "New password must be at least 8 characters, with one uppercase, one lowercase, one number, and one special character.";
                        }
                        if ($form_data['new_password'] !== $form_data['confirm_password']) {
                            $errors['confirm_password'] = "New passwords do not match.";
                        }
                    } else {
                        $errors['new_password'] = "New password is required when changing password.";
                    }
                }

                // Update user data if no errors
                if (empty($errors)) {
                    $sql = "";
                    $params = [];
                    $types = "";
                    $update_fields = [];

                    if ($form_data['name'] !== $username) {
                        $update_fields[] = "username = ?";
                        $params[] = $form_data['name'];
                        $types .= "s";
                    }

                    if (!empty($form_data['new_password']) && !empty($form_data['current_password'])) {
                        $hashed_password = password_hash($form_data['new_password'], PASSWORD_DEFAULT);
                        $update_fields[] = "password = ?";
                        $params[] = $hashed_password;
                        $types .= "s";
                    }

                    if (!empty($update_fields)) {
                        $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
                        $params[] = $_SESSION['user_id'];
                        $types .= "i";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param($types, ...$params);
                        if ($stmt->execute()) {
                            $success = "Account updated successfully!";
                            if ($form_data['name'] !== $username) {
                                $_SESSION['username'] = $form_data['name'];
                                $username = htmlspecialchars($form_data['name']);
                            }
                        } else {
                            $errors['general'] = "Error updating account: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $success = "No changes made.";
                    }
                }
            }
            $conn->close();
        }
    } catch (mysqli_sql_exception $e) {
        $errors['general'] = "Database error: " . $e->getMessage();
    }
    ?>

    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                <img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> <?php echo $username; ?>
            </a>
        </div>
    </header>

    <nav class="sidebar">
        <ul>
            <li><a href="userDashboard.php" class="sidebar-link">Booking Details</a></li>
            <li><a href="userFeedback.php" class="sidebar-link">Share Feedback</a></li>
            <li><a href="userAccountSettings.php" class="sidebar-link active">Account Settings</a></li>
            <li><a href="../index.php" class="sidebar-link">Search Bus</a></li>
            <li><a href="./userComplaint.php" class="sidebar-link">Complain</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section id="settings" class="section">
            <h2>Account Settings</h2>
            <?php if (!empty($success)): ?>
                <div style="color: green; text-align: center;">
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <div style="color: red; text-align: center;">
                    <p><?php echo htmlspecialchars($errors['general']); ?></p>
                </div>
            <?php endif; ?>
            <form class="settings-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <label for="name">Username:</label>
                <input type="text" id="name" name="name" placeholder="Enter your username" value="<?php echo htmlspecialchars($form_data['name']); ?>">
                <?php if (isset($errors['name'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['name']); ?></span>
                <?php endif; ?>

                <label for="current-password">Current Password:</label>
                <input type="password" id="current-password" name="current_password" placeholder="Enter current password">
                <?php if (isset($errors['current_password'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['current_password']); ?></span>
                <?php endif; ?>

                <label for="new-password">New Password:</label>
                <input type="password" id="new-password" name="new_password" placeholder="Enter new password">
                <?php if (isset($errors['new_password'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['new_password']); ?></span>
                <?php endif; ?>

                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm new password">
                <?php if (isset($errors['confirm_password'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
                <?php endif; ?>

                <button type="submit">Save Changes</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="footerContainer">
            <div class="footerSection">
                <h2>GO BUS</h2>
                <p>
                    gobus.com is a premium online booking portal which allows you to purchase ticket 
                    for various bus booking services locally across the country.
                </p>
            </div>

            <div class="footerSection">
                <h3>About GoBUS</h3>
                <a href="aboutUs.php">About Us</a>
                <a href="contact.php">Contact Us</a>
            </div>

            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="terms.php">Terms and Condition</a>
                <a href="privacy.php">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>
</body>
</html>
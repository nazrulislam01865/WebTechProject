
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <?php
    // Start session
    session_start();

    // Enable strict MySQLi error reporting (for consistency, though not used here)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Check if user is logged in
    $is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['username']);
    $username = $is_logged_in ? htmlspecialchars($_SESSION['username']) : '';
    ?>

    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <?php if ($is_logged_in): ?>
                <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                    <i class="fa-solid fa-user-circle"></i> <?php echo $username; ?>
                </a>
            <?php else: ?>
                <a href="./html/login.php" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
            <?php endif; ?>
        </div>
    </header>
    <img class="backgroundImage" src="picture/indexBackground.jpg" alt="Background Image">

    <div class="steps">
        <h2><span class="highlight">Buy ticket</span> in 3 easy steps</h2>
        <div class="stepContainer">
            <div class="step">
                <div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3>Search</h3>
                <p>Enter your starting point, destination, and travel date to explore available buses.</p>
                <div class="stepNumber">1</div>
            </div>

            <div class="step">
                <div class="icon"><i class="fa-solid fa-check"></i></div>
                <h3>Select</h3>
                <p>Choose your preferred bus and pick your seats.</p>
                <div class="stepNumber">2</div>
            </div>

            <div class="step">
                <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
                <h3>Pay</h3>
                <p>Complete your booking securely using cards, mobile banking or other payment options.</p>
                <div class="stepNumber">3</div>
            </div>
        </div>
    </div>

    <div class="search-box">
        <div class="select-type">
            <input type="radio" name="travel-type" value="One Way" required>One Way
            <input type="radio" name="travel-type" value="Round Way" required>Round Way
        </div>

        <div class="form-row">
            <input type="text" placeholder="Going From">
            <input type="text" placeholder="Going To">
        </div>

        <div class="form-row">
            <input type="date" placeholder="Journey Date">
            <input type="date" placeholder="Return Date">
        </div>

        <div class="trending">
            <p><b>Trending Searches:</b></p>
            <span>Dhaka → Rajshahi</span>
            <span>Dhaka → Barisal</span>
            <span>Dhaka → Cox's Bazar</span>
            <span>Dhaka → Chittagong</span>
        </div>
        <a href="searchBus.php"><button class="search-btn">Search Bus</button></a>
    </div>

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
                <a href="cancelTicket.php">Cancel Ticket</a>
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
</html> -->




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <?php
    // Start session
    session_start();

    // Enable strict MySQLi error reporting (for consistency, though not used here)
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Check if user is logged in
    $is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['username']);
    $username = $is_logged_in ? htmlspecialchars($_SESSION['username']) : '';
    $errors = [];
    $form_data = [
        'from' => $_POST['from'] ?? '',
        'to' => $_POST['to'] ?? '',
        'journey_date' => $_POST['journey_date'] ?? '',
        'return_date' => $_POST['return_date'] ?? '',
        'travel_type' => $_POST['travel_type'] ?? ''
    ];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $today = date('Y-m-d');
        $selected_type = $form_data['travel_type'];

        // Validate travel type selection
        if (empty($selected_type) || !in_array($selected_type, ['One Way', 'Round Way'])) {
            $errors['travel_type'] = "Please select One Way or Round Way.";
        }

        // Validate from and to
        if (empty($form_data['from'])) {
            $errors['from'] = "Going From is required.";
        }
        if (empty($form_data['to'])) {
            $errors['to'] = "Going To is required.";
        }

        // Validate journey date
        if (empty($form_data['journey_date'])) {
            $errors['journey_date'] = "Journey Date is required.";
        } elseif (strtotime($form_data['journey_date']) < strtotime($today)) {
            $errors['journey_date'] = "Journey Date must be today or in the future.";
        }

        // Validate return date only for Round Way
        if ($selected_type === 'Round Way') {
            if (empty($form_data['return_date'])) {
                $errors['return_date'] = "Return Date is required for Round Way.";
            } elseif (strtotime($form_data['return_date']) < strtotime($form_data['journey_date'])) {
                $errors['return_date'] = "Return Date must be on or after the Journey Date.";
            }
        }

        // If no errors, redirect to searchBus.php
        if (empty($errors)) {
            $query_params = "from=" . urlencode($form_data['from']) . "&to=" . urlencode($form_data['to']) . "&journey_date=" . urlencode($form_data['journey_date']) . "&travel_type=" . urlencode($selected_type);
            if ($selected_type === 'Round Way') {
                $query_params .= "&return_date=" . urlencode($form_data['return_date']);
            }
            header("Location: searchBus.php?" . $query_params);
            exit();
        }
    }
    ?>

    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <?php if ($is_logged_in): ?>
                <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                    <i class="fa-solid fa-user-circle"></i> <?php echo $username; ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="login-btn"><i class="fa-solid fa-user-circle"></i> Login</a>
            <?php endif; ?>
        </div>
    </header>
    <img class="backgroundImage" src="picture/indexBackground.jpg" alt="Background Image">

    <div class="steps">
        <h2><span class="highlight">Buy ticket</span> in 3 easy steps</h2>
        <div class="stepContainer">
            <div class="step">
                <div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3>Search</h3>
                <p>Enter your starting point, destination, and travel date to explore available buses.</p>
                <div class="stepNumber">1</div>
            </div>

            <div class="step">
                <div class="icon"><i class="fa-solid fa-check"></i></div>
                <h3>Select</h3>
                <p>Choose your preferred bus and pick your seats.</p>
                <div class="stepNumber">2</div>
            </div>

            <div class="step">
                <div class="icon"><i class="fa-solid fa-credit-card"></i></div>
                <h3>Pay</h3>
                <p>Complete your booking securely using cards, mobile banking or other payment options.</p>
                <div class="stepNumber">3</div>
            </div>
        </div>
    </div>

    <div class="search-box">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="select-type">
                <label>
                    <input type="radio" name="travel_type" value="One Way" <?php echo $form_data['travel_type'] === 'One Way' ? 'checked' : ''; ?> required> One Way
                </label>
                <label>
                    <input type="radio" name="travel_type" value="Round Way" <?php echo $form_data['travel_type'] === 'Round Way' ? 'checked' : ''; ?> required> Round Way
                </label>
                <?php if (isset($errors['travel_type'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['travel_type']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">

                <input type="text" name="from" placeholder="Going From" value="<?php echo htmlspecialchars($form_data['from']); ?>" >
                <?php if (isset($errors['from'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['from']); ?></span>
                <?php endif; ?>
                <input type="text" name="to" placeholder="Going To" value="<?php echo htmlspecialchars($form_data['to']); ?>" >
                <?php if (isset($errors['to'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['to']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <input type="date" name="journey_date" placeholder="Journey Date" value="<?php echo htmlspecialchars($form_data['journey_date']); ?>" min="<?php echo date('Y-m-d'); ?>" >
                <?php if (isset($errors['journey_date'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['journey_date']); ?></span>
                <?php endif; ?>
                <input type="date" name="return_date" placeholder="Return Date" value="<?php echo htmlspecialchars($form_data['return_date']); ?>" <?php echo $form_data['travel_type'] === 'Round Way' ? 'required' : 'disabled'; ?> <?php echo $form_data['travel_type'] === 'Round Way' && $form_data['journey_date'] ? 'min="' . htmlspecialchars($form_data['journey_date']) . '"' : ''; ?>>
                <?php if (isset($errors['return_date'])): ?>
                    <span style="color: red; font-size: 0.8em;"><?php echo htmlspecialchars($errors['return_date']); ?></span>
                <?php endif; ?>
            </div>

            <div class="trending">
                <p><b>Trending Searches:</b></p>
                <span>Dhaka → Rajshahi</span>
                <span>Dhaka → Barisal</span>
                <span>Dhaka → Cox's Bazar</span>
                <span>Dhaka → Chittagong</span>
            </div>
            <button type="submit" class="search-btn">Search Bus</button>
        </form>
    </div>

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
                <a href="cancelTicket.php">Cancel Ticket</a>
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

    <script>
        // Client-side validation for travel type
        document.querySelectorAll('input[name="travel_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const returnDateInput = document.querySelector('input[name="return_date"]');
                const journeyDateInput = document.querySelector('input[name="journey_date"]');
                if (this.value === 'Round Way') {
                    returnDateInput.disabled = false;
                    returnDateInput.required = true;
                    if (journeyDateInput.value) {
                        returnDateInput.min = journeyDateInput.value;
                    }
                } else {
                    returnDateInput.disabled = true;
                    returnDateInput.required = false;
                    returnDateInput.removeAttribute('min');
                    returnDateInput.value = ''; // Clear return date for One Way
                }
            });
        });

        // Update return date min when journey date changes
        document.querySelector('input[name="journey_date"]').addEventListener('change', function() {
            const returnDateInput = document.querySelector('input[name="return_date"]');
            if (document.querySelector('input[value="Round Way"]').checked) {
                returnDateInput.min = this.value;
            }
        });
    </script>
</body>
</html>
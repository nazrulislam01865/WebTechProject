
<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus</title>
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
                <a href="./html/login.php" class="login-btn"><img src="picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
            <?php endif; ?>
        </div>
    </header>
    <img class="backgroundImage" src="picture/indexBackground.jpg" alt="Background Image">

    <div class="steps">
        <h2><span class="highlight">Buy ticket</span> in 3 easy steps</h2>
        <div class="stepContainer">
            <div class="step">
                <div class="icon"><img src="picture/search_logo.png" alt="Search Icon" style="width: 25px; height: 25px; vertical-align: middle;"></div>
                <h3>Search</h3>
                <p>Enter your starting point, destination, and travel date to explore available buses.</p>
                <div class="stepNumber">1</div>
            </div>

            <div class="step">
                <div class="icon"><img src="picture/check.png" alt="Check Icon" style="width: 25px; height: 25px; vertical-align: middle;"></div>
                <h3>Select</h3>
                <p>Choose your preferred bus and pick your seats.</p>
                <div class="stepNumber">2</div>
            </div>

            <div class="step">
                <div class="icon"><img src="picture/card.png" alt="Card Icon" style="width: 25px; height: 24px; vertical-align: middle;"></div>
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
                    <input type="radio" name="travel_type" value="One Way" <?php echo $form_data['travel_type'] === 'One Way' ? 'checked' : ''; ?>> One Way
                </label>
                <label>
                    <input type="radio" name="travel_type" value="Round Way" <?php echo $form_data['travel_type'] === 'Round Way' ? 'checked' : ''; ?>> Round Way
                </label>
                <?php if (isset($errors['travel_type'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['travel_type']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <select name="from" class="form-input">
                        <option value="">Going From</option>
                        <option value="Dhaka" <?php echo htmlspecialchars($form_data['from']) === 'Dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                        <option value="Rajshahi" <?php echo htmlspecialchars($form_data['from']) === 'Rajshahi' ? 'selected' : ''; ?>>Rajshahi</option>
                        <option value="Barisal" <?php echo htmlspecialchars($form_data['from']) === 'Barisal' ? 'selected' : ''; ?>>Barisal</option>
                        <option value="Sylhet" <?php echo htmlspecialchars($form_data['from']) === 'Sylhet' ? 'selected' : ''; ?>>Sylhet</option>
                        <option value="Khulna" <?php echo htmlspecialchars($form_data['from']) === 'Khulna' ? 'selected' : ''; ?>>Khulna</option>
                        <option value="Mymensingh" <?php echo htmlspecialchars($form_data['from']) === 'Mymensingh' ? 'selected' : ''; ?>>Mymensingh</option>
                        <option value="Bandarban" <?php echo htmlspecialchars($form_data['from']) === 'Bandarban' ? 'selected' : ''; ?>>Bandarban</option>
                        <option value="Cox's Bazar" <?php echo htmlspecialchars($form_data['from']) === "Cox's Bazar" ? 'selected' : ''; ?>>Cox's Bazar</option>
                        <option value="Chittagong" <?php echo htmlspecialchars($form_data['from']) === 'Chittagong' ? 'selected' : ''; ?>>Chittagong</option>
                    </select>
                    <?php if (isset($errors['from'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['from']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <select name="to" class="form-input">
                        <option value="">Going To</option>
                        <option value="Dhaka" <?php echo htmlspecialchars($form_data['from']) === 'Dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                        <option value="Rajshahi" <?php echo htmlspecialchars($form_data['from']) === 'Rajshahi' ? 'selected' : ''; ?>>Rajshahi</option>
                        <option value="Barisal" <?php echo htmlspecialchars($form_data['from']) === 'Barisal' ? 'selected' : ''; ?>>Barisal</option>
                        <option value="Rangpur" <?php echo htmlspecialchars($form_data['from']) === 'Rangpur' ? 'selected' : ''; ?>>Rangpur</option>
                        <option value="Sylhet" <?php echo htmlspecialchars($form_data['from']) === 'Sylhet' ? 'selected' : ''; ?>>Sylhet</option>
                        <option value="Khulna" <?php echo htmlspecialchars($form_data['from']) === 'Khulna' ? 'selected' : ''; ?>>Khulna</option>
                        <option value="Mymensingh" <?php echo htmlspecialchars($form_data['from']) === 'Mymensingh' ? 'selected' : ''; ?>>Mymensingh</option>
                        <option value="Bandarban" <?php echo htmlspecialchars($form_data['from']) === 'Bandarban' ? 'selected' : ''; ?>>Bandarban</option>
                        <option value="Cox's Bazar" <?php echo htmlspecialchars($form_data['from']) === "Cox's Bazar" ? 'selected' : ''; ?>>Cox's Bazar</option>
                        <option value="Chittagong" <?php echo htmlspecialchars($form_data['from']) === 'Chittagong' ? 'selected' : ''; ?>>Chittagong</option>
                    </select>
                    <?php if (isset($errors['to'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['to']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <input type="date" name="journey_date" placeholder="Journey Date" value="<?php echo htmlspecialchars($form_data['journey_date']); ?>" min="<?php echo date('Y-m-d'); ?>" >
                    <?php if (isset($errors['journey_date'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['journey_date']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="date" name="return_date" placeholder="Return Date" value="<?php echo htmlspecialchars($form_data['return_date']); ?>" <?php echo $form_data['travel_type'] === 'Round Way' ? 'required' : 'disabled'; ?> <?php echo $form_data['travel_type'] === 'Round Way' && $form_data['journey_date'] ? 'min="' . htmlspecialchars($form_data['journey_date']) . '"' : ''; ?>>
                    <?php if (isset($errors['return_date'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['return_date']); ?></span>
                    <?php endif; ?>
                </div>
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
                <a href="./html/terms.php">Terms and Condition</a>
                <a href="privacy.php">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>

    <script>

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
                    returnDateInput.value = ''; 
                }
            });
        });

        document.querySelector('input[name="journey_date"]').addEventListener('change', function() {
            const returnDateInput = document.querySelector('input[name="return_date"]');
            if (document.querySelector('input[value="Round Way"]').checked) {
                returnDateInput.min = this.value;
            }
        });
    </script>
</body>
</html>  -->

<?php
ob_start();
session_start(); // Start session

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Enable strict MySQLi error reporting (for consistency)
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
    if ($form_data['from'] === $form_data['to'] && !empty($form_data['from']) && !empty($form_data['to'])) {
        $errors['to'] = "Going From and Going To cannot be the same.";
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

    if (empty($errors)) {
        $_SESSION['search_data'] = [
            'from' => $form_data['from'],
            'to' => $form_data['to'],
            'journey_date' => $form_data['journey_date'],
            'travel_type' => $selected_type,
            'return_date' => $form_data['return_date']
        ];

        // Debug log
        error_log("Redirecting to login.php with session data: " . print_r($_SESSION['search_data'], true));

        // Use absolute path for redirect
        header("Location: /webtech/WebTechProject/login.php");
        ob_end_flush();
        exit();
    } else {
        error_log("Validation errors: " . print_r($errors, true));
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <?php if ($is_logged_in): ?>
                <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                    <img src="picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> <?php echo $username; ?>
                </a>
            <?php else: ?>
                <a href="html/login.php" class="login-btn"><img src="picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
            <?php endif; ?>
        </div>
    </header>
    <img class="backgroundImage" src="picture/indexBackground.jpg" alt="Background Image">

    <div class="steps">
        <h2><span class="highlight">Buy ticket</span> in 3 easy steps</h2>
        <div class="stepContainer">
            <div class="step">
                <div class="icon"><img src="picture/search_logo.png" alt="Search Icon" style="width: 25px; height: 25px; vertical-align: middle;"></div>
                <h3>Search</h3>
                <p>Enter your starting point, destination, and travel date to explore available buses.</p>
                <div class="stepNumber">1</div>
            </div>

            <div class="step">
                <div class="icon"><img src="picture/check.png" alt="Check Icon" style="width: 25px; height: 25px; vertical-align: middle;"></div>
                <h3>Select</h3>
                <p>Choose your preferred bus and pick your seats.</p>
                <div class="stepNumber">2</div>
            </div>

            <div class="step">
                <div class="icon"><img src="picture/card.png" alt="Card Icon" style="width: 25px; height: 24px; vertical-align: middle;"></div>
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
                    <input type="radio" name="travel_type" value="One Way" <?php echo $form_data['travel_type'] === 'One Way' ? 'checked' : ''; ?>> One Way
                </label>
                <label>
                    <input type="radio" name="travel_type" value="Round Way" <?php echo $form_data['travel_type'] === 'Round Way' ? 'checked' : ''; ?>> Round Way
                </label>
                <?php if (isset($errors['travel_type'])): ?>
                    <div class="error"><?php echo htmlspecialchars($errors['travel_type']); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <select name="from" class="form-input">
                        <option value="">Going From</option>
                        <option value="Dhaka" <?php echo htmlspecialchars($form_data['from']) === 'Dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                        <option value="Rajshahi" <?php echo htmlspecialchars($form_data['from']) === 'Rajshahi' ? 'selected' : ''; ?>>Rajshahi</option>
                        <option value="Barisal" <?php echo htmlspecialchars($form_data['from']) === 'Barisal' ? 'selected' : ''; ?>>Barisal</option>
                        <option value="Sylhet" <?php echo htmlspecialchars($form_data['from']) === 'Sylhet' ? 'selected' : ''; ?>>Sylhet</option>
                        <option value="Khulna" <?php echo htmlspecialchars($form_data['from']) === 'Khulna' ? 'selected' : ''; ?>>Khulna</option>
                        <option value="Mymensingh" <?php echo htmlspecialchars($form_data['from']) === 'Mymensingh' ? 'selected' : ''; ?>>Mymensingh</option>
                        <option value="Bandarban" <?php echo htmlspecialchars($form_data['from']) === 'Bandarban' ? 'selected' : ''; ?>>Bandarban</option>
                        <option value="Cox's Bazar" <?php echo htmlspecialchars($form_data['from']) === "Cox's Bazar" ? 'selected' : ''; ?>>Cox's Bazar</option>
                        <option value="Chittagong" <?php echo htmlspecialchars($form_data['from']) === 'Chittagong' ? 'selected' : ''; ?>>Chittagong</option>
                    </select>
                    <?php if (isset($errors['from'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['from']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <select name="to" class="form-input">
                        <option value="">Going To</option>
                        <option value="Dhaka" <?php echo htmlspecialchars($form_data['to']) === 'Dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                        <option value="Rajshahi" <?php echo htmlspecialchars($form_data['to']) === 'Rajshahi' ? 'selected' : ''; ?>>Rajshahi</option>
                        <option value="Barisal" <?php echo htmlspecialchars($form_data['to']) === 'Barisal' ? 'selected' : ''; ?>>Barisal</option>
                        <option value="Rangpur" <?php echo htmlspecialchars($form_data['to']) === 'Rangpur' ? 'selected' : ''; ?>>Rangpur</option>
                        <option value="Sylhet" <?php echo htmlspecialchars($form_data['to']) === 'Sylhet' ? 'selected' : ''; ?>>Sylhet</option>
                        <option value="Khulna" <?php echo htmlspecialchars($form_data['to']) === 'Khulna' ? 'selected' : ''; ?>>Khulna</option>
                        <option value="Mymensingh" <?php echo htmlspecialchars($form_data['to']) === 'Mymensingh' ? 'selected' : ''; ?>>Mymensingh</option>
                        <option value="Bandarban" <?php echo htmlspecialchars($form_data['to']) === 'Bandarban' ? 'selected' : ''; ?>>Bandarban</option>
                        <option value="Cox's Bazar" <?php echo htmlspecialchars($form_data['to']) === "Cox's Bazar" ? 'selected' : ''; ?>>Cox's Bazar</option>
                        <option value="Chittagong" <?php echo htmlspecialchars($form_data['to']) === 'Chittagong' ? 'selected' : ''; ?>>Chittagong</option>
                    </select>
                    <?php if (isset($errors['to'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['to']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <input type="date" name="journey_date" placeholder="Journey Date" value="<?php echo htmlspecialchars($form_data['journey_date']); ?>" min="<?php echo date('Y-m-d'); ?>">
                    <?php if (isset($errors['journey_date'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['journey_date']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="date" name="return_date" placeholder="Return Date" value="<?php echo htmlspecialchars($form_data['return_date']); ?>" <?php echo $form_data['travel_type'] === 'Round Way' ? 'required' : 'disabled'; ?> <?php echo $form_data['travel_type'] === 'Round Way' && $form_data['journey_date'] ? 'min="' . htmlspecialchars($form_data['journey_date']) . '"' : ''; ?>>
                    <?php if (isset($errors['return_date'])): ?>
                        <span class="error"><?php echo htmlspecialchars($errors['return_date']); ?></span>
                    <?php endif; ?>
                </div>
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
                <a href="html/terms.php">Terms and Condition</a>
                <a href="privacy.php">Privacy Policy</a>
            </div>
        </div>

        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>

    <script>
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
                    returnDateInput.value = '';
                }
            });
        });

        document.querySelector('input[name="journey_date"]').addEventListener('change', function() {
            const returnDateInput = document.querySelector('input[name="return_date"]');
            if (document.querySelector('input[value="Round Way"]').checked) {
                returnDateInput.min = this.value;
            }
        });
    </script>
</body>
</html>
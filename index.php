<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

//Model
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

$promotions = [];
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "gobus";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    if ($conn->connect_error) {
        $errors['db'] = "Database connection failed: " . $conn->connect_error;
    } else {
        $sql = "SELECT promo_code, discount_type, discount_value, route FROM promotions";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $promotions[] = $row;
            }
        }
        $conn->close();
    }
} catch (mysqli_sql_exception $e) {
    $errors['db'] = "Database error: " . $e->getMessage();
}

//Controller
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $today = date('Y-m-d');
    $selected_type = $form_data['travel_type'];


    if (empty($selected_type) || !in_array($selected_type, ['One Way', 'Round Way'])) {
        $errors['travel_type'] = "Please select One Way or Round Way.";
    }

    if (empty($form_data['from'])) {
        $errors['from'] = "Going From is required.";
    }
    if (empty($form_data['to'])) {
        $errors['to'] = "Going To is required.";
    }

    if (empty($form_data['journey_date'])) {
        $errors['journey_date'] = "Journey Date is required.";
    } elseif (strtotime($form_data['journey_date']) < strtotime($today)) {
        $errors['journey_date'] = "Journey Date must be today or in the future.";
    }

    if ($selected_type === 'Round Way') {
        if (empty($form_data['return_date'])) {
            $errors['return_date'] = "Return Date is required for Round Way.";
        } elseif (strtotime($form_data['return_date']) < strtotime($form_data['journey_date'])) {
            $errors['return_date'] = "Return Date must be on or after the Journey Date.";
        }
    }

    if (empty($errors)) {
        $_SESSION['search_data'] = $form_data;
        $query_params = http_build_query([
            'from' => $form_data['from'],
            'to' => $form_data['to'],
            'journey_date' => $form_data['journey_date'],
            'travel_type' => $selected_type,
            'return_date' => $selected_type === 'Round Way' ? $form_data['return_date'] : ''
        ]);
        header("Location: ./screens/searchBus.php?" . $query_params);
        exit();
    }
}
?>

<!-- VIEW -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .dashboard-btn {
            margin-left: 10px;
            padding: 8px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            background-color: #007bff;
            transition: background-color 0.3s;
        }
        .dashboard-btn:hover {
            background-color: #0056b3;
        }
        .promotions-banner {
            margin: 20px auto;
            max-width: 1650px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            white-space: nowrap;
            font-size: 16px;
        }
        .promotions-banner .scroll-container {
            display: flex;
            animation: scroll 20s linear infinite;
        }
        .promotions-banner .promotion-item {
            flex: 0 0 auto;
            margin-right: 30px;
            color: #333;
        }
        .promotions-banner .promotion-item span {
            color: #007bff;
            font-weight: bold;
        }
        .promotions-banner .no-promotions {
            color: #777;
            font-size: 16px;
            text-align: center;
        }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .promotions-banner:hover .scroll-container {
            /* animation-play-state: paused; */
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <?php if ($is_logged_in): ?>
                <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='./screens/logout.php' : false;">
                    <img src="picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> <?php echo $username; ?>
                </a>
                <a href="./screens/userDashboard.php" class="dashboard-btn">Dashboard</a>
            <?php else: ?>
                <a href="./screens/login.php" class="login-btn"><img src="picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
            <?php endif; ?>
        </div>
    </header>
    <img class="backgroundImage" src="picture/indexBackground.jpg" alt="Background Image">

    <div class="promotions-banner">
        <?php if (empty($promotions)): ?>
            <p class="no-promotions">No promotions available at this time.</p>
        <?php else: ?>
            <div class="scroll-container">
                <?php foreach ($promotions as $promo): ?>
                    <div class="promotion-item">
                        <?php echo htmlspecialchars($promo['discount_type'] === 'Percentage' ? $promo['discount_value'] . '% off' : '৳' . $promo['discount_value'] . ' off'); ?> 
                        on <?php echo htmlspecialchars($promo['route']); ?>. Use this promo code: 
                        <span><?php echo htmlspecialchars($promo['promo_code']); ?></span> 
                    </div>
                <?php endforeach; ?>
                <?php foreach ($promotions as $promo): ?>
                    <div class="promotion-item">
                        <?php echo htmlspecialchars($promo['discount_type'] === 'Percentage' ? $promo['discount_value'] . '% off' : '৳' . $promo['discount_value'] . ' off'); ?> 
                        on <?php echo htmlspecialchars($promo['route']); ?>. Use this promo code: 
                        <span><?php echo htmlspecialchars($promo['promo_code']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

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
                <a href="./screens/aboutUs.php">About Us</a>
                <a href="./screens/contact.php">Contact Us</a>
                <a href="./screens/cancelTicket.php">Cancel Ticket</a>
            </div>
            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="./screens/terms.php">Terms and Condition</a>
                <a href="./screens/privacy.php">Privacy Policy</a>
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

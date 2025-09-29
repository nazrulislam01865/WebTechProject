<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug: Log session booking data
error_log("Session booking data on payment.php: " . print_r($_SESSION['booking_data'], true));

// Check if user is logged in and booking data exists
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    error_log("Invalid access: user_id or booking_data missing");
    echo "<script>alert('Invalid access. Please select a seat first.'); window.location.href='searchBus.php';</script>";
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate unique booking ID
function generateBookingId($conn) {
    do {
        $prefix = 'BK';
        $randomNum = mt_rand(10000, 99999);
        $booking_id = $prefix . $randomNum;
        $check_sql = "SELECT booking_id FROM bookings WHERE booking_id = '$booking_id'";
        $check_result = $conn->query($check_sql);
    } while ($check_result && $check_result->num_rows > 0);
    return $booking_id;
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $booking_data = $_SESSION['booking_data'];
    $user_id = $booking_data['user_id'];
    $bus_id = $conn->real_escape_string($booking_data['bus_id']);
    $seat_number = $conn->real_escape_string($booking_data['seat_number']);
    $phone_number = $conn->real_escape_string($booking_data['phone_number']);
    $boarding_point = $conn->real_escape_string($booking_data['boarding_point']);
    $dropping_point = $conn->real_escape_string($booking_data['dropping_point']);
    $route = $conn->real_escape_string($booking_data['route']);
    $journey_date = $conn->real_escape_string($booking_data['journey_date']);
    $fare = (float)$conn->real_escape_string($booking_data['fare']);
    $promo_code = $conn->real_escape_string($booking_data['promo_code'] ?? '');
    $operator_name = isset($booking_data['operator_name']) && $booking_data['operator_name'] !== null 
        ? $conn->real_escape_string($booking_data['operator_name']) 
        : 'Unknown Operator'; // Fallback if operator_name is missing

    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $transaction_id = $payment_method === 'mobile_banking' ? $conn->real_escape_string($_POST['transaction_id']) : NULL;

    // Debug: Log payment details
    error_log("Payment submission: user_id=$user_id, bus_id=$bus_id, fare=$fare, promo_code=$promo_code, operator_name=$operator_name, payment_method=$payment_method, transaction_id=" . ($transaction_id ?? 'None'));

    // Validate payment method
    if (!in_array($payment_method, ['credit_card', 'mobile_banking'])) {
        error_log("Invalid payment method: $payment_method");
        echo "<script>alert('Invalid payment method.');</script>";
        exit;
    }

    // Validate transaction ID for mobile banking
    if ($payment_method === 'mobile_banking' && empty($transaction_id)) {
        error_log("Missing transaction ID for mobile banking");
        echo "<script>alert('Transaction ID is required for mobile banking.');</script>";
        exit;
    }

    // Validate fare
    if (!is_numeric($fare) || $fare < 0) {
        error_log("Invalid fare amount: $fare");
        echo "<script>alert('Invalid fare amount.');</script>";
        exit;
    }

    // Validate operator_name
    if ($operator_name === 'Unknown Operator') {
        error_log("Warning: Operator name is missing or invalid for bus_id: $bus_id");
        echo "<script>alert('Operator name is missing. Please try again.'); window.location.href='searchBus.php';</script>";
        unset($_SESSION['booking_data']);
        exit;
    }

    // Check if seat is still available
    $check_seat = "SELECT * FROM bookings WHERE bus_id = '$bus_id' AND date = '$journey_date' AND seat_number = '$seat_number' AND status = 'Upcoming'";
    $seat_result = $conn->query($check_seat);
    if (!$seat_result) {
        error_log("Error checking seat availability: " . $conn->error);
        echo "<script>alert('Error checking seat availability: " . $conn->error . "');</script>";
        exit;
    }
    if ($seat_result->num_rows > 0) {
        error_log("Seat already booked: bus_id=$bus_id, seat_number=$seat_number, date=$journey_date");
        echo "<script>alert('This seat is already booked. Please select another seat.'); window.location.href='searchBus.php';</script>";
        unset($_SESSION['booking_data']);
        exit;
    }

    // Check if seats are available
    $check_seats = "SELECT seats_available FROM buses WHERE id = '$bus_id' AND journey_date = '$journey_date'";
    $seats_result = $conn->query($check_seats);
    if ($seats_result && $seats_result->num_rows > 0 && $seats_result->fetch_assoc()['seats_available'] > 0) {
        // Generate booking ID
        $booking_id = generateBookingId($conn);

        // Insert booking into bookings table
        $insert_booking = "INSERT INTO bookings (user_id, bus_id, booking_id, route, date, status, seat_number, phone_number, boarding_point, dropping_point, fare, payment_method, transaction_id, operator_name, promo_code) 
                           VALUES ('$user_id', '$bus_id', '$booking_id', '$route', '$journey_date', 'Upcoming', '$seat_number', '$phone_number', '$boarding_point', '$dropping_point', '$fare', '$payment_method', " . ($transaction_id ? "'$transaction_id'" : "NULL") . ", '$operator_name', " . ($promo_code ? "'$promo_code'" : "NULL") . ")";
        error_log("Insert booking query: $insert_booking");
        if ($conn->query($insert_booking)) {
            // Update seats_available in buses table
            $update_seats = "UPDATE buses SET seats_available = seats_available - 1 WHERE id = '$bus_id' AND journey_date = '$journey_date'";
            if ($conn->query($update_seats)) {
                // Clear booking data from session
                unset($_SESSION['booking_data']);
                echo "<script>alert('Payment successful! Booking confirmed. Booking ID: $booking_id'); window.location.href='userDashboard.php';</script>";
            } else {
                error_log("Error updating seats: " . $conn->error);
                echo "<script>alert('Error updating seats: " . $conn->error . "');</script>";
            }
        } else {
            error_log("Error booking seat: " . $conn->error);
            echo "<script>alert('Error booking seat: " . $conn->error . "');</script>";
        }
    } else {
        error_log("No seats available for bus_id: $bus_id, date: $journey_date");
        echo "<script>alert('No seats available for this bus.'); window.location.href='searchBus.php';</script>";
        unset($_SESSION['booking_data']);
    }
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBUS | Payment</title>
    <link rel="stylesheet" type="text/css" href="../css/payment.css">
    <style>
        .booking-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        .detail-item {
            flex: 1 1 300px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-item span {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .detail-item span strong {
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Go<span id="logo">Bus</span></div>
        <div class="header-right">
            <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
            <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
            </a>
        </div>
    </header>

    <div class="container">
        <h1>Payment Details</h1>
        <div class="payment-box">
            <h2>Booking Summary</h2>
            <div class="booking-details">
                <div class="detail-item">
                    <span><strong>Operator:</strong> <?php echo htmlspecialchars($_SESSION['booking_data']['operator_name'] ?? 'Unknown Operator'); ?></span>
                    <span><strong>Route:</strong> <?php echo htmlspecialchars($_SESSION['booking_data']['route'] ?? 'N/A'); ?></span>
                    <span><strong>Journey Date:</strong> <?php echo htmlspecialchars(isset($_SESSION['booking_data']['journey_date']) ? date('d M Y', strtotime($_SESSION['booking_data']['journey_date'])) : 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span><strong>Seat Number:</strong> <?php echo htmlspecialchars($_SESSION['booking_data']['seat_number'] ?? 'N/A'); ?></span>
                    <span><strong>Boarding Point:</strong> <?php echo htmlspecialchars($_SESSION['booking_data']['boarding_point'] ?? 'N/A'); ?></span>
                    <span><strong>Dropping Point:</strong> <?php echo htmlspecialchars($_SESSION['booking_data']['dropping_point'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span><strong>Original Fare:</strong> <?php echo number_format($_SESSION['booking_data']['original_fare'] ?? $_SESSION['booking_data']['fare'] ?? 0, 2); ?> tk</span>
                    <?php if (isset($_SESSION['booking_data']['discount_value']) && $_SESSION['booking_data']['discount_value'] > 0 && !empty($_SESSION['booking_data']['promo_code'])): ?>
                        <span><strong>Discount (<?php echo $_SESSION['booking_data']['discount_type'] === 'Percentage' ? number_format($_SESSION['booking_data']['discount_value'], 2) . '%' : number_format($_SESSION['booking_data']['discount_value'], 2) . ' Tk'; ?> with <?php echo htmlspecialchars($_SESSION['booking_data']['promo_code']); ?>):</strong> -<?php echo number_format(($_SESSION['booking_data']['original_fare'] ?? $_SESSION['booking_data']['fare']) - $_SESSION['booking_data']['fare'], 2); ?> tk</span>
                    <?php else: ?>
                        <span><strong>Discount:</strong> 0 tk</span>
                    <?php endif; ?>
                    <span><strong>Final Fare:</strong> <?php echo number_format($_SESSION['booking_data']['fare'] ?? 0, 2); ?> tk</span>
                    <span><strong>Phone Number:</strong> <?php echo htmlspecialchars($_SESSION['booking_data']['phone_number'] ?? 'N/A'); ?></span>
                </div>
            </div>

            <h2>Payment Information</h2>
            <form method="POST" action="" class="payment-form">
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="credit_card">Credit/Debit Card</option>
                        <option value="mobile_banking">Mobile Banking (bKash, Nagad, etc.)</option>
                    </select>
                </div>
                <div id="card-details" class="form-group">
                    <div>
                        <label class="form-label">Card Number</label>
                        <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" pattern="\d{16}" maxlength="16">
                    </div>
                    <div class="form-row">
                        <div>
                            <label class="form-label">Expiry Date</label>
                            <input type="text" name="expiry_date" class="form-control" placeholder="MM/YY" pattern="\d{2}/\d{2}">
                        </div>
                        <div>
                            <label class="form-label">CVV</label>
                            <input type="text" name="cvv" class="form-control" placeholder="123" pattern="\d{3}" maxlength="3">
                        </div>
                    </div>
                </div>
                <div id="mobile-banking-details" class="form-group hidden">
                    <div>
                        <label class="form-label">Mobile Banking Number</label>
                        <input type="text" name="mobile_banking_number" class="form-control" placeholder="+8801234567890" pattern="\+?[0-9]{10,14}">
                    </div>
                    <div>
                        <label class="form-label">Transaction ID</label>
                        <input type="text" name="transaction_id" class="form-control" placeholder="Enter Transaction ID">
                    </div>
                </div>
                <button type="submit" name="confirm_payment" class="submit-btn">Confirm Payment</button>
            </form>
            <p class="terms">By confirming, you agree to the <a href="terms.php">Terms & Conditions</a> and <a href="../privacy.php">Privacy Notice</a> of GoBUS.</p>
        </div>
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
                <a href="../index.php">Home</a>
                <a href="./aboutUs.php">About Us</a>
                <a href="./contact.php">Contact Us</a>
                
            </div>
            <div class="footerSection">
                <h3>Company Info</h3>
                <a href="./cancelTicket.php">Cancel Ticket</a>
                <a href="terms.php">Terms and Condition</a>
                <a href="./privacy.php">Privacy Policy</a>
            </div>
        </div>
        <div class="footerBottom">
            Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
        </div>
    </footer>

    <script>
        // Toggle payment method fields
        const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
        const cardDetails = document.getElementById('card-details');
        const mobileBankingDetails = document.getElementById('mobile-banking-details');

        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'credit_card') {
                cardDetails.classList.remove('hidden');
                mobileBankingDetails.classList.add('hidden');
                // Make card fields required
                document.querySelector('input[name="card_number"]').required = true;
                document.querySelector('input[name="expiry_date"]').required = true;
                document.querySelector('input[name="cvv"]').required = true;
                document.querySelector('input[name="mobile_banking_number"]').required = false;
                document.querySelector('input[name="transaction_id"]').required = false;
            } else if (this.value === 'mobile_banking') {
                cardDetails.classList.add('hidden');
                mobileBankingDetails.classList.remove('hidden');
                // Make mobile banking fields required
                document.querySelector('input[name="card_number"]').required = false;
                document.querySelector('input[name="expiry_date"]').required = false;
                document.querySelector('input[name="cvv"]').required = false;
                document.querySelector('input[name="mobile_banking_number"]').required = true;
                document.querySelector('input[name="transaction_id"]').required = true;
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
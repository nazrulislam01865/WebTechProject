<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Model
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

$from = isset($_GET['from']) ? $conn->real_escape_string($_GET['from']) : 'Dhaka';
$to = isset($_GET['to']) ? $conn->real_escape_string($_GET['to']) : 'Barisal';
$journey_date = isset($_GET['journey_date']) ? $conn->real_escape_string($_GET['journey_date']) : date('Y-m-d');
$travel_type = isset($_GET['travel_type']) ? $conn->real_escape_string($_GET['travel_type']) : 'One Way';
$return_date = isset($_GET['return_date']) ? $conn->real_escape_string($_GET['return_date']) : '';

$_SESSION['search_data'] = [
    'from' => $from,
    'to' => $to,
    'journey_date' => $journey_date,
    'travel_type' => $travel_type,
    'return_date' => $return_date
];

$formatted_journey_date = date('d M Y', strtotime($journey_date));

$sql = "SELECT * FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'";
$result = $conn->query($sql);

if (!$result) {
    error_log("Query failed: " . $conn->error);
    die("Query failed: " . $conn->error);
}

$total_buses = $result->num_rows;
$total_operators = $conn->query("SELECT COUNT(DISTINCT operator_name) as count FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'")->fetch_assoc()['count'];
$total_seats = 0;
if ($total_buses > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_seats += (int)$row['seats_available'];
    }
    $result->data_seek(0);
}

//Controller
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    error_log("Booking form submitted with POST data: " . print_r($_POST, true));

    if (!isset($_SESSION['user_id'])) {
        error_log("User not logged in, redirecting to login.php");
        echo "<script>alert('Please log in to book a seat.'); window.location.href='login.php';</script>";
        exit;
    }

    if (!isset($_POST['bus_id']) || !isset($_POST['seat_number']) || !isset($_POST['phone_number']) || !isset($_POST['boarding_point']) || !isset($_POST['dropping_point'])) {
        error_log("Missing required form fields: " . print_r($_POST, true));
        echo "<script>alert('Please fill in all required fields.'); window.location.href='searchBus.php';</script>";
        exit;
    }

    $bus_id = $conn->real_escape_string($_POST['bus_id']);
    $seat_number = $conn->real_escape_string($_POST['seat_number']);
    $raw_phone = trim($_POST['phone_number']);
    $phone_number = $conn->real_escape_string($raw_phone);
    $boarding_point = $conn->real_escape_string($_POST['boarding_point']);
    $dropping_point = $conn->real_escape_string($_POST['dropping_point']);
    $promo_code = isset($_POST['promo_code']) ? $conn->real_escape_string($_POST['promo_code']) : '';
    $route = "$from To $to";

    if (empty($raw_phone)) {
        error_log("Phone number is empty");
        echo "<script>alert('Phone number is required.'); window.location.href='searchBus.php';</script>";
        exit;
    }

    if (!preg_match('/^(\+880)?01[3-9]\d{8}$/', $raw_phone)) {
        error_log("Invalid phone number format: $raw_phone");
        echo "<script>alert('Please enter a valid Bangladeshi mobile number (e.g., 01712345678 or +8801712345678).'); window.location.href='searchBus.php';</script>";
        exit;
    }

    $check_seat = "SELECT * FROM bookings WHERE bus_id = '$bus_id' AND date = '$journey_date' AND seat_number = '$seat_number' AND status = 'Upcoming'";
    $seat_result = $conn->query($check_seat);
    if (!$seat_result) {
        error_log("Error checking seat availability: " . $conn->error);
        echo "<script>alert('Error checking seat availability: " . $conn->error . "'); window.location.href='searchBus.php';</script>";
        exit;
    }
    if ($seat_result->num_rows > 0) {
        error_log("Seat already booked: bus_id=$bus_id, seat_number=$seat_number, date=$journey_date");
        echo "<script>alert('This seat is already booked.'); window.location.href='searchBus.php';</script>";
        exit;
    }


    $check_seats = "SELECT seats_available, fare, operator_name FROM buses WHERE id = '$bus_id' AND journey_date = '$journey_date'";
    $seats_result = $conn->query($check_seats);
    if ($seats_result && $seats_result->num_rows > 0) {
        $bus_data = $seats_result->fetch_assoc();
        if ($bus_data['seats_available'] > 0) {
            $fare = (float)$bus_data['fare'];
            $discount_value = 0;
            $discount_type = '';

            error_log("Initial fare for bus_id $bus_id: $fare");

            if (!empty($promo_code)) {
                $promo_sql = "SELECT discount_type, discount_value, route FROM promotions WHERE promo_code = '$promo_code'";
                error_log("Promo code query: $promo_sql");
                $promo_result = $conn->query($promo_sql);
                if ($promo_result && $promo_result->num_rows > 0) {
                    $promo_data = $promo_result->fetch_assoc();
                    $promo_route = $promo_data['route'];

                    $normalized_route = str_replace(' To ', '-', $route);

                    if (strpos($promo_route, 'routes') !== false) {
                        $route_base = str_replace(' routes', '', $promo_route);
                        if (strpos($route, $route_base) === false && strpos($normalized_route, $route_base) === false) {
                            error_log("Promo code '$promo_code' invalid for route: $route (expected: $promo_route)");
                            echo "<script>alert('Promo code is not valid for this route.'); window.location.href='searchBus.php';</script>";
                            exit;
                        }
                    } else if ($promo_route !== $route && $promo_route !== $normalized_route) {
                        error_log("Promo code '$promo_code' invalid for route: $route (expected: $promo_route)");
                        echo "<script>alert('Promo code is not valid for this route.'); window.location.href='searchBus.php';</script>";
                        exit;
                    }

                    $discount_type = $promo_data['discount_type'];
                    $discount_value = (float)$promo_data['discount_value'];
                    if ($discount_type === 'Percentage') {
                        $fare = $fare - ($fare * $discount_value / 100);
                    } else if ($discount_type === 'Fixed Amount') {
                        $fare = $fare - $discount_value;
                    }
                    if ($fare < 0) $fare = 0;
                    error_log("Applied promo code '$promo_code': discount_type=$discount_type, discount_value=$discount_value, final_fare=$fare");
                } else {
                    error_log("Invalid promo code: $promo_code");
                    echo "<script>alert('Invalid promo code.'); window.location.href='searchBus.php';</script>";
                    exit;
                }
            }

            $_SESSION['booking_data'] = [
                'user_id' => $_SESSION['user_id'],
                'bus_id' => $bus_id,
                'seat_number' => $seat_number,
                'phone_number' => $phone_number,
                'boarding_point' => $boarding_point,
                'dropping_point' => $dropping_point,
                'route' => $route,
                'journey_date' => $journey_date,
                'fare' => $fare,
                'original_fare' => (float)$bus_data['fare'],
                'discount_value' => $discount_value,
                'discount_type' => $discount_type,
                'promo_code' => $promo_code,
                'operator_name' => $bus_data['operator_name'] ?? 'Unknown Operator'
            ];

            error_log("Booking data stored in session: " . print_r($_SESSION['booking_data'], true));

            header('Location: payment.php');
            echo "<script>window.location.href='payment.php';</script>";
            exit;
        } else {
            error_log("No seats available for bus_id: $bus_id, date: $journey_date");
            echo "<script>alert('No seats available for this bus.'); window.location.href='searchBus.php';</script>";
            exit;
        }
    } else {
        error_log("Error fetching bus details: " . $conn->error);
        echo "<script>alert('Error fetching bus details: " . $conn->error . "'); window.location.href='searchBus.php';</script>";
        exit;
    }
}
?>

<!-- VIEW -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GoBUS | Search Bus</title>
        <link rel="stylesheet" type="text/css" href="../css/searchBus.css">
        <style>
            .promo-number {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 10px 0;
                visibility: visible !important;
                opacity: 1 !important;
            }
            #applyPromoBtn {
                display: inline-block !important;
                visibility: visible !important;
                opacity: 1 !important;
                padding: 8px 16px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            #applyPromoBtn:hover {
                background-color: #0056b3;
            }
            .seat-selection-container {
                display: none;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .seat-info {
                margin-top: 10px;
            }
            #seatFare {
                font-weight: bold;
                color: #333;
            }
            .submit-btn {
                padding: 10px 20px;
                background-color: #28a745;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            .submit-btn:hover {
                background-color: #218838;
            }
        </style>
    </head>
    <body>
        <header>
            <div class="logo">Go<span id="logo">Bus</span></div>
            <div class="header-right">
                <a href="tel:+8801234567890" class="call-btn">Call +8801234567890</a>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
                    <a href="#" class="logout-btn" onclick="return confirm('Do you want to log out?') ? window.location.href='logout.php' : false;">
                        <img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                <?php else: ?>
                    <a href="./login.php" class="login-btn"><img src="../picture/user_logo.png" alt="User Icon" style="width: 18px; height: 18px; vertical-align: middle;"> Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="search-info">
            <div class="search-details">
                <span>ONWARD</span>
                <span><?php echo htmlspecialchars("$from To $to On $formatted_journey_date"); ?></span>
                <?php if ($travel_type === 'Round Way' && $return_date): ?>
                    <span>RETURN</span>
                    <span><?php echo htmlspecialchars("$to To $from On " . date('d M Y', strtotime($return_date))); ?></span>
                <?php endif; ?>
            </div>
            <div class="modify-search">
                <button onclick="window.location.href='../index.php'">MODIFY SEARCH</button>
            </div>
            <div class="stats">
                <span>Total Buses Found: <?php echo $total_buses; ?></span>
                <span>Total Operators Found: <?php echo $total_operators; ?></span>
                <span>Total Seats Available: <?php echo $total_seats; ?></span>
            </div>
        </div>

        <div class="container">
            <?php
            if ($total_buses > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bus_id = $row['id'];
                    $fare = (float)$row['fare'];
                    error_log("Bus ID $bus_id fare: $fare");
            ?>
            <div class="bus-details" data-bus-id="<?php echo $bus_id; ?>" data-fare="<?php echo $fare; ?>">
                <li>
                    <div class="bus-details-new-left">
                        <h3><?php echo htmlspecialchars($row['operator_name']); ?></h3>
                        <h6><?php echo htmlspecialchars($row['bus_number']); ?></h6>
                        <div class="non-ac-bus_couch-type">
                            <img src="../picture/snowflake.png" alt="AC Icon" style="width: 14px; height: 14px; vertical-align: middle;"><span> <?php echo htmlspecialchars($row['bus_type']); ?></span>
                        </div>
                        <a href="#">Cancellation policy</a>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-middle">
                        <div class="middle-left">
                            <h6>Starting</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['starting_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['starting_point']); ?></h6>
                        </div>
                        <div class="middle-middle">
                            <div class="bus-image">
                                <img src="../picture/bus.png" alt="Bus Image">
                            </div>
                            <h6 class="seat-left">Seat left: <?php echo htmlspecialchars($row['seats_available']); ?></h6>
                        </div>
                        <div class="middle-right">
                            <h6>Arrival</h6>
                            <h5><?php echo htmlspecialchars(date('h:i A', strtotime($row['arrival_time']))); ?></h5>
                            <h6><?php echo htmlspecialchars($row['destination']); ?></h6>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="bus-details-new-right">
                        <div class="no-extra-charge">No Extra Charge</div>
                        <div class="price">
                            <h3><?php echo number_format($fare, 2); ?> tk</h3>
                        </div>
                        <div class="view-seat">
                            <button class="view-seat-btn" data-bus-id="<?php echo $bus_id; ?>" <?php echo $row['seats_available'] == 0 ? 'disabled' : ''; ?>>View Seat</button>
                        </div>
                    </div>
                </li>
            </div>
            <?php
                }
            } else {
                echo '<p>No buses found for the selected route.</p>';
            }
            $conn->close();
            ?>

            <div id="seatSelectionContainer" class="seat-selection-container" style="display: none;">
                <div class="seat-legend">
                    <span class="legend-item booked-m"><i class="fa-solid fa-sofa"></i>BOOKED</span>
                    <span class="legend-item blocked">BLOCKED</span>
                    <span class="legend-item available">AVAILABLE</span>
                    <span class="legend-item selected">SELECTED</span>
                    <span class="legend-item sold-m">SOLD</span>
                </div>
                <div class="seat-selection-content">
                    <ul class="seat-layout">
                    </ul>
                    <div class="selection-details">
                        <form id="bookingForm" method="POST" action="">
                            <input type="hidden" name="bus_id" id="bus_id">
                            <input type="hidden" name="seat_number" id="seat_number">
                            <div class="boarding-dropping">
                                <div>
                                    <label>BOARDING POINT*</label>
                                    <select name="boarding_point" required>
                                        <?php
                                        $conn = new mysqli($servername, $username, $password, $dbname);
                                        if ($conn->connect_error) {
                                            die("Connection failed: " . $conn->connect_error);
                                        }
                                        $sql_boarding = "SELECT DISTINCT starting_point, starting_time FROM buses WHERE starting_point = '$from' AND destination = '$to' AND journey_date = '$journey_date'";
                                        $result_boarding = $conn->query($sql_boarding);
                                        if ($result_boarding && $result_boarding->num_rows > 0) {
                                            while ($row_boarding = $result_boarding->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row_boarding['starting_point']) . '">' . htmlspecialchars(date('h:i A', strtotime($row_boarding['starting_time']))) . ' - ' . htmlspecialchars($row_boarding['starting_point']) . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">No boarding points available</option>';
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label>DROPPING POINT*</label>
                                    <select name="dropping_point" required>
                                        <option value="select">Select dropping point</option>
                                        <option value="<?php echo htmlspecialchars($to); ?>"><?php echo htmlspecialchars($to); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mobile-number">
                                <label>PHONE NUMBER*</label>
                                <input type="text" name="phone_number" placeholder="Enter phone number">
                            </div>
                            <div class="promo-number">
                                <label>Promo Code</label>
                                <input type="text" name="promo_code" id="promo_code" placeholder="Enter promo code">
                                <button type="button" id="applyPromoBtn">Apply</button>
                            </div>
                            <button type="submit" name="submit_booking" class="submit-btn">Proceed to Payment</button>
                        </form>
                        <p>I have already have an account. <a href="login.php">Login with password</a>.</p>
                        <p>By logging in you are agreeing to the <a href="terms.php">Terms & Conditions</a> and <a href="../privacy.php">Privacy Notice of GoBUS</a></p>
                        <div class="seat-info">
                            <p>SEAT INFORMATION:</p>
                            <p>Seat Fare: <span id="seatFare">0 Tk</span></p>
                            <p>Service Charge: 0 Tk</p>
                            <p>PGW Charge: 0 Tk</p>
                            <p id="discountInfo" style="display: none;">Discount Applied: <span id="discountAmount"></span></p>
                        </div>
                    </div>
                </div>
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
                    <a href="../aboutUs.php">About Us</a>
                    <a href="../contact.php">Contact Us</a>
                    <a href="../cancelTicket.php">Cancel Ticket</a>
                </div>
                <div class="footerSection">
                    <h3>Company Info</h3>
                    <a href="terms.php">Terms and Condition</a>
                    <a href="../privacy.php">Privacy Policy</a>
                </div>
            </div>
            <div class="footerBottom">
                Copyright &copy;2025 | All Rights Reserved Designed by <span class="designer">Group 1</span>
            </div>
        </footer>

        <script>
            let originalFare = 0;

            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                console.log('Booking form submitted with values:', new FormData(this));
            });

            document.querySelectorAll('.view-seat-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const busId = this.getAttribute('data-bus-id');
                    console.log('View Seat button clicked, bus_id:', busId);
                    const seatContainer = document.getElementById('seatSelectionContainer');
                    const isVisible = seatContainer.style.display === 'block';
                    seatContainer.style.display = isVisible ? 'none' : 'block';
                    console.log('Seat selection container visibility:', seatContainer.style.display);

                    if (!isVisible) {
                        const busDetails = this.closest('.bus-details');
                        const selectedPrice = parseFloat(busDetails.getAttribute('data-fare'));
                        console.log('Fetched fare from data-fare:', selectedPrice);
                        originalFare = selectedPrice;
                        const seatFareP = document.getElementById('seatFare');
                        const busIdInput = document.getElementById('bus_id');
                        busIdInput.value = busId;

                        console.log('Fetching reserved seats for bus_id:', busId, 'journey_date:', '<?php echo $journey_date; ?>');
                        fetch(`get_reserved_seats.php?bus_id=${busId}&journey_date=<?php echo $journey_date; ?>`)
                            .then(response => {
                                console.log('Fetch response status:', response.status);
                                if (!response.ok) {
                                    throw new Error('Network response was not ok: ' + response.statusText);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Received data:', data);
                                if (data.error) {
                                    console.error('Server error:', data.error);
                                    alert('Error fetching reserved seats: ' + data.error);
                                    return;
                                }
                                const reservedSeats = data.reserved_seats || [];
                                const seatLayout = document.querySelector('.seat-layout');
                                seatLayout.innerHTML = '';
                                for (let i = 0; i < 40; i++) {
                                    const row = Math.floor(i / 4);
                                    const col = i % 4;
                                    const seat = document.createElement('li');
                                    seat.classList.add('seat');
                                    const seatNumber = String.fromCharCode(65 + row) + (col + 1);

                                    let gridCol = col + 1;
                                    if (col >= 2) {
                                        gridCol += 1;
                                    }
                                    seat.style.gridRow = row + 1;
                                    seat.style.gridColumn = gridCol;

                                    seat.dataset.seatNumber = seatNumber;
                                    seat.textContent = seatNumber;
                                    if (reservedSeats.includes(seatNumber)) {
                                        seat.classList.add('booked-m');
                                    } else {
                                        seat.classList.add('available');
                                    }

                                    seatLayout.appendChild(seat);
                                }

                                const allSeats = seatLayout.querySelectorAll('.seat');
                                allSeats.forEach(seat => {
                                    seat.addEventListener('click', function() {
                                        console.log('Seat clicked:', this.dataset.seatNumber);
                                        if (this.classList.contains('available') || this.classList.contains('selected')) {
                                            if (this.classList.contains('selected')) {
                                                this.classList.remove('selected');
                                                this.classList.add('available');
                                                seatFareP.textContent = '0 Tk';
                                                document.getElementById('seat_number').value = '';
                                                document.getElementById('discountInfo').style.display = 'none';
                                            } else {
                                                document.querySelectorAll('.seat.selected').forEach(s => {
                                                    s.classList.remove('selected');
                                                    s.classList.add('available');
                                                });
                                                this.classList.remove('available');
                                                this.classList.add('selected');
                                                seatFareP.textContent = originalFare.toFixed(2) + ' Tk';
                                                console.log('Displaying fare in seatFare:', originalFare.toFixed(2));
                                                document.getElementById('seat_number').value = this.dataset.seatNumber;
                                                document.getElementById('promo_code').value = '';
                                                document.getElementById('discountInfo').style.display = 'none';
                                            }
                                        }
                                    });
                                });
                                const applyPromoBtn = document.getElementById('applyPromoBtn');
                                console.log('Apply promo button exists:', !!applyPromoBtn);
                                if (applyPromoBtn) {
                                    applyPromoBtn.style.display = 'inline-block';
                                    applyPromoBtn.style.visibility = 'visible';
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                alert('Failed to load seat data: ' + error.message);
                            });
                    }
                });
            });

            const applyPromoBtn = document.getElementById('applyPromoBtn');
            if (applyPromoBtn) {
                console.log('Apply promo button found on page load');
                applyPromoBtn.addEventListener('click', function() {
                    console.log('Apply promo button clicked');
                    const promoCodeInput = document.getElementById('promo_code');
                    const promoCode = promoCodeInput.value.trim();
                    const seatFareP = document.getElementById('seatFare');
                    const discountInfo = document.getElementById('discountInfo');
                    const discountAmount = document.getElementById('discountAmount');
                    const route = '<?php echo "$from To $to"; ?>';

                    if (!promoCode) {
                        alert('Please enter a promo code.');
                        return;
                    }

                    console.log('Applying promo code:', promoCode, 'for route:', route, 'original fare:', originalFare);
                    fetch('validate_promo.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'promo_code=' + encodeURIComponent(promoCode) + '&route=' + encodeURIComponent(route)
                    })
                    .then(response => {
                        console.log('Fetch response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Promo validation response:', data);
                        if (data.success) {
                            const discountValue = parseFloat(data.discount_value);
                            let discountedFare = originalFare;
                            if (data.discount_type === 'Percentage') {
                                discountedFare = originalFare - (originalFare * discountValue / 100);
                                discountAmount.textContent = discountValue.toFixed(2) + '%';
                            } else {
                                discountedFare = originalFare - discountValue;
                                discountAmount.textContent = discountValue.toFixed(2) + ' Tk';
                            }
                            if (discountedFare < 0) discountedFare = 0;
                            seatFareP.textContent = discountedFare.toFixed(2) + ' Tk';
                            console.log('Displaying discounted fare:', discountedFare.toFixed(2));
                            discountInfo.style.display = 'block';
                        } else {
                            alert(data.message || 'Invalid promo code or route.');
                            promoCodeInput.value = '';
                            seatFareP.textContent = originalFare.toFixed(2) + ' Tk';
                            console.log('Reverting to original fare:', originalFare.toFixed(2));
                            discountInfo.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error validating promo code:', error);
                        alert('Failed to validate promo code.');
                        promoCodeInput.value = '';
                        seatFareP.textContent = originalFare.toFixed(2) + ' Tk';
                        console.log('Reverting to original fare on error:', originalFare.toFixed(2));
                        discountInfo.style.display = 'none';
                    });
                });
            } else {
                console.error('Apply promo button not found on page load');
            }
        </script>
    </body>
</html>
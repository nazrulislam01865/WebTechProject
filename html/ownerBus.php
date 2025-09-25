<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['company_name'])) {
    header("Location: login.php");
    exit();
}

$company_name = htmlspecialchars($_SESSION['company_name']);
$errors = [];
$success_message = [];
$today_trips = 0;
$total_revenue = 0;
$upcoming_trips = [];
$passengers = [];
$drivers = [];

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "gobus";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $errors['general'] = "Database connection failed: " . $conn->connect_error;
    } else {
        // Fetch today's trips
        $sql = "SELECT COUNT(DISTINCT b.id) as trip_count 
                FROM buses b 
                WHERE b.operator_name = ? AND DATE(b.journey_date) = CURDATE()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $today_trips = $row['trip_count'];
        }
        $stmt->close();

        // Fetch total revenue from bookings table
        $sql = "SELECT COALESCE(SUM(fare), 0) as total_fare 
                FROM bookings 
                WHERE operator_name = ? AND status IN ('Upcoming', 'Completed')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $total_revenue = $row['total_fare'];
        }
        $stmt->close();

        // Fetch revenue data for graph (daily revenue)
        $revenue_data = [];
        $sql = "SELECT DATE(date) as booking_date, COALESCE(SUM(fare), 0) as daily_revenue 
                FROM bookings 
                WHERE operator_name = ? AND status IN ('Upcoming', 'Completed')
                GROUP BY DATE(date) 
                ORDER BY booking_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $revenue_data[] = [
                'date' => $row['booking_date'],
                'revenue' => $row['daily_revenue']
            ];
        }
        $stmt->close();

        // Check bookings table row count
        $sql = "SELECT COUNT(*) as row_count FROM bookings";
        $result = $conn->query($sql);
        $row_count = $result->fetch_assoc()['row_count'];

        // Fetch upcoming trips
        $sql = "SELECT id, bus_number, starting_point, destination, starting_time, arrival_time, journey_date, bus_type, seats_available, fare 
                FROM buses 
                WHERE operator_name = ? AND journey_date >= CURDATE() 
                ORDER BY journey_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $upcoming_trips[] = $row;
        }
        $stmt->close();

        // Fetch drivers
        $sql = "SELECT id, name, license_number, phone FROM drivers WHERE company_id = 
                (SELECT id FROM bus_companies WHERE company_name = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }
        $stmt->close();

        // Handle Add Trip Form
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_trip'])) {
            $starting_point = trim($_POST['trip-from'] ?? '');
            $destination = trim($_POST['trip-to'] ?? '');
            $starting_time = $_POST['starting-time'] ?? '';
            $arrival_time = $_POST['arrival-time'] ?? '';
            $journey_date = $_POST['trip-date'] ?? '';
            $bus_type = $_POST['bus-type'] ?? '';
            $seats = trim($_POST['seat-number'] ?? '');
            $fare = trim($_POST['fare'] ?? '');
            $bus_number = "BUS-" . strtoupper(substr(md5(uniqid()), 0, 6));

            // Validations
            if (empty($starting_point)) {
                $errors['trip-from'] = "Starting point is required.";
            } elseif (strlen($starting_point) > 100) {
                $errors['trip-from'] = "Starting point must be 100 characters or less.";
            }
            if (empty($destination)) {
                $errors['trip-to'] = "Destination is required.";
            } elseif (strlen($destination) > 100) {
                $errors['trip-to'] = "Destination must be 100 characters or less.";
            }
            if (empty($starting_time)) {
                $errors['starting-time'] = "Starting time is required.";
            }
            if (empty($arrival_time)) {
                $errors['arrival-time'] = "Arrival time is required.";
            }
            if (empty($journey_date)) {
                $errors['trip-date'] = "Journey date is required.";
            } elseif (strtotime($journey_date) < strtotime('today')) {
                $errors['trip-date'] = "Journey date cannot be in the past.";
            }
            if (!in_array($bus_type, ['AC', 'Non AC'])) {
                $errors['bus-type'] = "Invalid bus type.";
            }
            if (empty($seats)) {
                $errors['seat-number'] = "Number of seats is required.";
            } elseif (!is_numeric($seats) || $seats <= 0 || $seats > 100) {
                $errors['seat-number'] = "Seats must be a number between 1 and 100.";
            }
            if (empty($fare)) {
                $errors['fare'] = "Fare is required.";
            } elseif (!is_numeric($fare) || $fare <= 0) {
                $errors['fare'] = "Fare must be a positive number.";
            }

            // Insert trip if no errors
            if (empty($errors)) {
                $sql = "INSERT INTO buses (operator_name, bus_number, bus_type, starting_point, destination, starting_time, arrival_time, fare, seats_available, journey_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssdis", $company_name, $bus_number, $bus_type, $starting_point, $destination, $starting_time, $arrival_time, $fare, $seats, $journey_date);
                if ($stmt->execute()) {
                    $success_message['trip'] = "Trip added successfully!";
                    // Refresh upcoming trips
                    $sql = "SELECT id, bus_number, starting_point, destination, starting_time, arrival_time, journey_date, bus_type, seats_available, fare 
                            FROM buses 
                            WHERE operator_name = ? AND journey_date >= CURDATE() 
                            ORDER BY journey_date ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $company_name);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $upcoming_trips = [];
                    while ($row = $result->fetch_assoc()) {
                        $upcoming_trips[] = $row;
                    }
                    // Set active tab in sessionStorage
                    echo "<script>sessionStorage.setItem('activeTab', 'dashboard');</script>";
                } else {
                    $errors['general'] = "Error adding trip: " . $conn->error;
                }
                $stmt->close();
            }
        }

        // Handle Add Driver Form
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_driver'])) {
            $driver_name = trim($_POST['driver-name'] ?? '');
            $license_number = trim($_POST['driver-license'] ?? '');
            $phone = trim($_POST['driver-number'] ?? '');

            // Validations
            if (empty($driver_name)) {
                $errors['driver-name'] = "Driver name is required.";
            } elseif (strlen($driver_name) > 100) {
                $errors['driver-name'] = "Driver name must be 100 characters or less.";
            }
            if (empty($license_number)) {
                $errors['driver-license'] = "License number is required.";
            } elseif (strlen($license_number) > 50) {
                $errors['driver-license'] = "License number must be 50 characters or less.";
            } else {
                // Check for unique license number
                $sql = "SELECT id FROM drivers WHERE license_number = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $license_number);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $errors['driver-license'] = "License number already exists.";
                }
                $stmt->close();
            }
            if (empty($phone)) {
                $errors['driver-number'] = "Phone number is required.";
            } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
                $errors['driver-number'] = "Phone number must be 11 digits.";
            }

            // Insert driver if no errors
            if (empty($errors)) {
                $sql = "INSERT INTO drivers (company_id, name, license_number, phone) 
                        VALUES ((SELECT id FROM bus_companies WHERE company_name = ?), ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $company_name, $driver_name, $license_number, $phone);
                if ($stmt->execute()) {
                    $success_message['driver'] = "Driver added successfully!";
                    // Refresh drivers
                    $sql = "SELECT id, name, license_number, phone FROM drivers WHERE company_id = 
                            (SELECT id FROM bus_companies WHERE company_name = ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $company_name);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $drivers = [];
                    while ($row = $result->fetch_assoc()) {
                        $drivers[] = $row;
                    }
                    // Set active tab in sessionStorage
                    echo "<script>sessionStorage.setItem('activeTab', 'drivers');</script>";
                } else {
                    $errors['general'] = "Error adding driver: " . $conn->error;
                }
                $stmt->close();
            }
        }

        // Handle Passenger Search (Individual)
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_passenger'])) {
            $nid = trim($_POST['passenger-search-nid'] ?? '');
            $name = trim($_POST['passenger-search-name'] ?? '');
            $date = $_POST['passenger-search-date'] ?? '';

            // Validations
            if (empty($nid)) {
                $errors['passenger-search-nid'] = "NID is required.";
            } elseif (!preg_match('/^[0-9]{10}$/', $nid)) {
                $errors['passenger-search-nid'] = "NID must be 10 digits.";
            }
            if (empty($name)) {
                $errors['passenger-search-name'] = "Passenger name is required.";
            }
            if (empty($date)) {
                $errors['passenger-search-date'] = "Date is required.";
            }

            if (empty($errors)) {
                $sql = "SELECT u.username, u.nid, bk.booking_id, bk.route, bk.date, bk.seat_number 
                        FROM bookings bk 
                        JOIN users u ON bk.user_id = u.id 
                        JOIN buses b ON bk.bus_id = b.id 
                        WHERE b.operator_name = ? AND u.nid = ? AND u.username LIKE ? AND bk.date = ?";
                $stmt = $conn->prepare($sql);
                $name_like = "%$name%";
                $stmt->bind_param("ssss", $company_name, $nid, $name_like, $date);
                $stmt->execute();
                $result = $stmt->get_result();
                $passengers = [];
                while ($row = $result->fetch_assoc()) {
                    $passengers[] = $row;
                }
                // Set active tab in sessionStorage
                echo "<script>sessionStorage.setItem('activeTab', 'passengers');</script>";
                $stmt->close();
            }
        }

        // Handle Passenger Search (Date)
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_date'])) {
            $from = trim($_POST['search-from'] ?? '');
            $to = trim($_POST['search-To'] ?? '');
            $date = $_POST['search-date'] ?? '';

            // Validations
            if (empty($from)) {
                $errors['search-from'] = "Starting point is required.";
            }
            if (empty($to)) {
                $errors['search-To'] = "Destination is required.";
            }
            if (empty($date)) {
                $errors['search-date'] = "Date is required.";
            }

            if (empty($errors)) {
                $sql = "SELECT u.username, u.nid, bk.booking_id, bk.route, bk.date, bk.seat_number 
                        FROM bookings bk 
                        JOIN users u ON bk.user_id = u.id 
                        JOIN buses b ON bk.bus_id = b.id 
                        WHERE b.operator_name = ? AND bk.route LIKE ? AND bk.date = ?";
                $stmt = $conn->prepare($sql);
                $route_like = "%$from%$to%";
                $stmt->bind_param("sss", $company_name, $route_like, $date);
                $stmt->execute();
                $result = $stmt->get_result();
                $passengers = [];
                while ($row = $result->fetch_assoc()) {
                    $passengers[] = $row;
                }
                // Set active tab in sessionStorage
                echo "<script>sessionStorage.setItem('activeTab', 'passengers');</script>";
                $stmt->close();
            }
        }

        // Handle Cancel Trip
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_trip'])) {
            $bus_number = trim($_POST['bus-number'] ?? '');
            $search_date = $_POST['search-date'] ?? '';
            $cancel_reason = $_POST['cancelReason'] ?? '';
            $other_reason = trim($_POST['otherReason'] ?? '');
            $email_notif = isset($_POST['emailNotif']) ? 1 : 0;
            $sms_notif = isset($_POST['smsNotif']) ? 1 : 0;

            // Validations
            if (empty($bus_number)) {
                $errors['bus-number'] = "Bus number is required.";
            }
            if (empty($search_date)) {
                $errors['search-date'] = "Date is required.";
            }
            if (empty($cancel_reason)) {
                $errors['cancelReason'] = "Cancellation reason is required.";
            }
            if ($cancel_reason === 'Other' && empty($other_reason)) {
                $errors['otherReason'] = "Please specify the reason for cancellation.";
            }

            if (empty($errors)) {
                // Check if the bus exists for the given operator and date
                $sql = "SELECT id FROM buses WHERE operator_name = ? AND bus_number = ? AND journey_date = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $company_name, $bus_number, $search_date);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    $errors['bus-number'] = "No bus found for the specified number and date.";
                } else {
                    // Update bus status to Cancelled
                    $sql = "DELETE FROM buses WHERE operator_name = ? AND bus_number = ? AND journey_date = ?;";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $company_name, $bus_number, $search_date);
                    if ($stmt->execute()) {
                        $success_message['cancel'] = "Trip cancelled successfully!";
                        // Update bookings to Cancelled
                        $sql = "UPDATE bookings SET status = 'Cancelled' WHERE bus_id IN 
                                (SELECT id FROM buses WHERE operator_name = ? AND bus_number = ? AND journey_date = ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $company_name, $bus_number, $search_date);
                        $stmt->execute();
                        // Refresh upcoming trips
                        $sql = "SELECT id, bus_number, starting_point, destination, starting_time, arrival_time, journey_date, bus_type, seats_available, fare 
                                FROM buses 
                                WHERE operator_name = ? AND journey_date >= CURDATE() 
                                ORDER BY journey_date ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $company_name);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $upcoming_trips = [];
                        while ($row = $result->fetch_assoc()) {
                            $upcoming_trips[] = $row;
                        }
                        // Set active tab in sessionStorage
                    } else {
                        $errors['general'] = "Error cancelling trip: " . $conn->error;
                    }
                }
                $stmt->close();
            }
        }

        $conn->close();
    }
} catch (mysqli_sql_exception $e) {
    $errors['general'] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus - Bus Company Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles11.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h2>GoBus</h2>
        </div>
        <div class="nav-links">
            <div class="nav-item" data-tab="dashboard"><img src="../picture/home.png" alt="Home Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Dashboard</span></div>
            <div class="nav-item" data-tab="passengers"><img src="../picture/user_group.png" alt="User List Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Passenger List</span></div>
            <div class="nav-item" data-tab="revenue"><img src="../picture/chart.png" alt="Chart Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Revenue Report</span></div>
            <div class="nav-item" data-tab="drivers"><img src="../picture/driver.png" alt="License Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Assign Drivers</span></div>
            <div class="nav-item" data-tab="trips"><img src="../picture/busCancel.png" alt="Cancel Bus Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Cancel Trip</span></div>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header22">
                <h2>Bus Company Dashboard</h2>
            </div>
            <div class="user-profile" id="userProfile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($company_name); ?>&background=2563eb&color=fff" alt="User">
                <div>
                    <div><?php echo $company_name; ?></div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <form id="changePasswordForm" method="POST" action="updatePassword.php">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if (isset($errors['general'])): ?>
            <div style="color: red; text-align: center;">
                <p><?php echo htmlspecialchars($errors['general']); ?></p>
            </div>
        <?php endif; ?>

        <div class="content-tabs" id="dashboard">
            <div class="dashboard-cards">
                <div class="card stat-card">
                    <div class="label">Today's Trips</div>
                    <div class="value"><?php echo htmlspecialchars($today_trips); ?></div>
                    <div class="icon"><img src="../picture/path.png" alt="Money Icon" style="width: 35px; height: 35px; vertical-align: middle;"></div>
                </div>
                <div class="card stat-card">
                    <div class="label">Total Revenue</div>
                    <div class="value"><?php echo htmlspecialchars(number_format($total_revenue, 2)); ?> Tk</div>
                    <div class="icon"><img src="../picture/taka.png" alt="Money Icon" style="width: 24px; height: 24px; vertical-align: middle;"></div>
                </div>
            </div>

            <div class="add-trip-form">
                <h3>Add New Trip</h3>
                <?php if (isset($success_message['trip'])): ?>
                    <div style="color: green; text-align: center;">
                        <p><?php echo htmlspecialchars($success_message['trip']); ?></p>
                    </div>
                <?php endif; ?>
                <form id="trip" method="POST" action="">
                    <input type="hidden" name="add_trip" value="1">
                    <input type="hidden" name="current_tab" value="dashboard">
                    <div class="form-group">
                        <label for="trip-from">Starting:</label>
                        <input type="text" id="trip-from" name="trip-from" placeholder="From" value="<?php echo isset($_POST['trip-from']) ? htmlspecialchars($_POST['trip-from']) : ''; ?>">
                        <?php if (isset($errors['trip-from'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['trip-from']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="starting-time">Starting Time:</label>
                        <input type="time" id="starting-time" name="starting-time" value="<?php echo isset($_POST['starting-time']) ? htmlspecialchars($_POST['starting-time']) : ''; ?>">
                        <?php if (isset($errors['starting-time'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['starting-time']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="trip-to">Arrival:</label>
                        <input type="text" id="trip-to" name="trip-to" placeholder="To" value="<?php echo isset($_POST['trip-to']) ? htmlspecialchars($_POST['trip-to']) : ''; ?>">
                        <?php if (isset($errors['trip-to'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['trip-to']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="arrival-time">Arrival Time:</label>
                        <input type="time" id="arrival-time" name="arrival-time" value="<?php echo isset($_POST['arrival-time']) ? htmlspecialchars($_POST['arrival-time']) : ''; ?>">
                        <?php if (isset($errors['arrival-time'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['arrival-time']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="trip-date">Date:</label>
                        <input type="date" id="trip-date" name="trip-date" value="<?php echo isset($_POST['trip-date']) ? htmlspecialchars($_POST['trip-date']) : ''; ?>">
                        <?php if (isset($errors['trip-date'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['trip-date']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="bus-type">Bus Type:</label>
                        <div class="bus-options">
                            <input type="radio" id="ac" name="bus-type" value="AC" <?php echo (isset($_POST['bus-type']) && $_POST['bus-type'] === 'AC') ? 'checked' : ''; ?>><label for="ac">AC</label>
                            <input type="radio" id="non-ac" name="bus-type" value="Non AC" <?php echo (isset($_POST['bus-type']) && $_POST['bus-type'] === 'Non AC') ? 'checked' : ''; ?>><label for="non-ac">Non AC</label>
                        </div>
                        <?php if (isset($errors['bus-type'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['bus-type']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="seat-number">Number of Seats:</label>
                        <input type="text" id="seat-number" name="seat-number" placeholder="Enter Number of Seats" value="<?php echo isset($_POST['seat-number']) ? htmlspecialchars($_POST['seat-number']) : ''; ?>">
                        <?php if (isset($errors['seat-number'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['seat-number']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="fare">Fare (Tk):</label>
                        <input type="text" id="fare" name="fare" placeholder="Enter Fare" value="<?php echo isset($_POST['fare']) ? htmlspecialchars($_POST['fare']) : ''; ?>">
                        <?php if (isset($errors['fare'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['fare']); ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="add-trip-btn">Add Trip</button>
                </form>
            </div>

            <div class="trip-list" id="upcoming-trips">
                <h3 class="section-title">Upcoming Trips</h3>
                <?php if (empty($upcoming_trips)): ?>
                    <p>No upcoming trips found.</p>
                <?php else: ?>
                    <?php foreach ($upcoming_trips as $trip): ?>
                        <div class="trip-item">
                            <p><strong>Bus Number:</strong> <?php echo htmlspecialchars($trip['bus_number']); ?></p>
                            <p><strong>Route:</strong> <?php echo htmlspecialchars($trip['starting_point'] . ' to ' . $trip['destination']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($trip['journey_date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($trip['starting_time'] . ' - ' . $trip['arrival_time']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($trip['bus_type']); ?></p>
                            <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($trip['seats_available']); ?></p>
                            <p><strong>Fare:</strong> <?php echo htmlspecialchars($trip['fare']); ?> Tk</p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-tabs" id="passengers">
            <div class="card">
                <h3 class="section-title">Passenger List</h3>
                <div class="search-options">
                    <button class="search-option-btn active" data-search-type="basic">Individual Passenger Search</button>
                    <button class="search-option-btn" data-search-type="date">Passenger List Search</button>
                </div>
                <div class="search-form active" id="basic-search-form">
                    <form method="POST" action="">
                        <input type="hidden" name="search_passenger" value="1">
                        <input type="hidden" name="current_tab" value="passengers">
                        <div class="form-group">
                            <input type="text" id="passenger-search-nid" name="passenger-search-nid" placeholder="Passenger NID Number" value="<?php echo isset($_POST['passenger-search-nid']) ? htmlspecialchars($_POST['passenger-search-nid']) : ''; ?>">
                            <?php if (isset($errors['passenger-search-nid'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['passenger-search-nid']); ?></div>
                            <?php endif; ?>
                            <input type="text" id="passenger-search-name" name="passenger-search-name" placeholder="Passenger Name" value="<?php echo isset($_POST['passenger-search-name']) ? htmlspecialchars($_POST['passenger-search-name']) : ''; ?>">
                            <?php if (isset($errors['passenger-search-name'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['passenger-search-name']); ?></div>
                            <?php endif; ?>
                            <input type="date" id="passenger-search-date" name="passenger-search-date" value="<?php echo isset($_POST['passenger-search-date']) ? htmlspecialchars($_POST['passenger-search-date']) : ''; ?>">
                            <?php if (isset($errors['passenger-search-date'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['passenger-search-date']); ?></div>
                            <?php endif; ?>
                            <button type="submit">Search</button>
                        </div>
                    </form>
                    <?php if (!empty($passengers)): ?>
                        <div class="passenger-list">
                            <?php foreach ($passengers as $passenger): ?>
                                <div class="passenger-item">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($passenger['username']); ?></p>
                                    <p><strong>NID:</strong> <?php echo htmlspecialchars($passenger['nid']); ?></p>
                                    <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($passenger['booking_id']); ?></p>
                                    <p><strong>Route:</strong> <?php echo htmlspecialchars($passenger['route']); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($passenger['date']); ?></p>
                                    <p><strong>Seat:</strong> <?php echo htmlspecialchars($passenger['seat_number']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['search_passenger']) || isset($_POST['search_date']))): ?>
                        <p>No passengers found.</p>
                    <?php endif; ?>
                </div>
                <div class="search-form" id="date-search-form">
                    <form method="POST" action="">
                        <input type="hidden" name="search_date" value="1">
                        <input type="hidden" name="current_tab" value="passengers">
                        <div class="form-group">
                            <input type="text" id="search-from" name="search-from" placeholder="From" value="<?php echo isset($_POST['search-from']) ? htmlspecialchars($_POST['search-from']) : ''; ?>">
                            <?php if (isset($errors['search-from'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['search-from']); ?></div>
                            <?php endif; ?>
                            <input type="text" id="search-To" name="search-To" placeholder="To" value="<?php echo isset($_POST['search-To']) ? htmlspecialchars($_POST['search-To']) : ''; ?>">
                            <?php if (isset($errors['search-To'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['search-To']); ?></div>
                            <?php endif; ?>
                            <input type="date" id="search-date" name="search-date" value="<?php echo isset($_POST['search-date']) ? htmlspecialchars($_POST['search-date']) : ''; ?>">
                            <?php if (isset($errors['search-date'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['search-date']); ?></div>
                            <?php endif; ?>
                            <button type="submit">Search List</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="content-tabs" id="revenue">
            <div class="card">
                <h3 class="section-title">Revenue Report</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="content-tabs" id="drivers">
            <div class="add-trip-form">
                <h3>Add New Driver</h3>
                <?php if (isset($success_message['driver'])): ?>
                    <div style="color: green; text-align: center;">
                        <p><?php echo htmlspecialchars($success_message['driver']); ?></p>
                    </div>
                <?php endif; ?>
                <form id="driverForm" method="POST" action="">
                    <input type="hidden" name="add_driver" value="1">
                    <input type="hidden" name="current_tab" value="drivers">
                    <input type="text" id="driver-name" name="driver-name" placeholder="Driver Full Name" value="<?php echo isset($_POST['driver-name']) ? htmlspecialchars($_POST['driver-name']) : ''; ?>">
                    <?php if (isset($errors['driver-name'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['driver-name']); ?></div>
                    <?php endif; ?>
                    <input type="text" id="driver-license" name="driver-license" placeholder="License Number" value="<?php echo isset($_POST['driver-license']) ? htmlspecialchars($_POST['driver-license']) : ''; ?>">
                    <?php if (isset($errors['driver-license'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['driver-license']); ?></div>
                    <?php endif; ?>
                    <input type="number" id="driver-number" name="driver-number" placeholder="Phone Number" value="<?php echo isset($_POST['driver-number']) ? htmlspecialchars($_POST['driver-number']) : ''; ?>">
                    <?php if (isset($errors['driver-number'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['driver-number']); ?></div>
                    <?php endif; ?>
                    <button type="submit">Add Driver</button>
                </form>
            </div>
            <div class="driver-list">
                <h3 class="section-title">Assign Driver</h3>
                <div id="driver-list">
                    <?php if (empty($drivers)): ?>
                        <p>No drivers found.</p>
                    <?php else: ?>
                        <?php foreach ($drivers as $driver): ?>
                            <div class="driver-item">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['name']); ?></p>
                                <p><strong>License:</strong> <?php echo htmlspecialchars($driver['license_number']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($driver['phone']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-tabs" id="trips">
            <div class="add-trip-form">
                <h3>Cancel Trip</h3>
                <?php if (isset($success_message['cancel'])): ?>
                    <div style="color: green; text-align: center;">
                        <p><?php echo htmlspecialchars($success_message['cancel']); ?></p>
                    </div>
                <?php endif; ?>
                <form id="searchTripForm" method="POST" action="">
                    <input type="hidden" name="cancel_trip" value="1">
                    <input type="hidden" name="current_tab" value="trips">
                    <div class="form-group">
                        <label for="bus-number">Bus Number:</label>
                        <input type="text" id="bus-number" name="bus-number" placeholder="Enter Bus Number" value="<?php echo isset($_POST['bus-number']) ? htmlspecialchars($_POST['bus-number']) : ''; ?>">
                        <?php if (isset($errors['bus-number'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['bus-number']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="search-date">Date:</label>
                        <input type="date" id="search-date" name="search-date" value="<?php echo isset($_POST['search-date']) ? htmlspecialchars($_POST['search-date']) : ''; ?>">
                        <?php if (isset($errors['search-date'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['search-date']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="cancelReason">Reason for cancellation</label>
                        <select id="cancelReason" name="cancelReason">
                            <option value="" disabled selected>Select a reason</option>
                            <option value="Mechanical issues" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Mechanical issues') ? 'selected' : ''; ?>>Mechanical issues</option>
                            <option value="Driver unavailable" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Driver unavailable') ? 'selected' : ''; ?>>Driver unavailable</option>
                            <option value="Weather conditions" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Weather conditions') ? 'selected' : ''; ?>>Weather conditions</option>
                            <option value="Other" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['cancelReason'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['cancelReason']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group" id="otherReasonDiv" style="display:<?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Other') ? 'block' : 'none'; ?>;">
                        <label for="otherReason">Please specify</label>
                        <textarea id="otherReason" name="otherReason" rows="3" placeholder="Enter your reason"><?php echo isset($_POST['otherReason']) ? htmlspecialchars($_POST['otherReason']) : ''; ?></textarea>
                        <?php if (isset($errors['otherReason'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['otherReason']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Notify passengers via</label>
                        <div>
                            <input type="checkbox" id="emailNotif" name="emailNotif" <?php echo (isset($_POST['emailNotif']) && $_POST['emailNotif']) ? 'checked' : ''; ?>> <label for="emailNotif">Email</label>
                            <input type="checkbox" id="smsNotif" name="smsNotif" <?php echo (isset($_POST['smsNotif']) && $_POST['smsNotif']) ? 'checked' : ''; ?>> <label for="smsNotif">SMS</label>
                        </div>
                    </div>
                    <button type="submit" class="cancel-btn">Cancel Trip</button>
                </form>
                <div class="trip-list" id="trip-list">
                    <h3 class="section-title">All Trips</h3>
                    <?php if (empty($upcoming_trips)): ?>
                        <p>No trips found.</p>
                    <?php else: ?>
                        <?php foreach ($upcoming_trips as $trip): ?>
                            <div class="trip-item">
                                <p><strong>Bus Number:</strong> <?php echo htmlspecialchars($trip['bus_number']); ?></p>
                                <p><strong>Route:</strong> <?php echo htmlspecialchars($trip['starting_point'] . ' to ' . $trip['destination']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($trip['journey_date']); ?></p>
                                <p><strong>Time:</strong> <?php echo htmlspecialchars($trip['starting_time'] . ' - ' . $trip['arrival_time']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($trip['bus_type']); ?></p>
                                <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($trip['seats_available']); ?></p>
                                <p><strong>Fare:</strong> <?php echo htmlspecialchars($trip['fare']); ?> Tk</p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab navigation
        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = sessionStorage.getItem('activeTab');
            if (activeTab && document.getElementById(activeTab)) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.content-tabs').forEach(tab => tab.classList.remove('active'));
                document.getElementById(activeTab).classList.add('active');
                document.querySelector(`.nav-item[data-tab="${activeTab}"]`).classList.add('active');
            } else {
                sessionStorage.setItem('activeTab', 'dashboard');
                document.getElementById('dashboard').classList.add('active');
                document.querySelector('.nav-item[data-tab="dashboard"]').classList.add('active');
            }

            // Logout confirmation on clicking user profile
            const userProfile = document.getElementById('userProfile');
            userProfile.addEventListener('click', (event) => {
                // Prevent opening the dropdown if clicking to logout
                event.stopPropagation();
                if (confirm('Are you sure you want to log out?')) {
                    window.location.href = 'logout.php';
                }
            });

            // Toggle password form visibility on clicking profile dropdown
            const profileDropdown = document.getElementById('profileDropdown');
            const changePasswordForm = document.getElementById('changePasswordForm');
            userProfile.addEventListener('click', (event) => {
                profileDropdown.classList.toggle('show');
                event.stopPropagation(); // Prevent document click from immediately closing
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!userProfile.contains(event.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        });

        // Revenue Chart Initialization
        const revenueData = <?php echo json_encode($revenue_data); ?>;
        const maxRows = 10000; // Maximum row count for bookings table
        const initialRowCount = <?php echo $row_count; ?>;
        let revenueChart;

        function initializeChart(data) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Daily Revenue (Tk)',
                        data: data.map(item => item.revenue),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Revenue (Tk)'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Fetch updated revenue data via AJAX
        function fetchRevenueData() {
            fetch('fetch_revenue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ company_name: '<?php echo $company_name; ?>' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error fetching revenue data:', data.error);
                    return;
                }
                if (data.row_count >= maxRows) {
                    console.log('Maximum row count reached, stopping polling.');
                    return;
                }
                revenueChart.data.labels = data.revenue_data.map(item => item.date);
                revenueChart.data.datasets[0].data = data.revenue_data.map(item => item.revenue);
                revenueChart.update();
                // Continue polling if row count is below max
                setTimeout(fetchRevenueData, 5000); // Poll every 5 seconds
            })
            .catch(error => {
                console.error('Error fetching revenue data:', error);
                setTimeout(fetchRevenueData, 5000); // Retry on error
            });
        }

        // Initialize chart and start polling if row count is below max
        if (document.getElementById('revenueChart')) {
            initializeChart(revenueData);
            if (initialRowCount < maxRows) {
                setTimeout(fetchRevenueData, 5000);
            }
        }

        // Update chart instantly after new booking (triggered by form submission elsewhere)
        document.addEventListener('bookingUpdated', () => {
            fetchRevenueData();
        });
    </script>
    <script src="../js/script11.js"></script>
</body>
</html>




















<!-- <?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['company_name'])) {
    header("Location: login.php");
    exit();
}

$company_name = htmlspecialchars($_SESSION['company_name']);
$errors = [];
$success_message = [];
$today_trips = 0;
$total_revenue = 0;
$upcoming_trips = [];
$passengers = [];
$drivers = [];

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "gobus";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        $errors['general'] = "Database connection failed: " . $conn->connect_error;
    } else {
        // Fetch today's trips
        $sql = "SELECT COUNT(DISTINCT b.id) as trip_count 
                FROM buses b 
                WHERE b.operator_name = ? AND DATE(b.journey_date) = CURDATE()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $today_trips = $row['trip_count'];
        }
        $stmt->close();

        // Fetch total revenue from bookings table
        $sql = "SELECT COALESCE(SUM(fare), 0) as total_fare 
                FROM bookings 
                WHERE operator_name = ? AND status IN ('Upcoming', 'Completed')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $total_revenue = $row['total_fare'];
        }
        $stmt->close();

        // Fetch upcoming trips
        $sql = "SELECT id, bus_number, starting_point, destination, starting_time, arrival_time, journey_date, bus_type, seats_available, fare 
                FROM buses 
                WHERE operator_name = ? AND journey_date >= CURDATE() 
                ORDER BY journey_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $upcoming_trips[] = $row;
        }
        $stmt->close();

        // Fetch drivers
        $sql = "SELECT id, name, license_number, phone FROM drivers WHERE company_id = 
                (SELECT id FROM bus_companies WHERE company_name = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }
        $stmt->close();

        // Handle Add Trip Form
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_trip'])) {
            $starting_point = trim($_POST['trip-from'] ?? '');
            $destination = trim($_POST['trip-to'] ?? '');
            $starting_time = $_POST['starting-time'] ?? '';
            $arrival_time = $_POST['arrival-time'] ?? '';
            $journey_date = $_POST['trip-date'] ?? '';
            $bus_type = $_POST['bus-type'] ?? '';
            $seats = trim($_POST['seat-number'] ?? '');
            $fare = trim($_POST['fare'] ?? '');
            $bus_number = "BUS-" . strtoupper(substr(md5(uniqid()), 0, 6));

            // Validations
            if (empty($starting_point)) {
                $errors['trip-from'] = "Starting point is required.";
            } elseif (strlen($starting_point) > 100) {
                $errors['trip-from'] = "Starting point must be 100 characters or less.";
            }
            if (empty($destination)) {
                $errors['trip-to'] = "Destination is required.";
            } elseif (strlen($destination) > 100) {
                $errors['trip-to'] = "Destination must be 100 characters or less.";
            }
            if (empty($starting_time)) {
                $errors['starting-time'] = "Starting time is required.";
            }
            if (empty($arrival_time)) {
                $errors['arrival-time'] = "Arrival time is required.";
            }
            if (empty($journey_date)) {
                $errors['trip-date'] = "Journey date is required.";
            } elseif (strtotime($journey_date) < strtotime('today')) {
                $errors['trip-date'] = "Journey date cannot be in the past.";
            }
            if (!in_array($bus_type, ['AC', 'Non AC'])) {
                $errors['bus-type'] = "Invalid bus type.";
            }
            if (empty($seats)) {
                $errors['seat-number'] = "Number of seats is required.";
            } elseif (!is_numeric($seats) || $seats <= 0 || $seats > 100) {
                $errors['seat-number'] = "Seats must be a number between 1 and 100.";
            }
            if (empty($fare)) {
                $errors['fare'] = "Fare is required.";
            } elseif (!is_numeric($fare) || $fare <= 0) {
                $errors['fare'] = "Fare must be a positive number.";
            }

            // Insert trip if no errors
            if (empty($errors)) {
                $sql = "INSERT INTO buses (operator_name, bus_number, bus_type, starting_point, destination, starting_time, arrival_time, fare, seats_available, journey_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssdis", $company_name, $bus_number, $bus_type, $starting_point, $destination, $starting_time, $arrival_time, $fare, $seats, $journey_date);
                if ($stmt->execute()) {
                    $success_message['trip'] = "Trip added successfully!";
                    // Refresh upcoming trips
                    $sql = "SELECT id, bus_number, starting_point, destination, starting_time, arrival_time, journey_date, bus_type, seats_available, fare 
                            FROM buses 
                            WHERE operator_name = ? AND journey_date >= CURDATE() 
                            ORDER BY journey_date ASC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $company_name);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $upcoming_trips = [];
                    while ($row = $result->fetch_assoc()) {
                        $upcoming_trips[] = $row;
                    }
                    // Set active tab in sessionStorage
                    echo "<script>sessionStorage.setItem('activeTab', 'dashboard');</script>";
                } else {
                    $errors['general'] = "Error adding trip: " . $conn->error;
                }
                $stmt->close();
            }
        }

        // Handle Add Driver Form
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_driver'])) {
            $driver_name = trim($_POST['driver-name'] ?? '');
            $license_number = trim($_POST['driver-license'] ?? '');
            $phone = trim($_POST['driver-number'] ?? '');

            // Validations
            if (empty($driver_name)) {
                $errors['driver-name'] = "Driver name is required.";
            } elseif (strlen($driver_name) > 100) {
                $errors['driver-name'] = "Driver name must be 100 characters or less.";
            }
            if (empty($license_number)) {
                $errors['driver-license'] = "License number is required.";
            } elseif (strlen($license_number) > 50) {
                $errors['driver-license'] = "License number must be 50 characters or less.";
            } else {
                // Check for unique license number
                $sql = "SELECT id FROM drivers WHERE license_number = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $license_number);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $errors['driver-license'] = "License number already exists.";
                }
                $stmt->close();
            }
            if (empty($phone)) {
                $errors['driver-number'] = "Phone number is required.";
            } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
                $errors['driver-number'] = "Phone number must be 11 digits.";
            }

            // Insert driver if no errors
            if (empty($errors)) {
                $sql = "INSERT INTO drivers (company_id, name, license_number, phone) 
                        VALUES ((SELECT id FROM bus_companies WHERE company_name = ?), ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $company_name, $driver_name, $license_number, $phone);
                if ($stmt->execute()) {
                    $success_message['driver'] = "Driver added successfully!";
                    // Refresh drivers
                    $sql = "SELECT id, name, license_number, phone FROM drivers WHERE company_id = 
                            (SELECT id FROM bus_companies WHERE company_name = ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $company_name);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $drivers = [];
                    while ($row = $result->fetch_assoc()) {
                        $drivers[] = $row;
                    }
                    // Set active tab in sessionStorage
                    echo "<script>sessionStorage.setItem('activeTab', 'drivers');</script>";
                } else {
                    $errors['general'] = "Error adding driver: " . $conn->error;
                }
                $stmt->close();
            }
        }

        // Handle Passenger Search (Individual)
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_passenger'])) {
            $nid = trim($_POST['passenger-search-nid'] ?? '');
            $name = trim($_POST['passenger-search-name'] ?? '');
            $date = $_POST['passenger-search-date'] ?? '';

            // Validations
            if (empty($nid)) {
                $errors['passenger-search-nid'] = "NID is required.";
            } elseif (!preg_match('/^[0-9]{10}$/', $nid)) {
                $errors['passenger-search-nid'] = "NID must be 10 digits.";
            }
            if (empty($name)) {
                $errors['passenger-search-name'] = "Passenger name is required.";
            }
            if (empty($date)) {
                $errors['passenger-search-date'] = "Date is required.";
            }

            if (empty($errors)) {
                $sql = "SELECT u.username, u.nid, bk.booking_id, bk.route, bk.date, bk.seat_number 
                        FROM bookings bk 
                        JOIN users u ON bk.user_id = u.id 
                        JOIN buses b ON bk.bus_id = b.id 
                        WHERE b.operator_name = ? AND u.nid = ? AND u.username LIKE ? AND bk.date = ?";
                $stmt = $conn->prepare($sql);
                $name_like = "%$name%";
                $stmt->bind_param("ssss", $company_name, $nid, $name_like, $date);
                $stmt->execute();
                $result = $stmt->get_result();
                $passengers = [];
                while ($row = $result->fetch_assoc()) {
                    $passengers[] = $row;
                }
                // Set active tab in sessionStorage
                echo "<script>sessionStorage.setItem('activeTab', 'passengers');</script>";
                $stmt->close();
            }
        }

        // Handle Passenger Search (Date)
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_date'])) {
            $from = trim($_POST['search-from'] ?? '');
            $to = trim($_POST['search-To'] ?? '');
            $date = $_POST['search-date'] ?? '';

            // Validations
            if (empty($from)) {
                $errors['search-from'] = "Starting point is required.";
            }
            if (empty($to)) {
                $errors['search-To'] = "Destination is required.";
            }
            if (empty($date)) {
                $errors['search-date'] = "Date is required.";
            }

            if (empty($errors)) {
                $sql = "SELECT u.username, u.nid, bk.booking_id, bk.route, bk.date, bk.seat_number 
                        FROM bookings bk 
                        JOIN users u ON bk.user_id = u.id 
                        JOIN buses b ON bk.bus_id = b.id 
                        WHERE b.operator_name = ? AND bk.route LIKE ? AND bk.date = ?";
                $stmt = $conn->prepare($sql);
                $route_like = "%$from%$to%";
                $stmt->bind_param("sss", $company_name, $route_like, $date);
                $stmt->execute();
                $result = $stmt->get_result();
                $passengers = [];
                while ($row = $result->fetch_assoc()) {
                    $passengers[] = $row;
                }
                // Set active tab in sessionStorage
                echo "<script>sessionStorage.setItem('activeTab', 'passengers');</script>";
                $stmt->close();
            }
        }

        // Handle Cancel Trip
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_trip'])) {
            $bus_number = trim($_POST['bus-number'] ?? '');
            $search_date = $_POST['search-date'] ?? '';
            $cancel_reason = $_POST['cancelReason'] ?? '';
            $other_reason = trim($_POST['otherReason'] ?? '');
            $email_notif = isset($_POST['emailNotif']) ? 1 : 0;
            $sms_notif = isset($_POST['smsNotif']) ? 1 : 0;

            // Validations
            if (empty($bus_number)) {
                $errors['bus-number'] = "Bus number is required.";
            }
            if (empty($search_date)) {
                $errors['search-date'] = "Date is required.";
            }
            if (empty($cancel_reason)) {
                $errors['cancelReason'] = "Cancellation reason is required.";
            }
            if ($cancel_reason === 'Other' && empty($other_reason)) {
                $errors['otherReason'] = "Please specify the reason for cancellation.";
            }

            if (empty($errors)) {
                // Check if the bus exists for the given operator and date
                $sql = "SELECT id FROM buses WHERE operator_name = ? AND bus_number = ? AND journey_date = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $company_name, $bus_number, $search_date);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    $errors['bus-number'] = "No bus found for the specified number and date.";
                } else {
                    // Update bus status to Cancelled
                    $sql = "DELETE FROM buses WHERE operator_name = ? AND bus_number = ? AND journey_date = ?;";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $company_name, $bus_number, $search_date);
                    if ($stmt->execute()) {
                        $success_message['cancel'] = "Trip cancelled successfully!";
                        // Update bookings to Cancelled
                        $sql = "UPDATE bookings SET status = 'Cancelled' WHERE bus_id IN 
                                (SELECT id FROM buses WHERE operator_name = ? AND bus_number = ? AND journey_date = ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $company_name, $bus_number, $search_date);
                        $stmt->execute();
                        // Refresh upcoming trips
                        $sql = "SELECT id, bus_number, starting_point, destination, starting_time, arrival_time, journey_date, bus_type, seats_available, fare 
                                FROM buses 
                                WHERE operator_name = ? AND journey_date >= CURDATE() 
                                ORDER BY journey_date ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $company_name);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $upcoming_trips = [];
                        while ($row = $result->fetch_assoc()) {
                            $upcoming_trips[] = $row;
                        }
                        // Set active tab in sessionStorage
                    } else {
                        $errors['general'] = "Error cancelling trip: " . $conn->error;
                    }
                }
                $stmt->close();
            }
        }

        $conn->close();
    }
} catch (mysqli_sql_exception $e) {
    $errors['general'] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoBus - Bus Company Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles11.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h2>GoBus</h2>
        </div>
        <div class="nav-links">
            <div class="nav-item" data-tab="dashboard"><img src="../picture/home.png" alt="Home Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Dashboard</span></div>
            <div class="nav-item" data-tab="passengers"><img src="../picture/user_group.png" alt="User List Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Passenger List</span></div>
            <div class="nav-item" data-tab="revenue"><img src="../picture/chart.png" alt="Chart Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Revenue Report</span></div>
            <div class="nav-item" data-tab="drivers"><img src="../picture/driver.png" alt="License Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Assign Drivers</span></div>
            <div class="nav-item" data-tab="trips"><img src="../picture/busCancel.png" alt="Cancel Bus Icon" style="width: 18px; height: 18px; margin-right: 10px; vertical-align: middle;"><span>Cancel Trip</span></div>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="header22">
                <h2>Bus Company Dashboard</h2>
            </div>
            <div class="user-profile" id="userProfile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($company_name); ?>&background=2563eb&color=fff" alt="User">
                <div>
                    <div><?php echo $company_name; ?></div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <form id="changePasswordForm" method="POST" action="updatePassword.php">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit">Update Password</button>
                    </form>
                    <hr>
                    <button id="logoutBtn" onclick="window.location.href='logout.php'">Logout</button>
                </div>
            </div>
        </div>

        <?php if (isset($errors['general'])): ?>
            <div style="color: red; text-align: center;">
                <p><?php echo htmlspecialchars($errors['general']); ?></p>
            </div>
        <?php endif; ?>

        <div class="content-tabs" id="dashboard">
            <div class="dashboard-cards">
                <div class="card stat-card">
                    <div class="label">Today's Trips</div>
                    <div class="value"><?php echo htmlspecialchars($today_trips); ?></div>
                    <div class="icon"><img src="../picture/path.png" alt="Money Icon" style="width: 35px; height: 35px; vertical-align: middle;"></div>
                </div>
                <div class="card stat-card">
                    <div class="label">Total Revenue</div>
                    <div class="value"><?php echo htmlspecialchars(number_format($total_revenue, 2)); ?> Tk</div>
                    <div class="icon"><img src="../picture/taka.png" alt="Money Icon" style="width: 24px; height: 24px; vertical-align: middle;"></div>
                </div>
            </div>

            <div class="add-trip-form">
                <h3>Add New Trip</h3>
                <?php if (isset($success_message['trip'])): ?>
                    <div style="color: green; text-align: center;">
                        <p><?php echo htmlspecialchars($success_message['trip']); ?></p>
                    </div>
                <?php endif; ?>
                <form id="trip" method="POST" action="">
                    <input type="hidden" name="add_trip" value="1">
                    <input type="hidden" name="current_tab" value="dashboard">
                    <div class="form-group">
                        <label for="trip-from">Starting:</label>
                        <input type="text" id="trip-from" name="trip-from" placeholder="From" value="<?php echo isset($_POST['trip-from']) ? htmlspecialchars($_POST['trip-from']) : ''; ?>">
                        <?php if (isset($errors['trip-from'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['trip-from']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="starting-time">Starting Time:</label>
                        <input type="time" id="starting-time" name="starting-time" value="<?php echo isset($_POST['starting-time']) ? htmlspecialchars($_POST['starting-time']) : ''; ?>">
                        <?php if (isset($errors['starting-time'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['starting-time']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="trip-to">Arrival:</label>
                        <input type="text" id="trip-to" name="trip-to" placeholder="To" value="<?php echo isset($_POST['trip-to']) ? htmlspecialchars($_POST['trip-to']) : ''; ?>">
                        <?php if (isset($errors['trip-to'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['trip-to']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="arrival-time">Arrival Time:</label>
                        <input type="time" id="arrival-time" name="arrival-time" value="<?php echo isset($_POST['arrival-time']) ? htmlspecialchars($_POST['arrival-time']) : ''; ?>">
                        <?php if (isset($errors['arrival-time'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['arrival-time']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="trip-date">Date:</label>
                        <input type="date" id="trip-date" name="trip-date" value="<?php echo isset($_POST['trip-date']) ? htmlspecialchars($_POST['trip-date']) : ''; ?>">
                        <?php if (isset($errors['trip-date'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['trip-date']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="bus-type">Bus Type:</label>
                        <div class="bus-options">
                            <input type="radio" id="ac" name="bus-type" value="AC" <?php echo (isset($_POST['bus-type']) && $_POST['bus-type'] === 'AC') ? 'checked' : ''; ?>><label for="ac">AC</label>
                            <input type="radio" id="non-ac" name="bus-type" value="Non AC" <?php echo (isset($_POST['bus-type']) && $_POST['bus-type'] === 'Non AC') ? 'checked' : ''; ?>><label for="non-ac">Non AC</label>
                        </div>
                        <?php if (isset($errors['bus-type'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['bus-type']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="seat-number">Number of Seats:</label>
                        <input type="text" id="seat-number" name="seat-number" placeholder="Enter Number of Seats" value="<?php echo isset($_POST['seat-number']) ? htmlspecialchars($_POST['seat-number']) : ''; ?>">
                        <?php if (isset($errors['seat-number'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['seat-number']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="fare">Fare (Tk):</label>
                        <input type="text" id="fare" name="fare" placeholder="Enter Fare" value="<?php echo isset($_POST['fare']) ? htmlspecialchars($_POST['fare']) : ''; ?>">
                        <?php if (isset($errors['fare'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['fare']); ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="add-trip-btn">Add Trip</button>
                </form>
            </div>

            <div class="trip-list" id="upcoming-trips">
                <h3 class="section-title">Upcoming Trips</h3>
                <?php if (empty($upcoming_trips)): ?>
                    <p>No upcoming trips found.</p>
                <?php else: ?>
                    <?php foreach ($upcoming_trips as $trip): ?>
                        <div class="trip-item">
                            <p><strong>Bus Number:</strong> <?php echo htmlspecialchars($trip['bus_number']); ?></p>
                            <p><strong>Route:</strong> <?php echo htmlspecialchars($trip['starting_point'] . ' to ' . $trip['destination']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($trip['journey_date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($trip['starting_time'] . ' - ' . $trip['arrival_time']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($trip['bus_type']); ?></p>
                            <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($trip['seats_available']); ?></p>
                            <p><strong>Fare:</strong> <?php echo htmlspecialchars($trip['fare']); ?> Tk</p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-tabs" id="passengers">
            <div class="card">
                <h3 class="section-title">Passenger List</h3>
                <div class="search-options">
                    <button class="search-option-btn active" data-search-type="basic">Individual Passenger Search</button>
                    <button class="search-option-btn" data-search-type="date">Passenger List Search</button>
                </div>
                <div class="search-form active" id="basic-search-form">
                    <form method="POST" action="">
                        <input type="hidden" name="search_passenger" value="1">
                        <input type="hidden" name="current_tab" value="passengers">
                        <div class="form-group">
                            <input type="text" id="passenger-search-nid" name="passenger-search-nid" placeholder="Passenger NID Number" value="<?php echo isset($_POST['passenger-search-nid']) ? htmlspecialchars($_POST['passenger-search-nid']) : ''; ?>">
                            <?php if (isset($errors['passenger-search-nid'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['passenger-search-nid']); ?></div>
                            <?php endif; ?>
                            <input type="text" id="passenger-search-name" name="passenger-search-name" placeholder="Passenger Name" value="<?php echo isset($_POST['passenger-search-name']) ? htmlspecialchars($_POST['passenger-search-name']) : ''; ?>">
                            <?php if (isset($errors['passenger-search-name'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['passenger-search-name']); ?></div>
                            <?php endif; ?>
                            <input type="date" id="passenger-search-date" name="passenger-search-date" value="<?php echo isset($_POST['passenger-search-date']) ? htmlspecialchars($_POST['passenger-search-date']) : ''; ?>">
                            <?php if (isset($errors['passenger-search-date'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['passenger-search-date']); ?></div>
                            <?php endif; ?>
                            <button type="submit">Search</button>
                        </div>
                    </form>
                    <?php if (!empty($passengers)): ?>
                        <div class="passenger-list">
                            <?php foreach ($passengers as $passenger): ?>
                                <div class="passenger-item">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($passenger['username']); ?></p>
                                    <p><strong>NID:</strong> <?php echo htmlspecialchars($passenger['nid']); ?></p>
                                    <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($passenger['booking_id']); ?></p>
                                    <p><strong>Route:</strong> <?php echo htmlspecialchars($passenger['route']); ?></p>
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($passenger['date']); ?></p>
                                    <p><strong>Seat:</strong> <?php echo htmlspecialchars($passenger['seat_number']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['search_passenger']) || isset($_POST['search_date']))): ?>
                        <p>No passengers found.</p>
                    <?php endif; ?>
                </div>
                <div class="search-form" id="date-search-form">
                    <form method="POST" action="">
                        <input type="hidden" name="search_date" value="1">
                        <input type="hidden" name="current_tab" value="passengers">
                        <div class="form-group">
                            <input type="text" id="search-from" name="search-from" placeholder="From" value="<?php echo isset($_POST['search-from']) ? htmlspecialchars($_POST['search-from']) : ''; ?>">
                            <?php if (isset($errors['search-from'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['search-from']); ?></div>
                            <?php endif; ?>
                            <input type="text" id="search-To" name="search-To" placeholder="To" value="<?php echo isset($_POST['search-To']) ? htmlspecialchars($_POST['search-To']) : ''; ?>">
                            <?php if (isset($errors['search-To'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['search-To']); ?></div>
                            <?php endif; ?>
                            <input type="date" id="search-date" name="search-date" value="<?php echo isset($_POST['search-date']) ? htmlspecialchars($_POST['search-date']) : ''; ?>">
                            <?php if (isset($errors['search-date'])): ?>
                                <div style="color: red;"><?php echo htmlspecialchars($errors['search-date']); ?></div>
                            <?php endif; ?>
                            <button type="submit">Search List</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="content-tabs" id="revenue">
            <div class="card">
                <h3 class="section-title">Revenue Report</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="content-tabs" id="drivers">
            <div class="add-trip-form">
                <h3>Add New Driver</h3>
                <?php if (isset($success_message['driver'])): ?>
                    <div style="color: green; text-align: center;">
                        <p><?php echo htmlspecialchars($success_message['driver']); ?></p>
                    </div>
                <?php endif; ?>
                <form id="driverForm" method="POST" action="">
                    <input type="hidden" name="add_driver" value="1">
                    <input type="hidden" name="current_tab" value="drivers">
                    <input type="text" id="driver-name" name="driver-name" placeholder="Driver Full Name" value="<?php echo isset($_POST['driver-name']) ? htmlspecialchars($_POST['driver-name']) : ''; ?>">
                    <?php if (isset($errors['driver-name'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['driver-name']); ?></div>
                    <?php endif; ?>
                    <input type="text" id="driver-license" name="driver-license" placeholder="License Number" value="<?php echo isset($_POST['driver-license']) ? htmlspecialchars($_POST['driver-license']) : ''; ?>">
                    <?php if (isset($errors['driver-license'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['driver-license']); ?></div>
                    <?php endif; ?>
                    <input type="number" id="driver-number" name="driver-number" placeholder="Phone Number" value="<?php echo isset($_POST['driver-number']) ? htmlspecialchars($_POST['driver-number']) : ''; ?>">
                    <?php if (isset($errors['driver-number'])): ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors['driver-number']); ?></div>
                    <?php endif; ?>
                    <button type="submit">Add Driver</button>
                </form>
            </div>
            <div class="driver-list">
                <h3 class="section-title">Assign Driver</h3>
                <div id="driver-list">
                    <?php if (empty($drivers)): ?>
                        <p>No drivers found.</p>
                    <?php else: ?>
                        <?php foreach ($drivers as $driver): ?>
                            <div class="driver-item">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($driver['name']); ?></p>
                                <p><strong>License:</strong> <?php echo htmlspecialchars($driver['license_number']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($driver['phone']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-tabs" id="trips">
            <div class="add-trip-form">
                <h3>Cancel Trip</h3>
                <?php if (isset($success_message['cancel'])): ?>
                    <div style="color: green; text-align: center;">
                        <p><?php echo htmlspecialchars($success_message['cancel']); ?></p>
                    </div>
                <?php endif; ?>
                <form id="searchTripForm" method="POST" action="">
                    <input type="hidden" name="cancel_trip" value="1">
                    <input type="hidden" name="current_tab" value="trips">
                    <div class="form-group">
                        <label for="bus-number">Bus Number:</label>
                        <input type="text" id="bus-number" name="bus-number" placeholder="Enter Bus Number" value="<?php echo isset($_POST['bus-number']) ? htmlspecialchars($_POST['bus-number']) : ''; ?>">
                        <?php if (isset($errors['bus-number'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['bus-number']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="search-date">Date:</label>
                        <input type="date" id="search-date" name="search-date" value="<?php echo isset($_POST['search-date']) ? htmlspecialchars($_POST['search-date']) : ''; ?>">
                        <?php if (isset($errors['search-date'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['search-date']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="cancelReason">Reason for cancellation</label>
                        <select id="cancelReason" name="cancelReason">
                            <option value="" disabled selected>Select a reason</option>
                            <option value="Mechanical issues" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Mechanical issues') ? 'selected' : ''; ?>>Mechanical issues</option>
                            <option value="Driver unavailable" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Driver unavailable') ? 'selected' : ''; ?>>Driver unavailable</option>
                            <option value="Weather conditions" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Weather conditions') ? 'selected' : ''; ?>>Weather conditions</option>
                            <option value="Other" <?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['cancelReason'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['cancelReason']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group" id="otherReasonDiv" style="display:<?php echo (isset($_POST['cancelReason']) && $_POST['cancelReason'] === 'Other') ? 'block' : 'none'; ?>;">
                        <label for="otherReason">Please specify</label>
                        <textarea id="otherReason" name="otherReason" rows="3" placeholder="Enter your reason"><?php echo isset($_POST['otherReason']) ? htmlspecialchars($_POST['otherReason']) : ''; ?></textarea>
                        <?php if (isset($errors['otherReason'])): ?>
                            <div style="color: red;"><?php echo htmlspecialchars($errors['otherReason']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Notify passengers via</label>
                        <div>
                            <input type="checkbox" id="emailNotif" name="emailNotif" <?php echo (isset($_POST['emailNotif']) && $_POST['emailNotif']) ? 'checked' : ''; ?>> <label for="emailNotif">Email</label>
                            <input type="checkbox" id="smsNotif" name="smsNotif" <?php echo (isset($_POST['smsNotif']) && $_POST['smsNotif']) ? 'checked' : ''; ?>> <label for="smsNotif">SMS</label>
                        </div>
                    </div>
                    <button type="submit" class="cancel-btn">Cancel Trip</button>
                </form>
                <div class="trip-list" id="trip-list">
                    <h3 class="section-title">All Trips</h3>
                    <?php if (empty($upcoming_trips)): ?>
                        <p>No trips found.</p>
                    <?php else: ?>
                        <?php foreach ($upcoming_trips as $trip): ?>
                            <div class="trip-item">
                                <p><strong>Bus Number:</strong> <?php echo htmlspecialchars($trip['bus_number']); ?></p>
                                <p><strong>Route:</strong> <?php echo htmlspecialchars($trip['starting_point'] . ' to ' . $trip['destination']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($trip['journey_date']); ?></p>
                                <p><strong>Time:</strong> <?php echo htmlspecialchars($trip['starting_time'] . ' - ' . $trip['arrival_time']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($trip['bus_type']); ?></p>
                                <p><strong>Seats Available:</strong> <?php echo htmlspecialchars($trip['seats_available']); ?></p>
                                <p><strong>Fare:</strong> <?php echo htmlspecialchars($trip['fare']); ?> Tk</p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        // Ensure the active tab is restored after page load
        document.addEventListener('DOMContentLoaded', () => {
            const activeTab = sessionStorage.getItem('activeTab');
            if (activeTab && document.getElementById(activeTab)) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.content-tabs').forEach(tab => tab.classList.remove('active'));
                document.getElementById(activeTab).classList.add('active');
                document.querySelector(`.nav-item[data-tab="${activeTab}"]`).classList.add('active');
            } else {
                // Default to dashboard on initial load
                sessionStorage.setItem('activeTab', 'dashboard');
                document.getElementById('dashboard').classList.add('active');
                document.querySelector('.nav-item[data-tab="dashboard"]').classList.add('active');
            }
        });
    </script>
    <script src="../js/script11.js"></script>
</body>
</html> -->
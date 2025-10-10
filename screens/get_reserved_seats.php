<?php
header('Content-Type: application/json');

//Model
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

//Model
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

//Controller
$bus_id = isset($_GET['bus_id']) ? $conn->real_escape_string($_GET['bus_id']) : '';
$journey_date = isset($_GET['journey_date']) ? $conn->real_escape_string($_GET['journey_date']) : '';

if (!$bus_id || !$journey_date) {
    echo json_encode(['error' => 'Invalid parameters: bus_id and journey_date are required']);
    exit;
}

//Model
$sql = "SELECT seat_number FROM bookings WHERE bus_id = '$bus_id' AND date = '$journey_date' AND status = 'Upcoming'";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    exit;
}

//Controller
$reserved_seats = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reserved_seats[] = $row['seat_number'];
    }
}

echo json_encode(['reserved_seats' => $reserved_seats]);

$conn->close();
?>
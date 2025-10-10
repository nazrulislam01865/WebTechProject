<?php
header('Content-Type: application/json');

//Model
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "gobus";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
    //Controller
    $data = json_decode(file_get_contents('php://input'), true);
    $company_name = $data['company_name'] ?? '';

    if (empty($company_name)) {
        echo json_encode(['error' => 'Company name is required']);
        exit();
    }

    //Model
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

    $sql = "SELECT COUNT(*) as row_count FROM bookings";
    $result = $conn->query($sql);
    $row_count = $result->fetch_assoc()['row_count'];

    $conn->close();

    //Controller
    echo json_encode([
        'revenue_data' => $revenue_data,
        'row_count' => $row_count
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gobus";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get promo code and route from POST request
$promo_code = isset($_POST['promo_code']) ? $conn->real_escape_string($_POST['promo_code']) : '';
$route = isset($_POST['route']) ? $conn->real_escape_string($_POST['route']) : '';

if (empty($promo_code)) {
    error_log("Promo code is required");
    echo json_encode(['success' => false, 'message' => 'Promo code is required']);
    exit;
}

if (empty($route)) {
    error_log("Route is required");
    echo json_encode(['success' => false, 'message' => 'Route is required']);
    exit;
}

// Normalize route for comparison
$normalized_route = str_replace(' To ', '-', $route);
error_log("Validating promo code: $promo_code, route: $route, normalized_route: $normalized_route");

// Check promo code in database
$sql = "SELECT discount_type, discount_value, route FROM promotions WHERE promo_code = '$promo_code'";
error_log("Promo validation query: $sql");
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $promo_data = $result->fetch_assoc();
    $promo_route = $promo_data['route'];
    error_log("Promo data: " . print_r($promo_data, true));
    
    // Check if the route matches
    if (strpos($promo_route, 'routes') !== false) {
        // Handle routes like 'Dhaka routes'
        $route_base = str_replace(' routes', '', $promo_route);
        if (strpos($route, $route_base) !== false || strpos($normalized_route, $route_base) !== false) {
            echo json_encode([
                'success' => true,
                'discount_type' => $promo_data['discount_type'],
                'discount_value' => (float)$promo_data['discount_value']
            ]);
        } else {
            error_log("Promo code '$promo_code' invalid for route: $route (expected: $promo_route)");
            echo json_encode(['success' => false, 'message' => 'Promo code is not valid for this route']);
        }
    } else if ($promo_route === $route || $promo_route === $normalized_route) {
        echo json_encode([
            'success' => true,
            'discount_type' => $promo_data['discount_type'],
            'discount_value' => (float)$promo_data['discount_value']
        ]);
    } else {
        error_log("Promo code '$promo_code' invalid for route: $route (expected: $promo_route)");
        echo json_encode(['success' => false, 'message' => 'Promo code is not valid for this route']);
    }
} else {
    error_log("Invalid promo code: $promo_code");
    echo json_encode(['success' => false, 'message' => 'Invalid promo code']);
}

$conn->close();
?>
<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../helpers/jwt.php';

$database = new Database();
$db = $database->getConnection();

$user = JWT::getUserFromToken();

if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Forbidden: Admin access required"]);
    exit();
}

// Get total vehicles
$query = "SELECT COUNT(*) as count FROM vehicles";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_vehicles = $result['count'];

// Get total bookings
$query = "SELECT COUNT(*) as count FROM bookings";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_bookings = $result['count'];

// Get total users
$query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_users = $result['count'];

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => [
        "total_vehicles" => $total_vehicles,
        "total_bookings" => $total_bookings,
        "total_users" => $total_users
    ]
]);
?>

<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../helpers/jwt.php';

$database = new Database();
$db = $database->getConnection();

$user = JWT::getUserFromToken();

if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

// Get total bookings
$query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user['id']);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total_bookings = $result['count'];

// Get active bookings
$query = "SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id AND status = 'Active'";
$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user['id']);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$active_bookings = $result['count'];

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => [
        "total_bookings" => $total_bookings,
        "active_bookings" => $active_bookings
    ]
]);
?>

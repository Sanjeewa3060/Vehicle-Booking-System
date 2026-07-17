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

$query = "SELECT b.*, v.name as vehicle_name, v.type as vehicle_type, v.price
          FROM bookings b
          JOIN vehicles v ON b.vehicle_id = v.id
          WHERE b.user_id = :user_id
          ORDER BY b.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(":user_id", $user['id']);
$stmt->execute();

$bookings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $bookings[] = $row;
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $bookings
]);
?>

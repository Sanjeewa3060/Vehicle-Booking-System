<?php
include_once '../../config/cors.php';
include_once '../../config/database.php';
include_once '../../helpers/jwt.php';

$database = new Database();
$db = $database->getConnection();

$user = JWT::getUserFromToken();

if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Forbidden: Admin access required"]);
    exit();
}

$query = "SELECT id, name, type, price, status, created_at FROM vehicles ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$vehicles = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $vehicles[] = $row;
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "data" => $vehicles
]);
?>

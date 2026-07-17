<?php
include_once '../config/cors.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

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

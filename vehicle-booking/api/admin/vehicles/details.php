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

$vehicle_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$vehicle_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Vehicle ID is required"]);
    exit();
}

$query = "SELECT * FROM vehicles WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $vehicle_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $vehicle
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Vehicle not found"
    ]);
}
?>

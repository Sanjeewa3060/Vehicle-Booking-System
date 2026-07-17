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

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->type) && !empty($data->price)) {
    $query = "INSERT INTO vehicles (name, type, price, status, created_at) 
              VALUES (:name, :type, :price, 'Available', NOW())";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":type", $data->type);
    $stmt->bindParam(":price", $data->price);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Vehicle added successfully",
            "vehicle_id" => $db->lastInsertId()
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Unable to add vehicle"
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
}
?>

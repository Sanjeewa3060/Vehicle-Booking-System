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

if (!empty($data->id) && !empty($data->name) && !empty($data->type) && !empty($data->price) && !empty($data->status)) {
    $query = "UPDATE vehicles SET name = :name, type = :type, price = :price, status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":id", $data->id);
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":type", $data->type);
    $stmt->bindParam(":price", $data->price);
    $stmt->bindParam(":status", $data->status);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Vehicle updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Unable to update vehicle"
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

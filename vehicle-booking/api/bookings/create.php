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

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->vehicle_id) &&
    !empty($data->start_date) &&
    !empty($data->end_date)
) {
    // Check if vehicle is available
    $query = "SELECT status FROM vehicles WHERE id = :vehicle_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":vehicle_id", $data->vehicle_id);
    $stmt->execute();
    
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($vehicle['status'] !== 'Available') {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Vehicle is not available"
        ]);
        exit();
    }
    
    // Create booking
    $query = "INSERT INTO bookings (user_id, vehicle_id, start_date, end_date, status, created_at) 
              VALUES (:user_id, :vehicle_id, :start_date, :end_date, 'Active', NOW())";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":user_id", $user['id']);
    $stmt->bindParam(":vehicle_id", $data->vehicle_id);
    $stmt->bindParam(":start_date", $data->start_date);
    $stmt->bindParam(":end_date", $data->end_date);
    
    if ($stmt->execute()) {
        // Update vehicle status
        $updateQuery = "UPDATE vehicles SET status = 'Booked' WHERE id = :vehicle_id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(":vehicle_id", $data->vehicle_id);
        $updateStmt->execute();
        
        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Booking created successfully",
            "booking_id" => $db->lastInsertId()
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Unable to create booking"
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
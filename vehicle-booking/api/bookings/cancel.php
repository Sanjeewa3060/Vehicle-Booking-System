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

if (!empty($data->booking_id)) {
    // Get booking details
    $query = "SELECT vehicle_id FROM bookings WHERE id = :booking_id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":booking_id", $data->booking_id);
    $stmt->bindParam(":user_id", $user['id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Update booking status
        $query = "UPDATE bookings SET status = 'Cancelled' WHERE id = :booking_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":booking_id", $data->booking_id);
        
        if ($stmt->execute()) {
            // Update vehicle status
            $updateQuery = "UPDATE vehicles SET status = 'Available' WHERE id = :vehicle_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(":vehicle_id", $booking['vehicle_id']);
            $updateStmt->execute();
            
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Booking cancelled successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Unable to cancel booking"
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Booking not found"
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Booking ID is required"
    ]);
}
?>

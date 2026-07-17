<?php
include_once '../config/cors.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : die();

$query = "SELECT id, name, type, price FROM vehicles WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

if($stmt->rowCount() > 0) {
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "data" => $vehicle]);
} else {
    echo json_encode(["success" => false, "message" => "Vehicle not found"]);
}
?>
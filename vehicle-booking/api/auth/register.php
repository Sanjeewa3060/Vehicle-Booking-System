<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = [];
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!empty($name) && !empty($email) && !empty($password)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit();
    }

    $query = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([":email" => $email]);

    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email already exists"]);
        exit();
    }

    $query = "INSERT INTO users (name, email, password, role, status, created_at) VALUES (:name, :email, :password, 'user', 'active', NOW())";
    $stmt = $db->prepare($query);

    $safeName = htmlspecialchars(strip_tags($name));
    $safeEmail = htmlspecialchars(strip_tags($email));
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt->execute([
        ":name" => $safeName,
        ":email" => $safeEmail,
        ":password" => $password_hash
    ]);

    http_response_code(201);
    echo json_encode(["success" => true, "message" => "User registered successfully"]);
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data"]);
}
?>
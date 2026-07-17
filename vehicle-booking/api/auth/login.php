<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../helpers/jwt.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$database = new Database();
$db = $database->getConnection();

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = [];
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!empty($email) && !empty($password)) {
    $query = "SELECT id, name, email, password, role FROM users WHERE email = :email AND status = 'active' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([":email" => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        $token_data = [
            "id" => $row['id'],
            "email" => $row['email'],
            "role" => $row['role'],
            "exp" => time() + (86400 * 7)
        ];

        $jwt = JWT::encode($token_data);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "token" => $jwt,
            "user" => [
                "id" => $row['id'],
                "name" => $row['name'],
                "email" => $row['email'],
                "role" => $row['role']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid credentials"
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required"
    ]);
}
?>
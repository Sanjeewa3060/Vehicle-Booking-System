<?php
require 'C:\xampp\htdocs\PHP Project\vehicle-booking\api\config\database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email AND status = 'active' LIMIT 1");
$stmt->execute([":email" => "admin@vehiclebook.com"]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && password_verify("password", $row['password'])) {
    echo "LOGIN_OK\n";
    echo $row['role'] . "\n";
} else {
    echo "LOGIN_FAIL\n";
    if ($row) { echo $row['email'] . "\n"; }
}

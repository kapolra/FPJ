<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "condb.php";
header('Content-Type: application/json');

$firstName = $_POST['firstName'] ?? '';
$lastName  = $_POST['lastName'] ?? '';
$phone     = $_POST['phone'] ?? '';
$username  = $_POST['username'] ?? '';
$password  = $_POST['password'] ?? '';

if (empty($firstName) || empty($username) || empty($password)) {
    echo json_encode(["success" => false, "error" => "กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน"]);
    exit;
}

try {
    $checkSql = "SELECT COUNT(*) FROM Customers WHERE username = :username";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute([':username' => $username]);
    
    if ($checkStmt->fetchColumn() > 0) {
        echo json_encode(["success" => false, "error" => "ชื่อผู้ใช้งานนี้ถูกใช้ไปแล้ว"]);
        exit;
    }

  
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Customers (firstName, lastName, phone, username, password) 
            VALUES (:firstName, :lastName, :phone, :username, :password)";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':firstName' => $firstName,
        ':lastName'  => $lastName,
        ':phone'     => $phone,
        ':username'  => $username,
        ':password'  => $hashed_password 
    ]);

    echo json_encode([
        "success" => true, 
        "message" => "สมัครสมาชิกสำเร็จ",
        "customer_id" => $conn->lastInsertId()
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);
}
?>
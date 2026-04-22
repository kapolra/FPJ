<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "condb.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM Customers WHERE username=:username AND password=:password";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $fullName = $user["firstName"] . " " . $user["lastName"];

    echo json_encode([
        "status" => "success",
        "customer_id" => $user["customer_id"],
        "username" => $user["username"],
        "name" => $fullName  
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid username or password"
    ]);
}
?>
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "condb.php";

$product_name = $_POST['product_name'] ?? '';
$description  = $_POST['description'] ?? '';
$price        = $_POST['price'] ?? 0;
$stock        = $_POST['stock'] ?? 0;
$image_name   = ''; 

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_name = time() . '_' . basename($_FILES['image']['name']);
    $target_dir = "images/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image_name);
}

try {
    $sql = "INSERT INTO Products (product_name, description, price, image, stock) 
            VALUES (:product_name, :description, :price, :image, :stock)";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':product_name' => $product_name,
        ':description'  => $description,
        ':price'        => $price,
        ':image'        => $image_name,
        ':stock'        => $stock
    ]);

    echo json_encode(["success" => true, "status" => "Product added"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
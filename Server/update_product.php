<?php
header("Content-Type: application/json");

$host = "localhost"; 
$username = "root";  
$password = "";      
$database = "le_shop"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Ошибка с подключением к базе данных: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    $id = $conn->real_escape_string($data['id']);
    $name = isset($data['name']) ? $conn->real_escape_string($data['name']) : null;
    $price = isset($data['price']) ? $conn->real_escape_string($data['price']) : null;
    $image_url = isset($data['image_url']) ? $conn->real_escape_string($data['image_url']) : null;

    
    $checkQuery = "SELECT id FROM products WHERE id = '$id'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult && $checkResult->num_rows > 0) {
        $updates = [];
        if ($name !== null) $updates[] = "name = '$name'";
        if ($price !== null) $updates[] = "price = '$price'";
        if ($image_url !== null) $updates[] = "image_url = '$image_url'";

        if (!empty($updates)) {
            $updateQuery = "UPDATE products SET " . implode(", ", $updates) . " WHERE id = '$id'";
            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(["status" => "success", "message" => "Продукт обновлен"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Ошибка при обновлении продукта: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Нет данных"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Продукт не найден"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID продукта не был передан"]);
}

$conn->close();
?>
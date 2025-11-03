<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SESSION["dir_root"] . "/Database/koneksi.php"; // ← gunakan slash agar cross-platform

$action = htmlspecialchars(filter_input(INPUT_POST, "action"));
$category = filter_input(INPUT_POST, "category", FILTER_SANITIZE_NUMBER_INT);
$product = filter_input(INPUT_POST, "product", FILTER_SANITIZE_NUMBER_INT);

// Cegah warning jika productData kosong
$formData = $_POST['productData'] ?? '';
parse_str($formData, $productData);

$sanitizeData = [];
foreach ($productData as $key => $value) {
    $sanitizeData[$key] = htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

switch ($action) {
    case 'create':
        $result = createData($sanitizeData);
        break;
    case 'read':
        $result = readData($category);
        break;
    case 'update':
        $result = updateData($sanitizeData);
        break;
    case 'delete':
        $result = deleteData($sanitizeData);
        break;
    default:
        $result = [
            "response" => 400,
            "status" => 0,
            "messages" => "Invalid action request",
            "data" => null
        ];
}

echo json_encode($result);
die();

/* ==== FUNCTIONS ==== */

function createData($sanitizeData)
{
    try {
        $sql = "INSERT INTO product (product_name, category_id, uom_id, photo_product)
                VALUES (:product_name, :category_id, :uom_id, :photo_product)";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(":product_name", $sanitizeData["product-name"]);
        $stmt->bindParam(":category_id", $sanitizeData["product-category"]);
        $stmt->bindParam(":uom_id", $sanitizeData["product-uom"]);
        $stmt->bindParam(":photo_product", $sanitizeData["product-image"]);
        $stmt->execute();

        $rowCount = $stmt->rowCount();
        $stmt->closeCursor();

        if ($rowCount > 0) {
            return ["response" => 200, "status" => 1, "messages" => "Create Data Successful", "data" => null];
        } else {
            return ["response" => 400, "status" => 0, "messages" => "Create Data Failed", "data" => null];
        }
    } catch (PDOException $e) {
        return ["response" => 500, "status" => 0, "messages" => "Error: " . $e->getMessage(), "data" => null];
    }
}

function readData($categoryId)
{
    try {
        $sql = "SELECT product.*, categories.category_name, uom.id AS uom_id, uom.uom_name
                FROM product
                LEFT JOIN categories ON product.category_id = categories.id
                LEFT JOIN uom ON product.uom_id = uom.id
                WHERE product.category_id = :categoryId
                ORDER BY categories.category_name";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(":categoryId", $categoryId);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return ["response" => 200, "status" => 1, "messages" => "Fetching Data Successful", "data" => $rows];
    } catch (PDOException $e) {
        return ["response" => 500, "status" => 0, "messages" => "Error: " . $e->getMessage(), "data" => null];
    }
}

function updateData($sanitizeData)
{
    try {
        $sql = "UPDATE product SET product_name = :product_name, category_id = :category_id, 
                uom_id = :uom_id, photo_product = :photo_product WHERE id = :id";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(":product_name", $sanitizeData["product-name"]);
        $stmt->bindParam(":category_id", $sanitizeData["product-category"]);
        $stmt->bindParam(":uom_id", $sanitizeData["product-uom"]);
        $stmt->bindParam(":photo_product", $sanitizeData["product-image"]);
        $stmt->bindParam(":id", $sanitizeData["product-id"]);
        $stmt->execute();

        $rowCount = $stmt->rowCount();
        $stmt->closeCursor();

        if ($rowCount > 0) {
            return ["response" => 200, "status" => 1, "messages" => "Update Data Successful", "data" => null];
        } else {
            return ["response" => 400, "status" => 0, "messages" => "No rows updated", "data" => null];
        }
    } catch (PDOException $e) {
        return ["response" => 500, "status" => 0, "messages" => "Error: " . $e->getMessage(), "data" => null];
    }
}

function deleteData($sanitizeData)
{
    try {
        $sql = "DELETE FROM product WHERE id = :id";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(":id", $sanitizeData["product-id"]);
        $stmt->execute();

        $rowCount = $stmt->rowCount();
        $stmt->closeCursor();

        if ($rowCount > 0) {
            return ["response" => 200, "status" => 1, "messages" => "Delete Data Successful", "data" => null];
        } else {
            return ["response" => 400, "status" => 0, "messages" => "No rows deleted", "data" => null];
        }
    } catch (PDOException $e) {
        return ["response" => 500, "status" => 0, "messages" => "Error: " . $e->getMessage(), "data" => null];
    }
}
?>
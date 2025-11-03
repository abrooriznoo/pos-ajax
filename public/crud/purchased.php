<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once $_SESSION["dir_root"] . "\Database\koneksi.php";

$action = htmlspecialchars(filter_input(INPUT_POST, "action"));
$periodDate = $_POST["periodDate"] ?? null;
date_default_timezone_set("Asia/Jakarta");

$sanitizeData = [];
foreach ($_POST as $key => $value) {
    if ($key === 'action')
        continue;

    if (is_array($value)) {
        // Tangani array (seperti periodDate[])
        $sanitizeData[$key] = array_map(function ($v) {
            return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
        }, $value);
    } else {
        // Tangani string biasa
        $sanitizeData[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}

switch ($action) {
    case 'create_header':
        $result = createHeader($sanitizeData);
        break;
    case 'create_detail':
        $result = createDetail($sanitizeData);
        break;
    case 'read':
        $result = readData($periodDate);
        break;
    case 'update_header':
        $result = updateHeader($sanitizeData);
        break;
    case 'update_detail':
        $result = updateDetail($sanitizeData);
        break;
    case 'save_update_detail':
        $result = saveUpdateDetail($sanitizeData);
        break;
    case 'detail':
        $id = htmlspecialchars(filter_input(INPUT_POST, "id"));
        $result = detailData($id);
        break;
    case 'update':
        $result = updateData($sanitizeData);
        break;
    case 'delete':
        $result = deleteData($sanitizeData);
        break;
    case 'delete_detail':
        $result = deleteDetail($sanitizeData);
        break;
}

echo json_encode($result);
die();

function createHeader($data)
{
    try {
        $sql = "INSERT INTO purchase_header (purchase_order, supplier_id, purchase_date) 
                VALUES (:po, :supplier, :date)";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(':po', $data['purchased_order']);
        $stmt->bindParam(':supplier', $data['supplier_id']);
        $stmt->bindParam(':date', $data['purchase_date']);
        $stmt->execute();

        $lastId = db()->lastInsertId();

        return [
            "response" => 200,
            "status" => 1,
            "messages" => "Header berhasil dibuat",
            "data" => ["purchase_id" => $lastId]
        ];
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Gagal membuat header: " . $e->getMessage()
        ];
    }
}

function createDetail($data)
{
    try {
        $sql = "INSERT INTO purchase_detail (purchase_id, product_id, purchase_qty, purchase_uom, purchase_price)
                VALUES (:purchase_id, :product_id, :qty, :uom_id, :price)";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(':purchase_id', $data['purchase_id']);
        $stmt->bindParam(':product_id', $data['product_id']);
        $stmt->bindParam(':qty', $data['qty']);
        $stmt->bindParam(':uom_id', $data['uom_id']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->execute();

        $lastId = db()->lastInsertId();

        return [
            "response" => 200,
            "status" => 1,
            "messages" => "Detail berhasil disimpan",
            "data" => ["detail_id" => $lastId]
        ];
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Gagal menyimpan detail: " . $e->getMessage()
        ];
    }
}

function deleteDetail($data)
{
    $detailId = $data['detail_id'] ?? null;

    if (!$detailId) {
        return ["response" => 400, "status" => 0, "messages" => "Invalid parameters"];
    }

    try {
        $sql = "DELETE FROM purchase_detail WHERE id = :detailId";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(':detailId', $detailId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return [
                "response" => 200,
                "status" => 1,
                "messages" => "Detail berhasil dihapus"
            ];
        } else {
            return [
                "response" => 404,
                "status" => 0,
                "messages" => "Detail tidak ditemukan"
            ];
        }
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Gagal menghapus detail: " . $e->getMessage()
        ];
    }
}

function readData($periodDate)
{
    $sql = "SELECT * FROM purchase_dataview WHERE purchase_date BETWEEN :startDate AND :endDate ORDER BY purchase_date, purchase_order";
    $stmt = db()->prepare($sql);
    $stmt->bindParam(":startDate", $periodDate[0]);
    $stmt->bindParam(":endDate", $periodDate[1]);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return ["response" => 200, "status" => 1, "messages" => "Fetching Data Successful", "data" => $data];
}

function detailData($id)
{
    $sql = "SELECT 
                pd.id AS detail_id,
                pd.purchase_id,
                pd.product_id,
                pd.purchase_qty,
                pd.purchase_uom,
                u.uom_name, -- ← ambil nama satuan
                pd.purchase_price,
                ph.id AS purchase_id,
                ph.purchase_order,
                ph.purchase_date,
                ph.supplier_id,
                p.product_name
            FROM purchase_detail pd
            JOIN purchase_header ph ON pd.purchase_id = ph.id
            JOIN product p ON pd.product_id = p.id
            JOIN uom u ON pd.purchase_uom = u.id -- ← JOIN ke tabel uom berdasarkan ID
            WHERE ph.id = :purchaseId";
    $stmt = db()->prepare($sql);
    $stmt->bindParam(":purchaseId", $id);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return ["response" => 200, "status" => 1, "messages" => "Fetching Detail Successful", "data" => $data];
}

function updateHeader($data)
{
    $purchaseId = $data['purchase_id'] ?? null;

    if (!$purchaseId) {
        return ["response" => 400, "status" => 0, "messages" => "Invalid parameters"];
    }

    try {
        $sql = "UPDATE purchase_header 
                SET purchase_order = :po, supplier_id = :supplier, purchase_date = :date 
                WHERE id = :purchaseId";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(':po', $data['purchased_order']);
        $stmt->bindParam(':supplier', $data['supplier_id']);
        $stmt->bindParam(':date', $data['purchase_date']);
        $stmt->bindParam(':purchaseId', $purchaseId);
        $stmt->execute();

        return [
            "response" => 200,
            "status" => 1,
            "messages" => "Header berhasil diperbarui"
        ];
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Gagal memperbarui header: " . $e->getMessage()
        ];
    }
}

function updateDetail($data)
{
    $detailId = $data['detail_id'] ?? null;

    if (!$detailId) {
        return ["response" => 400, "status" => 0, "messages" => "Invalid detail ID"];
    }

    try {
        $sql = "SELECT pd.*, ph.purchase_order, ph.purchase_date, ph.supplier_id, p.product_name 
                FROM purchase_detail pd
                JOIN purchase_header ph ON pd.purchase_id = ph.id
                JOIN product p ON pd.product_id = p.id
                WHERE pd.id = :detailId";

        $stmt = db()->prepare($sql);
        $stmt->bindParam(":detailId", $detailId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($data) {
            return [
                "response" => 200,
                "status" => 1,
                "messages" => "Detail ditemukan",
                "data" => $data
            ];
        } else {
            return [
                "response" => 404,
                "status" => 0,
                "messages" => "Detail tidak ditemukan"
            ];
        }

    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Database error: " . $e->getMessage()
        ];
    }
}

function saveUpdateDetail($data)
{
    $detailId = $data['detail_id'] ?? null;

    if (!$detailId) {
        return ["response" => 400, "status" => 0, "messages" => "Invalid parameters"];
    }

    try {
        $sql = "UPDATE purchase_detail 
                SET product_id = :product_id, purchase_qty = :qty, purchase_uom = :uom_id, purchase_price = :price 
                WHERE id = :detailId";
        $stmt = db()->prepare($sql);
        $stmt->bindParam(':product_id', $data['product_id']);
        $stmt->bindParam(':qty', $data['qty']);
        $stmt->bindParam(':uom_id', $data['uom_id']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':detailId', $detailId);
        $stmt->execute();

        return [
            "response" => 200,
            "status" => 1,
            "messages" => "Detail berhasil diperbarui"
        ];
    } catch (PDOException $e) {
        return [
            "response" => 500,
            "status" => 0,
            "messages" => "Gagal memperbarui detail: " . $e->getMessage()
        ];
    }
}

function deleteData($sanitizeData)
{

}

?>
<?php

const host = "localhost";
const db = "posAbroor";
const user = "root";
const pass = "";

function db(): PDO
{
    static $pdo;
    if (!$pdo) {
        $dsn = "mysql:host=" . host . ";port=3306;dbname=" . db . ";charset=utf8";
        $pdo = new PDO($dsn, user, pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}


$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

try {
    $stmt = db()->prepare("SELECT product.*, categories.category_name, uom.id AS uom_id, uom.uom_name FROM product LEFT JOIN categories ON product.category_id = categories.id LEFT JOIN uom ON product.uom_id = uom.id WHERE product.category_id = :category_id");
    $stmt->execute(['category_id' => $categoryId]);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $counter = 1;

    if ($products) {
        foreach ($products as $product) {
            echo "<tr>
                    <td>{$counter}.</td>
                    <td style='padding-left: 50px;'>" . htmlspecialchars($product['product_name']) . "</td>
                    <td>" . htmlspecialchars($product['uom_name']) . "</td>
                </tr>";
            $counter++;
        }
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='3'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}

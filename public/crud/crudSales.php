<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once $_SESSION["dir_root"] . "\Database\connections.php";

class crudSales
{
    private $conn;

    public function __construct()
    {
        $database = new Connections();
        $this->conn = $database->getConnection();
    }

    public function save($data)
    {
        try {
            $this->conn->beginTransaction();

            // Insert sales_header
            $stmtHeader = $this->conn->prepare("
            INSERT INTO sales_header (sales_order, sales_date, customer_id)
            VALUES (:sales_order, :sales_date, :customer_id)
        ");
            $stmtHeader->execute([
                ':sales_order' => $data['salesOrder'],
                ':sales_date' => $data['salesDate'],
                ':customer_id' => $data['customerId']
            ]);
            $salesHeaderId = $this->conn->lastInsertId();

            // Insert sales_detail & kurangi stok purchase_detail
            $stmtDetail = $this->conn->prepare("
            INSERT INTO sales_detail (sales_id, product_id, sales_qty, sales_price, sales_uom)
            VALUES (:sales_id, :product_id, :sales_qty, :sales_price, :sales_uom)
        ");

            $stmtUpdateStock = $this->conn->prepare("
            UPDATE purchase_detail
            SET purchase_qty = purchase_qty - :qty_sold
            WHERE product_id = :product_id
        ");

            foreach ($data['items'] as $item) {
                // Insert detail
                $stmtDetail->execute([
                    ':sales_id' => $salesHeaderId,
                    ':product_id' => $item['productId'],
                    ':sales_qty' => $item['salesQty'],
                    ':sales_price' => $item['salesTotal'],
                    ':sales_uom' => $item['uomId']
                ]);

                // Kurangi stok (pastikan tidak negatif)
                $stmtUpdateStock->execute([
                    ':qty_sold' => $item['salesQty'],
                    ':product_id' => $item['productId']
                ]);
            }

            $this->conn->commit();
            return ['status' => 'success', 'message' => 'Sales created successfully.', 'response' => 201];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['status' => 'error', 'message' => 'Failed to create sales: ' . $e->getMessage()];
        }
    }


    public function delete($data)
    {
        try {
            $salesId = $data['salesId'];

            // Hapus detail sales dulu karena ada foreign key constraint
            $stmtDetail = $this->conn->prepare("DELETE FROM sales_detail WHERE sales_id = :sales_id");
            $stmtDetail->execute([':sales_id' => $salesId]);

            // Hapus header sales
            $stmtHeader = $this->conn->prepare("DELETE FROM sales_header WHERE id = :sales_id");
            $stmtHeader->execute([':sales_id' => $salesId]);

            return ['status' => 'success', 'message' => 'Sales deleted successfully.', 'response' => 200];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to delete sales: ' . $e->getMessage()];
        }
    }

    public function detailSales($data)
    {
        try {
            if (empty($data['salesId'])) {
                return [
                    'status' => 'error',
                    'message' => 'Parameter salesId tidak boleh kosong',
                    'response' => 400
                ];
            }

            $salesId = $data['salesId'];

            // Gabungkan header + detail produk
            $stmt = $this->conn->prepare("SELECT 
                            sh.id AS sales_id,
                            sh.sales_order,
                            sh.sales_date,
                            c.customer_name,
                            p.product_name,
                            p.sales_price AS product_price,           
                            sd.sales_qty,
                            sd.sales_price AS detail_sales_price,  
                            (sd.sales_qty * p.sales_price) AS total_price,
                            u.uom_name
                        FROM sales_header sh
                        JOIN customer c ON sh.customer_id = c.id
                        JOIN sales_detail sd ON sh.id = sd.sales_id
                        JOIN product p ON sd.product_id = p.id
                        JOIN uom u ON sd.sales_uom = u.id
                        WHERE sh.id = :sales_id
                        ORDER BY p.product_name ASC
                    ");
            $stmt->execute([':sales_id' => $salesId]);
            $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$salesData) {
                return [
                    'status' => 'error',
                    'message' => 'Sales not found',
                    'response' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'salesData' => $salesData
                ],
                'response' => 200,
                'message' => 'Sales details fetched successfully.'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch sales details: ' . $e->getMessage()
            ];
        }
    }

    public function printSales($data)
    {
        try {
            $salesId = $data['salesId'];

            // Gabungkan header + detail produk
            $stmt = $this->conn->prepare("SELECT 
                            sh.id AS sales_id,
                            sh.sales_order,
                            sh.sales_date,
                            c.customer_name,
                            p.product_name,
                            p.sales_price AS product_price,           
                            sd.sales_qty,
                            sd.sales_price AS detail_sales_price,  
                            (sd.sales_qty * p.sales_price) AS total_price,
                            u.uom_name
                        FROM sales_header sh
                        JOIN customer c ON sh.customer_id = c.id
                        JOIN sales_detail sd ON sh.id = sd.sales_id
                        JOIN product p ON sd.product_id = p.id
                        JOIN uom u ON sd.sales_uom = u.id
                        WHERE sh.id = :sales_id
                        ORDER BY p.product_name ASC
                    ");
            $stmt->execute([':sales_id' => $salesId]);
            $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$salesData) {
                return [
                    'status' => 'error',
                    'message' => 'Sales not found',
                    'response' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'salesData' => $salesData
                ],
                'response' => 200
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch sales for printing: ' . $e->getMessage()
            ];
        }
    }
}
?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: auth/login.php");
    exit();
}

require_once $_SESSION["dir_root"] . "../Database/koneksi.php";
require_once $_SESSION["dir_root"] . "../public/crud/crudSales.php";

$salesId = $_GET['sales_id'] ?? null;

if (!$salesId) {
    echo "Sales ID tidak ditemukan.";
    exit();
}

// Panggil controller
$sales = new crudSales();
$response = $sales->printSales(['salesId' => $salesId]);

if ($response['status'] !== 'success') {
    echo "Data sales tidak ditemukan.";
    exit();
}

$salesData = $response['data']['salesData'];
?>


<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cetak Penjualan</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px;
            text-align: right;
        }
    </style>
</head>

<body onload="window.print()">

    <h2>Sales Order #<?= htmlspecialchars($salesData[0]['sales_order']) ?></h2>
    <p><strong>Customer:</strong> <?= htmlspecialchars($salesData[0]['customer_name']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($salesData[0]['sales_date']) ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>UOM</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grandTotal = 0;
            foreach ($salesData as $i => $row):
                $grandTotal += $row['total_price'];
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['sales_qty'] ?></td>
                    <td><?= htmlspecialchars($row['uom_name']) ?></td>
                    <td>Rp<?= number_format($row['product_price'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($row['total_price'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach ?>
            <tr>
                <td colspan="5" style="text-align:right;">
                    <strong>Subtotal</strong>
                </td>
                <td>
                    <strong>Rp<?= number_format($grandTotal, 0, ',', '.') ?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:right;">
                    <strong>Tax (10%)</strong>
                </td>
                <td>
                    <strong>Rp<?= number_format($grandTotal * 0.1, 0, ',', '.') ?></strong>
                </td>
            </tr>
            <tr>
                <?php
                $discount = 0;
                $maxDiscount = 300000; // misalnya diskon maksimum
                
                if ($grandTotal > 5_500_000) {
                    $discount = $maxDiscount;
                } elseif ($grandTotal > 5_000_000) {
                    $discount = 150000; // 15%
                } elseif ($grandTotal > 2_000_000) {
                    $discount = 100000; // 10%
                } elseif ($grandTotal > 1_000_000) {
                    $discount = 50000; // 5%
                }
                ?>

                <td colspan="5" style="text-align:right;">
                    <strong>Discount</strong>
                </td>
                <td>
                    <?php if ($discount > 0): ?>
                        <strong>- Rp<?= number_format($discount, 0, ',', '.') ?></strong>
                    <?php else: ?>
                        <strong>- Rp0</strong>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:right;">
                    <strong>Grand Total</strong>
                </td>
                <td>
                    <strong>Rp<?= number_format($grandTotal + ($grandTotal * 0.1) - $discount, 0, ',', '.') ?></strong>
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html> -->

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sales Order #<?= htmlspecialchars($salesData[0]['sales_order']) ?></title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
            color: #333;
        }

        .invoice-container {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo img {
            height: 60px;
        }

        h2 {
            color: #007bff;
            margin-bottom: 5px;
        }

        .customer-info {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 10px;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tfoot td {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .summary td {
            border: none;
        }

        .grand-total {
            font-size: 1.1em;
            color: #007bff;
        }

        footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="invoice-container">
        <header>
            <div class="logo">
                <img src="assets/icons.png" alt="Company Logo">
                <br>
                <small>PT. AR Sejahtera Abadi</small>
                <br>
                <small>Jl. Merdeka No.123, Jakarta</small>
            </div>
            <div>
                <h2>Sales Order #<?= htmlspecialchars($salesData[0]['sales_order']) ?></h2>
                <p><strong>Tanggal: <em><?= date("d F Y", strtotime($salesData[0]['sales_date'])) ?></em></strong></p>
            </div>
        </header>

        <section class="customer-info">
            <p><strong>Customer:</strong> <?= htmlspecialchars($salesData[0]['customer_name']) ?></p>
        </section>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grandTotal = 0;
                foreach ($salesData as $i => $row):
                    $grandTotal += $row['total_price'];
                    ?>
                    <tr>
                        <td><?= $i + 1 ?>.</td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td style="text-align: right;"><?= $row['sales_qty'] ?></td>
                        <td><?= htmlspecialchars($row['uom_name']) ?></td>
                        <td style="text-align: right;">Rp<?= number_format($row['product_price'], 0, ',', '.') ?></td>
                        <td style="text-align: right;">Rp<?= number_format($row['total_price'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <?php
                $tax = $grandTotal * 0.1;
                $discount = 0;
                $maxDiscount = 300000;

                if ($grandTotal > 5500000) {
                    $discount = $maxDiscount;
                } elseif ($grandTotal > 5000000) {
                    $discount = 150000;
                } elseif ($grandTotal > 2000000) {
                    $discount = 100000;
                } elseif ($grandTotal > 1000000) {
                    $discount = 50000;
                }

                $finalTotal = $grandTotal + $tax - $discount;
                ?>
                <tr>
                    <td colspan="5" class="text-right">Subtotal</td>
                    <td style="text-align: right;">Rp<?= number_format($grandTotal, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Tax (10%)</td>
                    <td style="text-align: right;">Rp<?= number_format($tax, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Discount</td>
                    <td style="text-align: right;">- Rp<?= number_format($discount, 0, ',', '.') ?></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="5" class="text-right">Grand Total</td>
                    <td style="text-align: right;">Rp<?= number_format($finalTotal, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <footer>
            <p>Terima kasih telah berbelanja dengan kami!</p>
            <p><em>Dokumen ini dibuat secara otomatis dan tidak memerlukan tanda tangan.</em></p>
        </footer>
    </div>
</body>

</html>
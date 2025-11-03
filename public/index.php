<?php
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: auth/login.php");
    exit();
}
require_once "bootstrap.php";

require_once "../Database/koneksi.php";
$site_root = $_SESSION["site_root"];

$sql = db()->prepare("SELECT * FROM categories");
$sql->execute();
$categories = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("SELECT * FROM product");
$sql->execute();
$products = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("SELECT * FROM uom");
$sql->execute();
$uom_result = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("SELECT * FROM customer");
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("
    SELECT p.*
    FROM product p
    JOIN purchase_detail pd ON p.id = pd.product_id
    WHERE pd.purchase_qty > 0
    GROUP BY p.id
    ORDER BY p.product_name ASC
");
$sql->execute();
$productList = $sql->fetchAll(PDO::FETCH_ASSOC);


$sql = db()->prepare("SELECT * FROM uom");
$sql->execute();
$uomList = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("SELECT 
            sh.id AS sales_id,
            sh.sales_order,
            sh.sales_date,
            c.customer_name,
            GROUP_CONCAT(p.product_name ORDER BY p.product_name SEPARATOR ', ') AS products,
            GROUP_CONCAT(sd.sales_qty ORDER BY p.product_name SEPARATOR ', ') AS qtys,
            GROUP_CONCAT(u.uom_name ORDER BY p.product_name SEPARATOR ', ') AS sales_uoms,
            MIN(sd.sales_price) AS first_sales_price
        FROM sales_header sh
        JOIN customer c ON sh.customer_id = c.id
        JOIN sales_detail sd ON sh.id = sd.sales_id
        JOIN product p ON sd.product_id = p.id
        JOIN uom u ON sd.sales_uom = u.id
        GROUP BY sh.id, sh.sales_order, sh.sales_date, c.customer_name
        ORDER BY sh.sales_order ASC;");
$sql->execute();
$salesData = $sql->fetchAll(PDO::FETCH_ASSOC);

// generate purchase order like: SO/2025/09/13/0001 and sequence resets each day
$dateSegment = date('Y/m/d');   // used in visible purchase_order
$dbDateSegment = date('Ymd');   // used for predictable LIKE pattern (no slashes)

$stmt = db()->prepare("SELECT sales_order FROM sales_header WHERE sales_order LIKE :like ORDER BY sales_order DESC LIMIT 1");
$stmt->execute([':like' => 'SO/' . $dateSegment . '/%']);
$last = $stmt->fetch(PDO::FETCH_ASSOC);

if ($last && isset($last['sales_order'])) {
    // expected format segments: SO/YYYY/MM/DD/NNNN
    $parts = explode('/', $last['sales_order']);
    $lastSeq = intval(end($parts));
    $nextSeq = $lastSeq + 1;
} else {
    $nextSeq = 1;
}

$generateSalesOrder = 'SO/' . $dateSegment . '/' . sprintf('%04d', $nextSeq);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script>
        const base_url = "<?= $site_root ?>";
        const productData = <?= json_encode($productList) ?>;
        const uomData = <?= json_encode($uomList) ?>;
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>POS - Point of Sales</title>
    <!-- STYLE PRODUCT -->
    <style>
        /* Animasi untuk div pembungkus */
        /* #table-container {
            transition: max-height 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-20px);
        }

        #table-container.show {
            max-height: 800px;
            opacity: 1;
            transform: translateY(0);
        } */

        /* Gaya untuk baris tabel yang dapat diklik */
        #data-categories,
        #data-product {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            border-spacing: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 1rem;
            color: #212529;
        }

        #data-categories th,
        #data-categories td,
        #data-product th,
        #data-product td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        #data-categories thead th,
        #data-product thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #e9ecef;
        }

        #data-categories tbody+tbody,
        #data-product tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        /* Hover effect for rows */
        #data-categories tbody tr:hover,
        #data-product tbody tr:hover {
            background-color: #b8fda3ff;
            cursor: pointer;
            transition: background-color 0.50s ease-in-out;
        }

        /* Selected row */
        #data-categories tbody tr.selected,
        #data-product tbody tr.selected {
            background-color: #9cd1f0ff;
            transition: background-color 0.50s ease-in-out;
        }
    </style>
    <!-- STYLE PURCHASED -->
    <style>
        /* Root table style */
        #table-purchased,
        #table-purchased-detail {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            /* Diganti dari fixed agar kolom bisa fleksibel */
            background-color: #ffffff;
            font-size: 0.95rem;
        }

        /* Header style */
        #table-purchased th,
        #table-purchased-detail th {
            padding: 10px 12px;
            background-color: #f8f9fa;
            color: #333;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: 600;
            white-space: nowrap;
            /* mencegah pecah */
        }

        /* Cell style */
        #table-purchased td,
        #table-purchased-detail td {
            padding: 9px 11px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            word-break: break-word;
        }

        /* Hover */
        #table-purchased tbody tr.hoverable:hover td,
        #table-purchased-detail tbody tr.hoverable:hover td {
            background-color: #eafee4 !important;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        /* Selected row */
        #table-purchased tbody tr.selected td,
        #table-purchased-detail tbody tr.selected td {
            background-color: #3ca5ec !important;
            color: #fff;
            transition: background-color 0.3s ease-in-out;
        }

        /* Responsive wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Media query untuk layar kecil */
        @media (max-width: 768px) {

            #table-purchased th,
            #table-purchased td,
            #table-purchased-detail th,
            #table-purchased-detail td {
                font-size: 0.85rem;
                padding: 8px;
            }
        }
    </style>
    <!-- STYLE SALES -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #detail,
            #detail *,
            .summary,
            .summary * {
                visibility: visible;
            }

            #detail {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .summary {
                position: relative;
                margin-top: 20px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-dark bg-gradient shadow-lg">
        <?php include "components/navbar.php"; ?>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Content -->
        <section class="section">
            <?php
            if (isset($_GET['pages'])) {
                if (file_exists('pages/' . $_GET['pages'] . ".php")) {
                    include_once 'pages/' . $_GET['pages'] . ".php";
                }
            } else {
                include 'pages/home.php'; // default page
            }
            ?>
            <!-- / Content -->
        </section>
    </div>

    <script src=" <?= $site_root ?> /js/setProducts.js"></script>
    <script src=" <?= $site_root ?> /js/setPurchases.js"></script>
    <script src=" <?= $site_root ?> /js/setSales.js"></script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
    <script src="../js/setPurchases.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
</body>

</html>
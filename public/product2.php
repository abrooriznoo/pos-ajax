<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: auth/login.php");
    exit();
}

require_once $_SESSION["dir_root"] . "../Database/koneksi.php";
$site_root = $_SESSION["site_root"];

$sql = db()->prepare("SELECT * FROM categories");
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script>
        const base_url = "<?= $site_root ?>"; // auto-set by PHP
        // console.log(base_url);
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>POS - Products</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-dark bg-gradient shadow-lg">
        <?php include "components/navbar.php"; ?>
    </nav>

    <!-- Main Content -->
    <div class="card p-3 m-5 shadow-lg">
        <div class="card-body p-2">
            <h2 class="card-title">Products</h2>
            <small class="text-gray-600">List of all products available in the system.</small>

            <div class="alert alert-info mt-3" role="alert">
                This section displays all products available in the system along with their details.
            </div>


            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-start">
                        <div class="mt-3 mx-1">
                            <button type="button" class="btn bg-primary bg-gradient mb-3 shadow-lg text-white"
                                id="btn-add-products" data-bs-toggle="modal" data-bs-target="#modalProduct">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <button type="button" class="btn bg-secondary bg-gradient mb-3 shadow-lg text-white"
                                id="btn-edit-products" data-bs-toggle="modal" data-bs-target="#modalProduct">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="btn bg-danger bg-gradient mb-3 shadow-lg text-white"
                                id="btn-delete-products" data-bs-toggle="modal" data-bs-target="#modalProduct">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div>
                            <small class="text-muted">Select a category to filter products.</small>
                            <div class="d-flex justify-content-between gap-3">
                                <div class="w-70">
                                    <select name="category-select" id="category-select"
                                        class="form-select mt-3 shadow-lg">
                                        <option value="" selected disabled>-- Select Category --</option>
                                        <?php foreach ($result as $category): ?>
                                            <option value="<?= $category['id'] ?>">
                                                <?= $category['category_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button class="btn btn-secondary mt-3" onclick="resetFilters()">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Table Category -->
                    <div class="col-4">
                        <div class="card p-2 mt-3 shadow-lg">
                            <table id="data-categories">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th>Category Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($result as $category):
                                        ?>
                                        <tr class="" data-id="<?= $category['id'] ?>"
                                            data-category="<?= $category['category_name'] ?>">
                                            <td><?= $no++; ?>.</td>
                                            <td><?= $category['category_name']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table Product -->
                    <div class="col-8">
                        <div class="row">
                            <div id="table-container" class="card p-2 mt-3 shadow-lg">
                                <table id="data-product">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Product Name</th>
                                            <th>UOM</th>
                                            <th>Image</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>Category : </th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $stmt = db()->query("SELECT product.*, categories.category_name, uom.uom_name FROM product LEFT JOIN categories ON product.category_id = categories.id LEFT JOIN uom ON product.uom_id = uom.id ORDER BY categories.category_name");

                                            $lastCategory = '';
                                            $counter = 1;

                                            while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                // Jika kategori berubah, cetak baris kategori dulu
                                                if ($product['category_name'] !== $lastCategory) {
                                                    if ($lastCategory !== '') {
                                                        echo "<tr><td colspan='4' style='height: 20px;'></td></tr>";
                                                    }
                                                    echo "<tr>
                                                            <td></td>
                                                            <td class='fw-bolder' style='padding-left: 20px;'>" . htmlspecialchars($product['category_name'] ?? 'Non Category') . "</td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>";
                                                    $lastCategory = $product['category_name'];
                                                    $counter = 1;
                                                }

                                                // Baris produk dengan data attributes
                                                echo "<tr class='hoverable' data-id='" . $product['id'] . "' data-name='" . $product['product_name'] . "' data-uom='" . $product['uom_id'] . "' data-category='" . $product['category_id'] . "'  data-foto='" . $product['photo_product'] . "'>
                                                        <td>" . $counter++ . ".</td>
                                                        <td style='padding-left: 50px;'>" . htmlspecialchars($product['product_name']) . "</td>
                                                        <td>" . htmlspecialchars($product['uom_name']) . "</td>
                                                        <td><img src='" . $site_root . "/public/assets/img/" . $product['photo_product'] . "' style='width: 100px; max-height: 100%;' class='object-fit-contain img-fluid'></td>
                                                    </tr>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "<tr><td colspan='3'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="card p-2 mt-3 shadow-lg">
                                <div class="" id="get-product">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Add Product -->
            <div class="modal fade" id="modalProduct" tabindex="-1" aria-labelledby="modalProductLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="d-flex justify-content-between p-3" id="modalHeader">
                            <h1 class="modal-title fs-5" id="modalProductLabel"></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3 text-danger fw-bold d-none" id="delete-confirmation-message">
                                        Apakah kamu yakin ingin menghapus data ini?
                                    </div>
                                    <form action="" method="post" id="form-product" enctype="multipart/form-data">
                                        <input type="hidden" name="product-id" id="product-id">

                                        <div class="mb-3">
                                            <label for="product-name" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="product-name"
                                                name="product-name" placeholder="Masukkan Nama produk">
                                        </div>

                                        <div class="mb-3">
                                            <label for="product-category" class="form-label">Product Category</label>
                                            <select name="product-category" id="product-category"
                                                class="form-select shadow-sm">
                                                <option value="" selected disabled>-- Select Category --</option>
                                                <?php foreach ($result as $category): ?>
                                                    <option value="<?= $category['id'] ?>"><?= $category['category_name'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="product-uom" class="form-label">Product UOM</label>
                                            <select name="product-uom" id="product-uom" class="form-select shadow-sm">
                                                <option value="" selected disabled>-- Select UOM --</option>
                                                <?php
                                                // Menyiapkan query SQL untuk mengambil semua data dari tabel 'uom' dan mengurutkannya berdasarkan kolom 'uom_name'
                                                $stmt = db()->prepare("SELECT * FROM uom ORDER BY uom_name");
                                                $stmt->execute(); // Menjalankan query yang sudah disiapkan
                                                // Mengambil setiap baris hasil query satu per satu dalam bentuk array asosiatif
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='" . $row['id'] . "'>" . $row['uom_name'] . "</option>";
                                                }
                                                $stmt->closeCursor(); // Tutup cursor agar resource dibebaskan
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="product-image" class="form-label">Product Image</label>
                                            <input type="file" class="form-control" id="product-image"
                                                name="product-image" accept="image/*">
                                            <div class="mt-2">
                                                <img id="product-preview" src="" style="max-height: 150px;"
                                                    class="img-thumbnail d-none">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <div class="d-flex justify-content-end mt-3 button-group">
                                                <button type="button" class="btn btn-secondary me-2"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" id="btn-submit">Save
                                                    Product</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div id="toast-message" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toast-body">Success</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div> -->


        <!-- Scripts -->
        <script src="../js/setProducts.js"></script>

        <!-- Jquery -->
        <!-- <script>
        $(document).ready(function () {
            $.ajax({
                url: '../Database/koneksi.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    let html = '';
                    response.forEach(function (products, index) {
                        html += `
                    <tr>
                        <td>${index + 1}.</td>
                        <td>${products.product_name}</td>
                        <td>${products.uom}</td>
                        <td>${products.category_name}</td>
                    </tr>
                `;
                    });
                    $('#data-product tbody').html(html);
                    $('#table-container').addClass('show');
                },
                error: function (xhr, status, error) {
                    console.error("Gagal load data:", error);
                }
            });
        });
    </script> -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
            </script>
</body>

</html>
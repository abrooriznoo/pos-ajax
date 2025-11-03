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
                                <select name="category-select" id="category-select" class="form-select mt-3 shadow-lg">
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
                                foreach ($categories as $category):
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
        <div class="modal fade" id="modalProduct" tabindex="-1" aria-labelledby="modalProductLabel" aria-hidden="true">
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
                                        <input type="text" class="form-control" id="product-name" name="product-name"
                                            placeholder="Masukkan Nama produk">
                                    </div>

                                    <div class="mb-3">
                                        <label for="product-category" class="form-label">Product Category</label>
                                        <select name="product-category" id="product-category"
                                            class="form-select shadow-sm">
                                            <option value="" selected disabled>-- Select Category --</option>
                                            <?php foreach ($categories as $category): ?>
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
                                        <input type="file" class="form-control" id="product-image" name="product-image"
                                            accept="image/*">
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
</div>
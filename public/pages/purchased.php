<!-- Main Content -->
<div class="card p-3 m-5 shadow-lg">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-center mb-2 mx-2">
            <div class="">
                <h2 class="card-title">Purchase Data</h2>
                <small class="text-gray-600">List of all purchased items available in the system.</small>
            </div>
            <button type="button" class="btn bg-primary bg-gradient shadow-lg text-white btn-sm" id="btn-add-purchase"
                onclick="window.location.href='components/formPurchased.php';">
                <i class="bi bi-plus-lg"></i> Tambah Purchase
            </button>
        </div>

        <!-- <div class="alert alert-info mt-3" role="alert">
                This section displays all purchased items available in the system along with their details.
            </div> -->

        <hr />

        <div class="container-fluid mt-4">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="align-items-center mb-2">
                            <small>Purchase Periode</small>
                        </div>
                        <input type="text" class="form-control mb-2" id="purchase-periode" placeholder="Select period">
                    </div>
                </div>
            </div>
            <div class="card mt-4 shadow-lg">
                <div class="card-body">
                    <h5>Purchase Header</h5>

                    <div class="container-fluid py-4">
                        <div class="d-flex justify-content-center" id="before-load-header">
                            <h6 class="text-danger">Silahkan Pilih Periode Tanggal Untuk Melihat Data Purchase</h6>
                        </div>
                        <div class="row g-4 d-none" id="purchase-data-section">
                            <!-- Kolom Header -->
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <div id="purchase-header" class="border rounded p-3 bg-light shadow-sm">
                                        <!-- Isi header akan muncul di sini -->
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Detail -->
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <div id="purchase-detail" class="border rounded p-3 bg-white shadow-sm">
                                        <!-- Isi detail akan muncul di sini -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Add Item -->
    <div class="modal fade" id="modalAddItem" tabindex="-1" aria-labelledby="modalAddItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAddItemLabel">Add Purchased Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-add-item">
                        <input type="hidden" id="modal-purchase-id" name="purchase_id">

                        <div class="mb-3">
                            <label for="product-select" class="form-label">Product</label>
                            <select class="form-select" id="product-select" name="product_id" required>
                                <option value="#" disabled selected>Select a product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>">
                                        <?= htmlspecialchars($product['product_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="item-price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="item-price" name="price" required>
                        </div>

                        <div class="mb-3">
                            <label for="item-quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="item-quantity" name="qty" required>
                        </div>

                        <div class="mb-3">
                            <label for="item-uom" class="form-label">UOM</label>
                            <select class="form-select" id="item-uom" name="uom_id" required>
                                <option value="#" disabled selected>Select UOM</option>
                                <?php foreach ($uom_result as $uom): ?>
                                    <option value="<?= htmlspecialchars($uom['id']) ?>">
                                        <?= htmlspecialchars($uom['uom_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Item</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Item -->
    <div class="modal fade" id="modalEditItem" tabindex="-1" aria-labelledby="modalEditItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditItemLabel">Edit Purchased Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-item" method="post">
                        <input type="hidden" id="edit-detail-id" name="detail_id">
                        <!-- <input type="hidden" id="edit-purchase-id" name="purchase_id"> -->

                        <div class="mb-3">
                            <label for="edit-product-select" class="form-label">Product</label>
                            <select class="form-select" id="edit-product-select" name="product_id" required>
                                <option value="" disabled selected>Select a product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= htmlspecialchars($product['id']) ?>">
                                        <?= htmlspecialchars($product['product_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit-item-price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit-item-price" name="price" required>
                            <div class="invalid-feedback">Please enter a valid price.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-item-quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="edit-item-quantity" name="qty" required>
                            <div class="invalid-feedback">Please enter a valid quantity.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-item-uom" class="form-label">UOM</label>
                            <select class="form-select" id="edit-item-uom" name="uom_id" required>
                                <option value="" disabled selected>Select UOM</option>
                                <?php foreach ($uom_result as $uom): ?>
                                    <option value="<?= htmlspecialchars($uom['id']) ?>">
                                        <?= htmlspecialchars($uom['uom_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation to Delete Item -->
    <div class="modal fade" id="modalDeleteItem" tabindex="-1" aria-labelledby="modalDeleteItemLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalDeleteItemLabel">Delete Purchased Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item?</p>
                    <form id="form-delete-item" method="post">
                        <input type="hidden" id="delete-detail-id" name="detail_id">
                        <input type="hidden" id="delete-purchase-id" name="purchase_id">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
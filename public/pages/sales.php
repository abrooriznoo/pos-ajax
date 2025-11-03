<!-- Main Content -->
<div class="card p-3 m-5 shadow-lg">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="card-title">Sales Data</h2>
                <small class="text-gray-600">List of all sold items available in the system.</small>
            </div>

            <div id="salesTableContainer" class="mt-4">
                <!-- Trigger Button -->
                <div class="mb-3 justify-content-end gap-3">
                    <div class="mb-3 text-end">
                        <button type="button" id="btnAddRow" class="btn btn-outline-secondary">
                            <i class="bi bi-plus-lg"></i> Add Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales table will be dynamically inserted here -->
        <form action="POST" id="salesForm" enctype="multipart/form-data">
            <fieldset id="header" class="header d-none">
                <!-- Filter Card (Initially Hidden) -->
                <div id="filterCard" class="card shadow-lg mb-4">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="salesOrder" class="form-label">Sales Order</label>
                                <input type="text" id="salesOrder" name="salesOrder" class="form-control"
                                    placeholder="Enter sales order" value="<?= $generateSalesOrder ?>" required
                                    readonly>
                            </div>

                            <!-- Date Picker -->
                            <div class="col-md-4 mb-3">
                                <label for="salesDate" class="form-label">Sales Date</label>
                                <input type="text" id="salesDate" name="salesDate" class="form-control"
                                    placeholder="Select date range" required readonly>
                            </div>

                            <!-- Customer Dropdown -->
                            <div class="col-md-4 mb-3">
                                <label for="customer" class="form-label">Customer</label>
                                <select name="customer" id="customer" class="form-select" required>
                                    <option value="" disabled selected>Select customer</option>
                                    <?php foreach ($result as $customer): ?>
                                        <option value="<?= htmlspecialchars($customer['id']) ?>">
                                            <?= htmlspecialchars($customer['customer_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset id="detail" class="detail d-none">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">Product</div>
                            <div class="col-2 text-end">Price</div>
                            <div class="col-2 text-end">Quantity</div>
                            <div class="col-2">UOM</div>
                            <div class="col-2 text-end">Total</div>
                            <div class="col-1 text-center no-print">Action</div>
                        </div>
                    </div>

                    <!-- Tempat untuk baris data -->
                    <div id="detailBody" class="d-none"></div>
                </div>
            </fieldset>


            <div class="summary mt-4 border rounded shadow-lg p-3 d-none">
                <!-- Summary content will be dynamically inserted here -->
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-end">
                        <div class="row mb-3">
                            <div class="col">Subtotal :</div>
                            <div class="col text-end pe-3"><span id="subtotal">Rp0</span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">Tax (10%) :</div>
                            <div class="col text-end pe-3"><span id="tax">Rp0</span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col fw-bold">Discount :</div>
                            <div class="col text-end pe-3 fw-bold"><span id="discount">- Rp0</span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col fw-bold">Grand Total :</div>
                            <div class="col text-end pe-3 fw-bold"><span id="grandTotal">Rp0</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-3 d-none" id="formButtons">
                <button type="reset" id="resetBtn" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-counterclockwise"></i> Cancel
                </button>
                <button type="button" id="saveBtn" class="btn btn-outline-primary">
                    <i class="bi bi-save2"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card p-3 m-5 shadow-lg">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="card-title">Sales Records</h2>
                <small class="text-gray-600">List of all sold items available in the system.</small>
            </div>
        </div>

        <div class="container-fluid p-3">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <td>No.</td>
                        <td>Sales Order</td>
                        <td>Sales Date</td>
                        <td>Customer Name</td>
                        <td>Product Name</td>
                        <td>Quantity</td>
                        <td>Price</td>
                        <td>UOM</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($salesData)) {
                        echo '<tr><td colspan="9" class="text-center">Data tidak tersedia</td></tr>';
                    } else { ?>
                        <?php foreach ($salesData as $index => $sale): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <a href="#" class="text-decoration-none text-dark sales-order-link"
                                        data-sales-id="<?= $sale['sales_id'] ?>">
                                        <?= htmlspecialchars($sale['sales_order']) ?> <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                                <td><?= date('d-m-Y', strtotime($sale['sales_date'])) ?></td>
                                <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                                <td><?= htmlspecialchars($sale['products']) ?></td>
                                <td><?= $sale['qtys'] ?></td>
                                <td>Rp <?= number_format($sale['first_sales_price'], 2) ?></td>
                                <td><?= htmlspecialchars($sale['sales_uoms']) ?></td>
                                <td class="text-center no-print d-flex justify-content-start gap-3">
                                    <button class='btn btn-sm btn-outline-success printBtn'
                                        data-sales-id='<?= $sale['sales_id'] ?>'>
                                        <i class='bi bi-printer'></i> Print
                                    </button>
                                    <button class='btn btn-sm btn-outline-danger btnDelete'
                                        data-sale-id='<?= $sale['sales_id'] ?>' onclick='deleteSale(this)'>
                                        <i class='bi bi-trash3'></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal, letakkan di luar loop -->
    <div class="modal fade" id="salesDetailModal" tabindex="-1" aria-labelledby="salesDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesDetailModalLabel">Sales Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="salesDetailContent">
                    <!-- Konten detail akan dimuat di sini via JS -->
                    <p>Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Delete -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteSaleId" value="">
                    <div class="">
                        <h6>Are you sure you want to proceed?</h6>
                        <small class="text-danger">This action cannot be undone.</small>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
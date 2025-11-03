<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: auth/login.php");
    exit();
}

require_once $_SESSION["dir_root"] . "../Database/koneksi.php";
$site_root = $_SESSION["site_root"];

$sql = db()->prepare("SELECT * FROM product");
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("SELECT * FROM uom");
$sql->execute();
$uom_result = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = db()->prepare("SELECT * FROM supplier");
$sql->execute();
$supplier_result = $sql->fetchAll(PDO::FETCH_ASSOC);

$isEdit = isset($formData); // true kalau sedang edit

// generate purchase order like: PO/2025/09/13/0001 and sequence resets each day
$dateSegment = date('Y/m/d');   // used in visible purchase_order
$dbDateSegment = date('Ymd');   // used for predictable LIKE pattern (no slashes)

$stmt = db()->prepare("SELECT purchase_order FROM purchase_header WHERE purchase_order LIKE :like ORDER BY purchase_order DESC LIMIT 1");
$stmt->execute([':like' => 'PO/' . $dateSegment . '/%']);
$last = $stmt->fetch(PDO::FETCH_ASSOC);

if ($last && isset($last['purchase_order'])) {
    // expected format segments: PO/YYYY/MM/DD/NNNN
    $parts = explode('/', $last['purchase_order']);
    $lastSeq = intval(end($parts));
    $nextSeq = $lastSeq + 1;
} else {
    $nextSeq = 1;
}

$generatePurchaseOrder = 'PO/' . $dateSegment . '/' . sprintf('%04d', $nextSeq);
?>

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Form Purchased</title>
    <style>
        #items-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        #items-table th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light bg-dark bg-gradient shadow">
        <?php include "navbar.php"; ?>
    </nav>

    <!-- Main Content -->
    <div class="card p-4 p-md-5 m-3 m-md-5 shadow rounded-4 border-0 bg-light">
        <h3 class="text-center mb-4 text-primary fw-bold">
            <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-cart-check-fill' ?> me-2"></i>
            <?= $isEdit ? 'Update Purchased Order' : 'Create Purchased Order' ?>
        </h3>

        <form id="purchased-form" method="POST">
            <!-- HIDDEN: tanggal hari ini -->
            <input type="hidden" name="purchase_date" id="purchase-date" value="<?= date('Y-m-d') ?>">

            <!-- HIDDEN: purchase_id setelah insert header -->
            <input type="hidden" name="purchase_id" id="purchase-id" value="<?= $isEdit ? $formData['id'] : '' ?>">

            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $formData['id'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="purchased-order" class="form-label">Purchased Order</label>
                <input type="text" class="form-control text-muted" id="purchased-order" name="purchased_order"
                    value="<?= $isEdit ? htmlspecialchars($formData['purchased_order']) : $generatePurchaseOrder ?>"
                    readonly>
            </div>

            <div class="mb-4">
                <label for="supplier" class="form-label">Supplier</label>
                <select class="form-select" id="supplier" name="supplier" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($supplier_result as $row): ?>
                        <option value="<?= $row['id'] ?>" <?= $isEdit && $row['id'] == $formData['supplier_id'] ? 'selected' : '' ?>>
                            <?= $row['supplier_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr class="my-4">

            <!-- SECTION PRODUCT ITEMS -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Product Items</h5>
                    <button type="button" class="btn btn-primary btn-sm" id="add-item">
                        <i class="bi bi-plus-circle me-1"></i>Add Product
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Product</th>
                                <th style="width: 15%">QTY</th>
                                <th style="width: 15%">UOM</th>
                                <th style="width: 25%">Price</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <!-- Row pertama akan di-generate oleh JavaScript atau PHP untuk edit mode -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-<?= $isEdit ? 'warning' : 'success' ?> px-4" id="submit-button">
                    <i class="bi <?= $isEdit ? 'bi-save2' : 'bi-send-fill' ?> me-1"></i>
                    <?= $isEdit ? 'Update' : 'Submit' ?>
                </button>
                <button type="reset" class="btn btn-outline-secondary px-4 ms-2">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- <script src="../../js/setPurchases.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceInput = document.getElementById('price');
            const priceRawInput = document.getElementById('price_raw');

            function formatRibuan(angka) {
                return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function cleanFormat(angka) {
                return angka.replace(/\./g, '');
            }

            priceInput.addEventListener('input', function () {
                let raw = cleanFormat(this.value);
                if (isNaN(raw)) return;
                this.value = formatRibuan(raw);
                priceRawInput.value = raw;
            });

            // Trigger format saat halaman dimuat jika ada nilai
            if (priceInput.value) {
                const cleaned = cleanFormat(priceInput.value);
                priceInput.value = formatRibuan(cleaned);
                priceRawInput.value = cleaned;
            }
        });

        $(document).ready(function () {
            const purchaseId = <?= json_encode($_GET['id'] ?? '') ?>;
            const detailId = <?= json_encode($_GET['detail_id'] ?? '') ?>;

            if (purchaseId) {
                loadDetailData(purchaseId, detailId);
            }
        });

        // Create Purchase Data
        $(document).ready(function () {
            $('#purchased-form').on('submit', function (e) {
                e.preventDefault();

                // Collect header data
                const headerData = {
                    action: 'create_header',
                    purchased_order: $('#purchased-order').val(),
                    supplier_id: $('#supplier').val(),
                    purchase_date: $('#purchase-date').val(),
                };

                $.ajax({
                    type: 'POST',
                    url: base_url + '/public/crud/purchased.php',
                    data: headerData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 1) {
                            const purchaseId = response.data.purchase_id;
                            $('#purchase-id').val(purchaseId);

                            // Collect all items from table
                            const items = [];
                            $('#items-body tr').each(function () {
                                const product_id = $(this).find('select[name*="[product]"]').val();
                                const qty = $(this).find('input[name*="[qty]"]').val();
                                const uom_id = $(this).find('select[name*="[uom]"]').val();
                                const price = $(this).find('input[name*="[price]"]').val();

                                // Validate item before pushing (optional)
                                if (product_id && qty && uom_id && price) {
                                    items.push({
                                        purchase_id: purchaseId,
                                        product_id: product_id,
                                        qty: qty,
                                        uom_id: uom_id,
                                        price: price,
                                    });
                                }
                            });

                            // If no valid items, alert and stop
                            if (items.length === 0) {
                                alert('Please add at least one valid product item');
                                return;
                            }

                            // Function to send details one by one (or use Promise.all)
                            let successCount = 0;
                            let failCount = 0;

                            items.forEach((item, index) => {
                                $.ajax({
                                    type: 'POST',
                                    url: base_url + '/public/crud/purchased.php',
                                    data: { ...item, action: 'create_detail' },
                                    dataType: 'json',
                                    success: function (res) {
                                        if (res.status === 1) {
                                            successCount++;
                                        } else {
                                            failCount++;
                                            console.error('Failed item:', item, res.messages);
                                        }

                                        // After last item, give feedback
                                        if (successCount + failCount === items.length) {
                                            if (failCount === 0) {
                                                // alert('Data berhasil disimpan!');
                                                // Simpan pesan ke localStorage agar toast muncul setelah reload
                                                localStorage.setItem('toastMessage', 'Purchase order created successfully!');
                                                localStorage.setItem(
                                                    'toastType',
                                                    res.response === 200 ? 'success' : 'danger'
                                                )

                                                $('#purchased-form')[0].reset();
                                                // Redirect or reload
                                                window.location.href = base_url + '/public/purchased.php';
                                            } else {
                                                alert('Beberapa item gagal disimpan, cek console untuk detail.');
                                            }
                                        }
                                    },
                                    error: function () {
                                        failCount++;
                                        if (successCount + failCount === items.length) {
                                            alert('Terjadi kesalahan saat menyimpan detail.');
                                        }
                                    },
                                });
                            });
                        } else {
                            alert('Gagal simpan header: ' + response.messages);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Terjadi kesalahan saat menyimpan data.');
                    },
                });
            });
        });


        function loadDetailData(purchaseId, detailId) {
            $.ajax({
                url: base_url + '/public/crud/purchased.php',
                method: 'POST',
                data: {
                    action: 'update_detail',
                    purchase_id: purchaseId,
                    detail_id: detailId
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    if (response.status === 1) {
                        const detailData = response.data;  // single object expected
                        populateForm(detailData);
                    } else {
                        alert('Gagal mengambil data: ' + response.messages);
                    }
                },
                error: function () {
                    alert('Error saat mengambil data');
                }
            });
        }

        function populateForm(data) {
            // contoh mapping dari data ke input form
            $('#purchase-id').val(data.purchase_id || '');
            $('#purchased-order').val(data.purchase_order || '');
            $('#supplier').val(data.supplier_id || '');
            $('#product').val(data.product_id || '');
            $('#qty').val(data.purchase_qty || '');
            $('#uom').val(data.purchase_uom || '');
            $('#price_raw').val(data.purchase_price || '');

            // Untuk input price (format bisa disesuaikan)
            $('#price').val(data.purchase_price || '');

            // Kalau ada input hidden id detail
            $('input[name="id"]').val(data.id || '');

            // Tanggal, jika mau di-set
            $('#purchase-date').val(data.purchase_date || '');

            // Ganti heading dan button
            $('h3').html(`
                <i class="bi bi-pencil-square me-2"></i>
                Update Purchased Order
            `);
            $('#submit-button').removeClass('btn-success').addClass('btn-warning').html(`
                <i class="bi bi-save2 me-1"></i> Update
            `);
        }

        $(document).ready(function () {
            const purchaseId = <?= json_encode($_GET['id'] ?? '') ?>;
            if (purchaseId) {
                loadDetailData(purchaseId);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = 0;

            // Data untuk dropdown (dari PHP)
            const products = <?= json_encode($result) ?>;
            const uoms = <?= json_encode($uom_result) ?>;
            const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

            // Function untuk membuat row baru
            function addItemRow(data = null) {
                const index = itemIndex++;
                const row = document.createElement('tr');
                row.setAttribute('data-index', index);

                let productOptions = '<option value="">Select Product</option>';
                products.forEach(product => {
                    const selected = data && product.id == data.product_id ? 'selected' : '';
                    productOptions += `<option value="${product.id}" ${selected}>${product.product_name}</option>`;
                });

                // Build UOM options
                let uomOptions = '<option value="">Select UOM</option>';
                uoms.forEach(uom => {
                    const selected = data && uom.id == data.uom_id ? 'selected' : '';
                    uomOptions += `<option value="${uom.id}" ${selected}>${uom.uom_name}</option>`;
                });

                row.innerHTML = `
                    <td>
                        <select class="form-select form-select-sm" name="items[${index}][product]" required>
                            ${productOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" 
                            name="items[${index}][qty]" min="1" 
                            value="${data ? data.qty : ''}" required>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="items[${index}][uom]" required>
                            ${uomOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" 
                            name="items[${index}][price]" 
                            value="${data ? data.price : ''}" 
                            required placeholder="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;

                document.getElementById('items-body').appendChild(row);

                // Event listener untuk tombol remove
                row.querySelector('.remove-item').addEventListener('click', function () {
                    if (document.querySelectorAll('#items-body tr').length > 1) {
                        row.remove();
                    } else {
                        alert('Minimal harus ada 1 product item');
                    }
                });
            }

            // Tombol Add Item
            document.getElementById('add-item').addEventListener('click', function () {
                addItemRow();
            });

            // Initialize dengan 1 row kosong atau data edit
            if (isEdit) {
                // Jika mode edit, load data dari PHP
                // Anda perlu adjust ini sesuai struktur data edit Anda
                <?php if ($isEdit && isset($formData)): ?>
                    addItemRow({
                        product_id: '<?= $formData['product_id'] ?>',
                        qty: '<?= $formData['qty'] ?>',
                        uom_id: '<?= $formData['purchase_uom'] ?>',
                        price: '<?= $formData['price'] ?>'
                    });
                <?php endif; ?>
            } else {
                // Mode baru, tambah 1 row kosong
                addItemRow();
            }

            // Form validation
            document.getElementById('purchased-form').addEventListener('submit', function (e) {
                const itemRows = document.querySelectorAll('#items-body tr');
                if (itemRows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one product item');
                    return false;
                }
            });
        });

        // Toast
        function showToast(title, message, type = 'success') {
            const toastContainer =
                document.getElementById('toast-container') ||
                (() => {
                    const container = document.createElement('div')
                    container.id = 'toast-container'
                    container.style.position = 'fixed'
                    container.style.top = '20px'
                    container.style.right = '20px'
                    container.style.zIndex = '9999'
                    document.body.appendChild(container)
                    return container
                })()

            if (!document.getElementById('custom-toast-style')) {
                const style = document.createElement('style')
                style.id = 'custom-toast-style'
                style.innerHTML = `
                        .toast {
                            transition: all 0.4s ease-in-out, opacity 0.3s ease-in-out;
                            opacity: 0;
                            transform: translateY(20px);
                            color: #fff !important;               /* 🔥 semua teks putih */
                        }
                        .toast.show {
                            opacity: 1;
                            transform: translateY(0);
                        }
                        .toast .toast-header,
                        .toast .toast-body,
                        .toast strong,
                        .toast small {
                            color: #fff !important;               /* 🔥 pastikan semua elemen dalam toast putih */
                        }
                        .toast:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
                        }
                        .toast .btn-close-white {
                            filter: invert(1);                    /* biar tombol close tetap putih */
                        }
                        `
                document.head.appendChild(style)
            }

            const toast = document.createElement('div')
            toast.className = `toast fade align-items-center border-0 shadow-sm text-bg-${type}`
            toast.setAttribute('role', 'alert')
            toast.setAttribute('aria-live', 'assertive')
            toast.setAttribute('aria-atomic', 'true')
            toast.style.minWidth = '320px'
            toast.style.marginBottom = '12px'
            toast.style.borderRadius = '8px'
            toast.style.color = '#fff' // fallback warna putih

            toast.innerHTML = `
                        <div class="toast-header" style="background: transparent; border-bottom: none;">
                        <span class="rounded-circle me-2 d-flex align-items-center justify-content-center"
                            style="width:28px;height:28px;background: rgba(255,255,255,0.2);">
                            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'
                }" style="font-size:18px;color:#fff;"></i>
                        </span>
                        <strong class="me-auto" style="font-weight: 600;">${title}</strong>
                        <small style="opacity: 0.85;">Just now</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body" style="font-size: 1rem; font-weight: 400;">
                        ${message}
                        </div>
                    `

            toastContainer.appendChild(toast)
            const bsToast = new bootstrap.Toast(toast, {
                animation: true,
                autohide: true,
                delay: 5000,
            })
            bsToast.show()
            toast.addEventListener('hidden.bs.toast', () => toast.remove())
        }

        $(function () {
            const toastMsg = localStorage.getItem('toastMessage')
            const toastType = localStorage.getItem('toastType')

            if (toastMsg) {
                // Tampilkan toast setelah 500ms agar UI sudah siap
                setTimeout(() => {
                    showToast('Success', toastMsg, toastType || 'success')
                }, 500)

                // Hapus data supaya tidak muncul lagi
                localStorage.removeItem('toastMessage')
                localStorage.removeItem('toastType')
            }
        })
    </script>
</body>

</html>
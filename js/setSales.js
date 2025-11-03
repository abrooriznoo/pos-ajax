// window.addEventListener('DOMContentLoaded', function (e) {
//   const purchasePeriod = document.getElementById('date')
//   flatpickr(purchasePeriod, {
//     mode: 'range',
//     locale: 'id',
//     maxDate: 'today',
//     dateFormat: 'd-m-Y',
//     onChange: function (selectedDates, dataStr, instance) {
//       // console.log(selectedDates)
//       if (selectedDates.length === 2) {
//         $.post(
//           base_url + '/public/crud/sales.php',
//           {
//             action: 'read',
//             periodDate: [
//               formatDate(selectedDates[0]),
//               formatDate(selectedDates[1]),
//             ],
//           },
//           function (result) {
//             result = JSON.parse(result)
//             document.getElementById('purchase-header').innerHTML = ''
//             if (result.response === 200 && result.data.length > 0) {
//               document.getElementById('purchase-header').innerHTML =
//                 showPurchaseHeader(result.data)
//             }
//           }
//         )
//       }
//     },
//   })
// })

let rowCount = 0
let productSelect =
  `<option value="#" selected disabled>Select Product</option>` +
  productData
    .map(
      (product) =>
        `<option value="${product.id}">${product.product_name}</option>`
    )
    .join('')

let totalSalesProduct = []

function FormDate(date) {
  // Keluarkan dalam format YYYY-MM-DD
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

$(function () {
  $('#salesDate').val(FormDate(new Date()))
  //   generateRow()
})

function generateRow() {
  rowCount++
  const row = $(`
        <div class="row mb-2 align-items-center row-line p-3" data-index="${rowCount}">
            <!-- Product Select -->
            <div class="col-3">
                <select class="form-select form-select-sm product-select" name="productId[]">
                    ${productSelect}
                </select>
            </div>

            <!-- Price Display -->
            <div class="col-2 text-end">
                <span name="salesPrice[]" class="sales-price text-muted small d-block">Rp0.00</span>
            </div>

            <!-- Quantity Input -->
            <div class="col-2 d-flex justify-content-end">
                <input type="number" name="salesQty[]" class="form-control form-control-sm sales-qty text-end" min="1" value="0">
            </div>

            <!-- UOM Display -->
            <div class="col-2">
                <span name="salesUom[]" class="sales-uom text-muted small d-block">-</span>
            </div>

            <!-- Total Display -->
            <div class="col-2 text-end">
                <span name="salesTotal[]" class="sales-total text-muted small d-block">Rp0.00</span>
            </div>

            <!-- Remove Button -->
            <div class="col-1 text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row no-print">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `)

  $('#detailBody').append(row)
}

function formatRupiah(value) {
  return parseFloat(value || 0).toLocaleString('id-ID', {
    style: 'currency',
    currency: 'IDR',
  })
}

let grandTotals = 0

// Handler saat produk dipilih
$(document).on('change', '.product-select', function () {
  const row = $(this).closest('.row-line')
  const productId = $(this).val()
  const rowProduct = productData.find((product) => product.id == productId)
  const uom = uomData.find((uom) => uom.id == rowProduct?.uom_id)

  if (rowProduct) {
    row
      .find('.sales-price')
      .text(formatRupiah(rowProduct.sales_price.toString()))
    row.find('.sales-uom').text(uom ? uom.uom_name : '-')

    const qtyInput = row.find('.sales-qty')
    qtyInput.data('price', rowProduct.sales_price)

    // (Opsional) auto isi qty 1 saat produk dipilih
    qtyInput.val(1)

    // Hitung total langsung
    const total = rowProduct.sales_price * 1
    row.find('.sales-total').text(formatRupiah(total))

    // Simpan total per index
    const index = row.data('index')
    if (typeof index !== 'undefined') {
      totalSalesProduct[index] = total
    }

    // 👉 Tambahkan ini agar subtotal langsung dihitung:
    const subtotal = getSubtotal()
    $('#subtotal').text(formatRupiah(subtotal))

    // tax
    const tax = taxCalculation(subtotal)
    $('#tax').text(formatRupiah(tax))

    // discount
    const discount = discountCalculation(subtotal)
    $('#discount').text('- ' + formatRupiah(discount))

    // grand total
    const grandTotal = subtotal + tax - discount
    $('#grandTotal').text(formatRupiah(grandTotal))
    grandTotals = grandTotal
  } else {
    // Reset jika tidak ada produk
    row.find('.sales-price, .sales-uom, .sales-total').text('-')
    row.find('.sales-qty').val('')
  }
})

// Handle saat tombol tambah baris diklik
$('#btnAddRow').on('click', function () {
  generateRow()

  // Tampilkan summary jika sebelumnya tersembunyi
  $('.header').removeClass('d-none')
  $('.detail').removeClass('d-none')
  $('#detailBody').removeClass('d-none')
  $('.summary').removeClass('d-none')
  $('#formButtons').removeClass('d-none')
})

// Handler saat tombol hapus baris diklik
$(document).on('click', '.btn-remove-row', function () {
  const row = $(this).closest('.row-line')
  const index = row.data('index')

  if (typeof index !== 'undefined') {
    delete totalSalesProduct[index] // 🔁 hapus dari array
  }

  row.remove()

  // Hitung ulang subtotal dan tampilkan
  const subtotal = getSubtotal()
  $('#subtotal').text(formatRupiah(subtotal))

  // Hitung ulang diskon
  const tax = taxCalculation(subtotal)
  $('#tax').text(formatRupiah(tax))

  // discount
  const discount = discountCalculation(subtotal)
  $('#discount').text('- ' + formatRupiah(discount))

  // grand total
  const grandTotal = subtotal + tax - discount
  $('#grandTotal').text(formatRupiah(grandTotal))
  grandTotals = grandTotal
})

// Handler saat qty diubah
$(document).on('input', '.sales-qty', function () {
  const qty = parseFloat($(this).val() || 0)
  const price = parseFloat($(this).data('price') || 0)
  const total = qty * price

  const row = $(this).closest('.row-line')
  row.find('.sales-total').text(formatRupiah(total))

  const index = row.data('index')
  if (typeof index !== 'undefined') {
    totalSalesProduct[index] = total
  }

  // Hitung dan tampilkan subtotal
  const subtotal = getSubtotal()
  //   console.log('Subtotal:', formatRupiah(subtotal))
  $('#subtotal').text(formatRupiah(subtotal))

  // Hitung dan tampilkan tax
  const tax = taxCalculation(subtotal)
  $('#tax').text(formatRupiah(tax))

  // discount
  const discount = discountCalculation(subtotal)
  $('#discount').text('- ' + formatRupiah(discount))

  // grand total
  const grandTotal = subtotal + tax - discount
  $('#grandTotal').text(formatRupiah(grandTotal))
  grandTotals = grandTotal
})

function getSubtotal() {
  let subtotal = 0

  $('.sales-total').each(function () {
    const valueText = $(this)
      .text()
      .replace(/[Rp\s]/g, '') // hilangkan Rp dan spasi
      .replace(/\./g, '') // hilangkan titik ribuan
      .replace(',', '.') // ganti koma desimal ke titik

    const value = parseFloat(valueText) || 0
    subtotal += value
  })

  return subtotal
}

// Hitung Tax 10%
function taxCalculation(subtotal) {
  const taxRate = 0.1 // 10%
  return subtotal * taxRate
}

// Hitung diskon jika subtotal > Rp1.000.000
function discountCalculation(subtotal) {
  const maxDiscount = 200000
  let discount = 0

  if (subtotal > 5_000_000 && subtotal <= 5_500_000) {
    discount = 150_000 // 15%
  } else if (subtotal > 2_000_000 && subtotal <= 5_000_000) {
    discount = 100_000 // 10%
  } else if (subtotal > 1_000_000 && subtotal <= 2_000_000) {
    discount = 50_000 // 5%
  }

  // Batasi ke maksimum diskon
  if (subtotal > 5_500_000) {
    discount = maxDiscount
  }

  return discount
}

$('#resetBtn').on('click', function () {
  // Clear all rows
  $('#detailBody').empty()
  totalSalesProduct = []
  rowCount = 0
  // Reset summary
  $('#subtotal').text(formatRupiah(0))
  $('#tax').text(formatRupiah(0))
  $('#discount').text('- ' + formatRupiah(0))
  $('#grandTotal').text(formatRupiah(0))

  // Close summary section
  $('.header').addClass('d-none')
  $('.detail').addClass('d-none')
  $('#detailBody').addClass('d-none')
  $('.summary').addClass('d-none')
  $('#formButtons').addClass('d-none')
})

// CRUD
$('#salesForm').on('click', '#saveBtn', function (e) {
  e.preventDefault()

  const rows = []
  $('#detailBody .row-line').each(function () {
    const row = $(this)
    const qty = parseFloat(row.find('.sales-qty').val() || 0)
    if (qty > 0) {
      const productId = row.find('.product-select').val()
      const rowProduct = productData.find((product) => product.id == productId)
      if (rowProduct) {
        rows.push({
          productId: productId,
          // salesPrice: grandTotals,
          salesQty: qty,
          salesTotal: grandTotals,
          uomId: rowProduct.uom_id,
        })
      }
    }
  })

  $.ajax({
    url: base_url + '/api/ClassRouter.php',
    method: 'POST',
    data: JSON.stringify({
      action: 'save',
      module: 'sales',
      salesDate: $('#salesDate').val(),
      salesOrder: $('#salesOrder').val(),
      customerId: $('#customer').val(),
      items: rows,
    }),
    contentType: 'application/json',
    success: (result) => {
      result = JSON.parse(result)
      console.log(result)
      if (result.response === 201) {
        // Simpan pesan sukses ke localStorage sebelum reload
        localStorage.setItem('toastMessage', result.message)
        localStorage.setItem('toastType', 'success')

        // Reload halaman
        location.reload()
      } else {
        // Simpan pesan sukses ke localStorage sebelum reload
        localStorage.setItem('toastMessage', result.message)
        localStorage.setItem('toastType', 'danger')

        // Reload halaman
        location.reload()
      }
    },
    error: (xhr) => {
      console.log('Error details:', xhr.responseText)
      alert('AJAX error: ' + xhr.statusText)
    },
  })
})

// Handle print button
$(document).on('click', '.printBtn', function () {
  const salesId = $(this).data('sales-id')

  if (!salesId) {
    alert('Sales ID tidak ditemukan.')
    return
  }

  // Buka halaman cetak dan kirim sales_id lewat query string
  window.open(
    base_url + `/public/components/printSales.php?sales_id=${salesId}`,
    '_blank'
  )
})

//Handle delete button
function deleteSale(button) {
  const salesId = $(button).data('sale-id')

  if (!salesId) {
    alert('Sales ID tidak ditemukan.')
    return
  }

  // Simpan ID ke input hidden di modal
  $('#deleteSaleId').val(salesId)

  // Tampilkan modal konfirmasi
  const modal = new bootstrap.Modal(
    document.getElementById('confirmDeleteModal')
  )
  modal.show()
}

// Saat tombol konfirmasi Delete di modal ditekan
$('#confirmDeleteBtn').on('click', function () {
  const salesId = $('#deleteSaleId').val()

  if (!salesId) {
    alert('Sales ID tidak ditemukan.')
    return
  }

  // Tutup modal terlebih dahulu
  const modalEl = document.getElementById('confirmDeleteModal')
  const modal = bootstrap.Modal.getInstance(modalEl)
  modal.hide()

  // Kirim AJAX request
  $.ajax({
    url: base_url + '/api/ClassRouter.php',
    method: 'POST',
    data: JSON.stringify({
      action: 'delete',
      module: 'sales',
      salesId: salesId,
    }),
    contentType: 'application/json',
    success: (result) => {
      try {
        result = JSON.parse(result)
      } catch (e) {
        console.error('Invalid JSON:', result)
        alert('Server error: invalid response')
        return
      }

      // Simpan pesan ke localStorage agar toast muncul setelah reload
      localStorage.setItem('toastMessage', result.message)
      localStorage.setItem(
        'toastType',
        result.response === 200 ? 'success' : 'danger'
      )

      // Reload halaman
      location.reload()
    },
    error: (xhr) => {
      console.error('Error details:', xhr.responseText)
      alert('AJAX error: ' + xhr.statusText)
    },
  })
})

// Detail Sales
$(document).on('click', '.sales-order-link', function (e) {
  e.preventDefault()

  const salesId = $(this).data('sales-id')
  $('#salesDetailContent').html('<p>Loading...</p>')

  const salesModal = new bootstrap.Modal(
    document.getElementById('salesDetailModal')
  )
  salesModal.show()

  $.ajax({
    url: base_url + '/api/ClassRouter.php',
    method: 'GET',
    data: {
      action: 'detailSales',
      module: 'sales',
      salesId: salesId,
    },
    success: function (response) {
      const salesDetails = JSON.parse(response)
      // Asumsikan response sudah di JSON.parse otomatis oleh jQuery jika header content-type JSON
      if (salesDetails.status === 'success') {
        const salesData = salesDetails.data.salesData
        const grandTotal = salesDetails.data.salesData[0].detail_sales_price
        const subtotal = salesDetails.data.salesData.reduce(
          (sum, item) => sum + parseFloat(item.total_price),
          0
        )

        if (salesData.length === 0) {
          $('#salesDetailContent').html('<p>Data tidak ditemukan.</p>')
          return
        }

        let html = `<table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Product Name</th>
                          <th>Quantity</th>
                          <th>Price</th>
                          <th>UOM</th>
                          <th>Total Price</th>
                        </tr>
                      </thead>
                      <tbody>`

        salesData.forEach((item, idx) => {
          html += `<tr>
                    <td>${idx + 1}</td>
                    <td>${item.product_name}</td>
                    <td>${item.sales_qty}</td>
                    <td>Rp ${parseFloat(item.product_price).toLocaleString(
                      'id-ID'
                    )}</td>
                    <td>${item.uom_name}</td>
                    <td>Rp ${parseFloat(item.total_price).toLocaleString(
                      'id-ID'
                    )}</td>
                  </tr>`
        })

        let discount = 0
        const maxDiscount = 300000

        if (subtotal > 5500000) {
          discount = maxDiscount
        } else if (subtotal > 5000000) {
          discount = 150000
        } else if (subtotal > 2000000) {
          discount = 100000
        } else if (subtotal > 1000000) {
          discount = 50000
        }

        const tax = subtotal * 0.1
        const finalTotal = subtotal + tax - discount

        html += `
          </tbody></table>
          <div class="row mt-4">
            <div class="col-8"></div>
            <div class="col-4">
              <div class="text-start mb-1">
                <strong>Subtotal:</strong>
                <span class="float-end">Rp ${subtotal.toLocaleString(
                  'id-ID'
                )}</span>
              </div>
              <div class="text-start mb-1">
                <strong>Tax (10%):</strong>
                <span class="float-end">Rp ${tax.toLocaleString('id-ID')}</span>
              </div>
              <div class="text-start mb-1">
                <strong>Diskon:</strong>
                <span class="float-end text-danger">- Rp ${discount.toLocaleString(
                  'id-ID'
                )}</span>
              </div>
              <div class="text-start border-top pt-2 mt-2">
                <strong>Grand Total:</strong>
                <span class="float-end fw-bold">Rp ${finalTotal.toLocaleString(
                  'id-ID'
                )}</span>
              </div>
            </div>
          </div>
        `

        $('#salesDetailContent').html(html)
        showToast('Success', 'Data berhasil dimuat.', 'success')
      } else {
        $('#salesDetailContent').html(
          '<p>Gagal memuat data: ' + response.message + '</p>'
        )
      }
    },
    error: function () {
      $('#salesDetailContent').html(
        '<p>Terjadi kesalahan saat memuat data.</p>'
      )
    },
  })
})

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
        <i class="bi bi-${
          type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'
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

// Toggle Filter Card
// document
//   .getElementById('toggleFilterBtn')
//   .addEventListener('click', function () {
//     const card = document.getElementById('filterCard')

//     if (card.classList.contains('d-none')) {
//       card.classList.remove('d-none')
//       this.classList.remove('btn-outline-primary')
//       this.classList.add('btn-outline-danger')
//       this.innerHTML = '<i class="bi bi-x-circle"></i> Close Filter'
//     } else {
//       card.classList.add('d-none')
//       this.classList.remove('btn-outline-danger')
//       this.classList.add('btn-outline-primary')
//       this.innerHTML = '<i class="bi bi-funnel"></i> Filter'
//     }
//   })

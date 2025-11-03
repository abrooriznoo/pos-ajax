window.addEventListener('DOMContentLoaded', function (e) {
  const purchasePeriod = document.getElementById('purchase-periode')
  flatpickr(purchasePeriod, {
    mode: 'range',
    locale: 'id',
    maxDate: 'today',
    dateFormat: 'd-m-Y',
    onChange: function (selectedDates, dataStr, instance) {
      // console.log(selectedDates)
      if (selectedDates.length === 2) {
        // $.post(
        //   base_url + '/public/crud/purchased.php',
        //   {
        //     action: 'read',
        //     periodDate: [
        //       formatDate(selectedDates[0]),
        //       formatDate(selectedDates[1]),
        //     ],
        //   },
        //   // function (result) {
        //   //   // console.log(result)
        //   //   result = JSON.parse(result)

        //   //   const purchasedDetail = document.getElementById('purchase-detail')
        //   //   if (purchasedDetail) {
        //   //     purchasedDetail.innerHTML = result.data
        //   //   } else {
        //   //     console.warn('Element #purchase-detail not found')
        //   //   }

        //   //   if (result.response === 200 && result.data.length > 0) {
        //   //     const purchaseHeader = document.getElementById('purchase-header')
        //   //     if (purchaseHeader) {
        //   //       purchaseHeader.innerHTML = showPurchaseHeader(result.data)
        //   //     } else {
        //   //       console.warn('Element #purchase-header not found')
        //   //     }
        //   //   }
        //   // }
        //   function (result) {
        //     result = JSON.parse(result)
        //     const purchaseHeaderDiv = document.getElementById('purchase-header')
        //     const beforeLoadHeader =
        //       document.getElementById('before-load-header')
        //     const purchaseDataSection = document.getElementById(
        //       'purchase-data-section'
        //     )

        //     // Kosongkan isi sebelumnya
        //     purchaseHeaderDiv.innerHTML = ''

        //     if (result.response === 200 && result.data.length > 0) {
        //       // Tampilkan data purchase
        //       purchaseHeaderDiv.innerHTML = showPurchaseHeader(result.data)

        //       // Sembunyikan tulisan sebelum load
        //       if (beforeLoadHeader) beforeLoadHeader.classList.add('d-none')

        //       // Tampilkan section purchase
        //       if (purchaseDataSection)
        //         purchaseDataSection.classList.remove('d-none')
        //     } else {
        //       // Jika data kosong, tetap tampilkan tulisan sebelum load
        //       if (beforeLoadHeader) beforeLoadHeader.classList.remove('d-none')
        //       if (purchaseDataSection)
        //         purchaseDataSection.classList.add('d-none')
        //     }
        //   }
        // )
        $.post(
          base_url + '/public/crud/purchased.php',
          {
            action: 'read',
            periodDate: [
              formatDate(selectedDates[0]),
              formatDate(selectedDates[1]),
            ],
          },
          function (result) {
            console.log(result) // Debug response
            try {
              result = JSON.parse(result)
              const purchaseHeaderDiv =
                document.getElementById('purchase-header')
              const beforeLoadHeader =
                document.getElementById('before-load-header')
              const purchaseDataSection = document.getElementById(
                'purchase-data-section'
              )

              purchaseHeaderDiv.innerHTML = ''

              if (result.response === 200 && result.data.length > 0) {
                purchaseHeaderDiv.innerHTML = showPurchaseHeader(result.data)

                if (beforeLoadHeader) beforeLoadHeader.classList.add('d-none')

                if (purchaseDataSection)
                  purchaseDataSection.classList.remove('d-none')
              } else {
                if (beforeLoadHeader)
                  beforeLoadHeader.classList.remove('d-none')
                if (purchaseDataSection)
                  purchaseDataSection.classList.add('d-none')
              }
            } catch (error) {
              console.error('Error parsing JSON:', error) // Log the parsing error
            }
          }
        )
      }
    },
  })

  const tableHeader = document.getElementById('purchase-header')
  if (tableHeader) {
    tableHeader.addEventListener('click', function (e) {
      if (e.target && e.target.nodeName === 'TD') {
        const selectedRow = e.target.closest('tr.hoverable')
        const purchaseId = selectedRow.getAttribute('data-id')
        // Remove 'selected' class from all rows
        document
          .querySelectorAll('#table-purchased tbody tr.hoverable')
          .forEach((row) => {
            row.classList.remove('selected')
          })
        // Add 'selected' class to the clicked row
        selectedRow.classList.add('selected')
        // Fetch and display purchase details
        $.post(
          base_url + '/public/crud/purchased.php',
          {
            action: 'detail',
            id: purchaseId,
          },
          function (result) {
            result = JSON.parse(result)
            // console.log(result.data)
            document.getElementById('purchase-detail').innerHTML = ''
            if (result.response === 200 && result.data.length > 0) {
              showToast('Success', 'Data berhasil dimuat.', 'success')

              document.getElementById('purchase-detail').innerHTML =
                showPurchaseDetail(result.data)
            }
          }
        )
      }
    })
  } else {
    console.warn('Element #purchase-header not found')
  }

  let currentPurchaseId = null

  const btnAddItem = document.getElementById('btn-add-item')
  const modalPurchaseIdInput = document.getElementById('modal-purchase-id')

  if (tableHeader) {
    tableHeader.addEventListener('click', function (e) {
      if (e.target && e.target.nodeName === 'TD') {
        const selectedRow = e.target.closest('tr.hoverable')
        const purchaseId = selectedRow.getAttribute('data-id')

        if (purchaseId) {
          currentPurchaseId = purchaseId
          modalPurchaseIdInput.value = purchaseId
          btnAddItem.disabled = false
        }
      }
    })
  }
})

function showPurchaseHeader(data) {
  let table = ''
  table += `
    <table id="table-purchased" class="table table-bordered table-sm">
      <thead class="table-light">
        <tr>
          <th scope="col" style="width: 45px;">No.</th>
          <th scope="col">Date</th>
          <th scope="col">Supplier</th>
          <th scope="col">PO</th>
          <th scope="col">Amount</th>
        </tr>
      </thead>
      <tbody>
  `

  data.forEach((item, index) => {
    const dateParts = item.purchase_date.split('-')
    const formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`
    const formatTotal = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    })

    table += `
      <tr class="hoverable" data-id="${item.id}">
        <td>${index + 1}.</td>
        <td>${(item.purchase_date = formattedDate)}</td>
        <td>${item.supplier_name}</td>
        <td>${item.purchase_order}</td>
        <td>${(item.totalpurchase = formatTotal.format(
          item.totalpurchase
        ))}</td>
      </tr>
    `
  })

  table += `</tbody></table>`
  return table
}

$('#btn-add-item').on('click', function () {
  const purchaseId = $(this).data('purchase-id')
  // console.log('Add Item for purchase_id:', purchaseId)

  // Misal kamu mau set hidden input di modal add item supaya tahu purchase_id
  $('#modalAddItem input[name="purchase_id"]').val(purchaseId)
})

function showPurchaseDetail(data) {
  const purchaseId = $(this).data('purchase_id')
  let table = ''
  table += `
    <div class="d-flex justify-content-end align-items-center mb-2">
      <button class="btn btn-light btn-sm" id="btn-add-item" data-purchase-id="${purchaseId}" data-bs-toggle="modal" data-bs-target="#modalAddItem">
        <i class="bi bi-plus"></i> Add Item
      </button> 
    </div>
    <table id="table-purchased-detail" class="table table-bordered table-sm">
      <thead class="table-light">
        <tr>
          <th scope="col">Product Name</th>
          <th scope="col">Price</th>
          <th scope="col">Quantity</th>
          <th scope="col">UOM</th>
          <th scope="col">Total</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
  `

  data.forEach((item) => {
    const formatTotal = new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    })

    table += `
      <tr class="hoverable" data-id="${item.detail_id}">
        <td>${item.product_name}</td>
        <td>${formatTotal.format(item.purchase_price)}</td>
        <td>${item.purchase_qty}</td>
        <td>${item.uom_name}</td>
        <td>${formatTotal.format(item.purchase_price * item.purchase_qty)}</td>
        <td>
          <div class="d-flex gap-1">
            <button type="button" 
                class="btn btn-sm bg-secondary bg-gradient shadow-lg text-white btn-edit-item" 
                data-purchase-id="${item.purchase_id}" 
                data-detail-id="${item.detail_id}" 
                data-bs-toggle="modal" 
                data-bs-target="#modalEditItem">
                <i class="bi bi-pencil-square"></i>
            </button>
            <button type="button" 
                class="btn btn-sm bg-danger bg-gradient shadow-lg text-white btn-delete-item"
                data-purchase-id="${item.purchase_id}"
                data-detail-id="${item.detail_id}"
                data-bs-toggle="modal"
                data-bs-target="#modalDeleteItem">
                <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `

    $(document).on('click', '.btn-delete-item', function () {
      const detailId = $(this).data('detail-id')
      // console.log('Delete item with detail_id:', detailId)

      $('#delete-detail-id').val(detailId)
    })
  })

  table += `</tbody></table>`
  return table
}

$(document).on('click', '.btn-edit-item', function () {
  const purchaseId = $(this).data('purchase-id')
  const detailId = $(this).data('detail-id')

  // console.log('Edit item with purchase_id:', purchaseId, 'and detail_id:', detailId)

  // Panggil fungsi untuk load data detail dan isi form
  loadDetailData(purchaseId, detailId)
})

function loadDetailData(purchaseId, detailId) {
  $.ajax({
    url: base_url + '/public/crud/purchased.php',
    method: 'POST',
    data: {
      action: 'update_detail',
      // purchase_id: purchaseId,
      detail_id: detailId,
    },
    dataType: 'json',
    success: function (response) {
      if (response.status === 1) {
        $('#edit-purchase-id').val(response.data.purchase_id)
        $('#edit-detail-id').val(response.data.id)
        $('#edit-product-select').val(response.data.product_id)
        $('#edit-item-price').val(response.data.purchase_price)
        $('#edit-item-quantity').val(response.data.purchase_qty)
        $('#edit-item-uom').val(response.data.purchase_uom)
      } else {
        alert('Gagal memuat data: ' + response.messages)
      }
    },
    error: function () {
      alert('Error saat memuat data.')
    },
  })
}

function loadPurchaseDetail(purchaseId) {
  $.post(
    base_url + '/public/crud/purchased.php',
    { action: 'detail', id: purchaseId },
    function (result) {
      result = JSON.parse(result)
      document.getElementById('purchase-detail').innerHTML = ''
      if (result.response === 200 && result.data.length > 0) {
        document.getElementById('purchase-detail').innerHTML =
          showPurchaseDetail(result.data)
      }
    }
  )
}

$('#form-add-item').on('submit', function (e) {
  e.preventDefault()
  // Validasi sederhana, bisa diperluas
  if (!this.checkValidity()) {
    e.stopPropagation()
    $(this).addClass('was-validated')
    return
  }

  const formData = $(this).serialize()

  $.ajax({
    url: base_url + '/public/crud/purchased.php',
    method: 'POST',
    data: formData + '&action=create_detail',
    dataType: 'json',
    success: function (response) {
      if (response.status === 1) {
        showToast('Success', 'Data berhasil disimpan!', 'success')

        $('#modalAddItem').modal('hide')

        // Reload purchase detail table, misal:
        loadPurchaseDetail($('#modal-purchase-id').val())
      } else {
        alert('Gagal menyimpan data: ' + response.messages)
        // console.log(formData)
      }
    },
    error: function () {
      alert('Error saat menyimpan data.')
    },
  })
})

$('#form-edit-item').on('submit', function (e) {
  e.preventDefault()

  // Validasi sederhana, bisa diperluas
  if (!this.checkValidity()) {
    e.stopPropagation()
    $(this).addClass('was-validated')
    return
  }

  const formData = $(this).serialize()

  $.ajax({
    url: base_url + '/public/crud/purchased.php',
    method: 'POST',
    data: formData + '&action=save_update_detail',
    dataType: 'json',
    success: function (response) {
      if (response.status === 1) {
        // alert('Data berhasil disimpan!')
        showToast('Success', 'Data berhasil di update!', 'success')

        $('#modalEditItem').modal('hide')

        // Reload purchase detail table, misal:
        loadPurchaseDetail($('#edit-purchase-id').val())
      } else {
        alert('Gagal menyimpan data: ' + response.messages)
        console.log(formData)
      }
    },
    error: function () {
      alert('Error saat menyimpan data.')
    },
  })
})

$('#form-delete-item').on('submit', function (e) {
  e.preventDefault()

  const formData = $(this).serialize() + '&action=delete_detail'

  $.ajax({
    url: base_url + '/public/crud/purchased.php',
    method: 'POST',
    data: formData,
    dataType: 'json',
    success: function (response) {
      if (response.status === 1) {
        showToast('Success', 'Item berhasil dihapus!', 'success')

        $('#modalDeleteItem').modal('hide')

        // Refresh tabel detail
        loadPurchaseDetail($('#delete-purchase-id').val())
      } else {
        alert('Gagal menghapus item: ' + response.messages)
      }
    },
    error: function () {
      alert('Terjadi kesalahan saat menghapus data.')
    },
  })
})

function formatDate(theDate) {
  const formattedDate = [
    theDate.getFullYear(),
    (theDate.getMonth() + 1).toString().padStart(2, '0'),
    theDate.getDate().toString().padStart(2, '0'),
  ].join('-')
  return formattedDate
}

document
  .querySelectorAll('#table-purchased tbody tr.hoverable')
  .forEach((tr) => {
    tr.addEventListener('click', () => {
      document
        .querySelectorAll('#table-purchased tbody tr.hoverable')
        .forEach((row) => {
          row.classList.remove('selected')
        })
      tr.classList.add('selected')
      console.log('Row clicked:', tr)
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

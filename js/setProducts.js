// JS
// document.addEventListener('DOMContentLoaded', function () {
// })

// JQUERY
// $(function () {
//   // Event ketika tombol yang memicu modal diklik
//   $('[data-bs-target="#modalProduct"]').on('click', function () {
//     const btnId = $(this).attr('id') // Dapatkan ID tombol yang diklik

//     // Menyesuaikan teks modalTitle berdasarkan ID tombol yang dipilih
//     const modal = $('#modalProduct') // Mendapatkan referensi modal
//     const modalTitle = modal.find('#modalHeader h1') // Menemukan elemen modalTitle
//     const modalBody = modal.find('.modal-body .card-body .form-product') // Menemukan elemen modalBody
//     const modalFooter = modal.find('.modal-footer .button-group') // Menemukan elemen modalFooter
//     const categorySelect = $('#product-category') // Pastikan elemen select ada

//     // Bersihkan isi modalBody jika perlu
//     modalBody.empty()
//     modalFooter.empty()

//     switch (btnId) {
//       case 'btn-add-products':
//         modalTitle.text('Add Products')
//         modalFooter.append(
//           `<button type="button" class="btn bg-primary bg-gradient text-white" id="save-edit-product">Save</button>
//           <button type="button" class="btn bg-light bg-gradient ms-2" data-bs-dismiss="modal">Cancel</button>`
//         )

//         // Menangani klik tombol simpan untuk menambahkan produk
//         $('#save-add-product').on('click', function () {
//           const productName = $('#product-name').val()
//           const categoryId = $('#product-category').val() //  Pastikan categoryId sudah terdefinisi
//           const productUOM = $('#product-uom').val()

//           if (productName && productUOM) {
//             $.ajax({
//               url: base_url + '/public/crud/products.php',
//               method: 'POST',
//               data: {
//                 action: 'create',
//                 product_name: productName,
//                 product_category: categoryId, // Pastikan categoryId sudah terdefinisi
//                 product_uom: productUOM,
//               },
//               success: function (response) {
//                 const result = JSON.parse(response)
//                 if (result.status === 1) {
//                   alert(result.messages)
//                   // Update UI or close modal
//                   location.reload() // reload the page after successful addition
//                 } else {
//                   alert('Error adding product')
//                 }
//               },
//             })
//           } else {
//             alert('Please fill out all fields')
//           }
//         })
//         break

//       case 'btn-edit-products':
//         modalTitle.text('Edit Products')
//         // Ambil data produk berdasarkan productId dari baris yang dipilih
//         const selectedRow = document.querySelector(
//           '#data-product tbody tr.selected'
//         )
//         if (selectedRow) {
//           const productId = selectedRow.dataset.id
//           const productName = selectedRow.dataset.product
//           const UomId = selectedRow.dataset.uom
//           const UomName = selectedRow.dataset.uomName
//           const categoryId = selectedRow.dataset.categoryId
//           const categoryName = selectedRow.dataset.categoryName
//           console.log('Category ID:', categoryId)
//           console.log('Category Name:', categoryName)
//           console.log('UOM ID:', UomId)
//           console.log('UOM Name:', UomName)

//           // Isi modalBody dengan form edit produk
//           modalBody.append(`<div>
//             <div>
//               <input type="text" class="form-control" id="product-id" value="${productId}" hidden>
//             </div>
//             <div class="mb-3">
//               <label for="product-name" class="form-label">Product Name</label>
//               <input type="text" class="form-control" id="product-name" value="${productName}">
//             </div>
//             <div class="mb-3">
//               <label for="product-category" class="form-label">Product Category</label>
//               <select name="product-category" id="product-category" class="form-select shadow-sm">
//                 <?php foreach ($result as $category): ?>
//                   <option value="${categoryId}" selected}>${categoryName}</option>
//                 <?php endforeach; ?>
//               </select>
//             </div>
//             <div class="mb-3">
//               <label for="product-uom" class="form-label">Product UOM</label>
//               <select name="product-category" id="product-category" class="form-select shadow-sm">
//                 <?php foreach ($result as $category): ?>
//                   <option value="${UomId}" selected}>${UomName}</option>
//                 <?php endforeach; ?>
//               </select>
//             </div>
//           </div>`)
//         } else {
//           modalBody.append(
//             '<div class="text-danger">Pilih produk terlebih dahulu.</div>'
//           )
//         }
//         modalFooter.append(
//           `<button type="button" class="btn bg-primary bg-gradient text-white" id="save-edit-product">Update</button>
//           <button type="button" class="btn bg-light bg-gradient ms-2" data-bs-dismiss="modal">Cancel</button>`
//         )
//         break
//       case 'btn-delete-products':
//         modalTitle.text('Delete Products')
//         modalBody.append('<p>Konfirmasi penghapusan produk.</p>')
//         break
//       default:
//         modalTitle.text('Product Details') // Default, jika tidak ada tombol yang terpilih
//         break
//     }
//   })
// })

let produkId, produkName, uomId, categoryId

$(function () {
  let triggerButton = null

  // Tangkap klik button yang punya data-bs-target="#modalProduct"
  $('[data-bs-target="#modalProduct"]').on('click', function () {
    triggerButton = $(this)
  })

  $('#modalProduct').on('shown.bs.modal', function () {
    if (!triggerButton) return

    const modal = $(this)
    const modalTitle = modal.find('#modalProductLabel')
    const btnId = triggerButton.attr('id')

    // Reset dulu isi form
    $('#product-id').val('')
    $('#product-name').val('')
    $('#product-uom').val('')
    $('#product-category').val('')

    switch (btnId) {
      case 'btn-edit-products':
        // Ambil data dari tombol trigger, misal via data attributes
        const selectedRow = document.querySelector(
          '#data-product tbody tr.selected'
        )

        if (selectedRow) {
          produkId = selectedRow.dataset.id
          produkName = selectedRow.dataset.name
          produkUom = selectedRow.dataset.uom
          categoryId = selectedRow.dataset.category
        } else {
          produkId = ''
          produkName = ''
          produkUom = ''
          categoryId = ''
        }

        console.log('Category ID:', categoryId)
        console.log('Product ID:', produkId)
        console.log('Product Name:', produkName)
        console.log('Product UOM:', produkUom)

        modalTitle.text('Edit Product: ' + produkName)
        $('#product-id').val(produkId)
        $('#product-name').val(produkName)
        $('#product-uom').val(produkUom)
        $('#product-category').val(categoryId)
        $('#btn-submit').text('Update Product')
        break

      case 'btn-delete-products':
        modalTitle.text('Delete Product: ' + produkName)
        $('#product-id').val(produkId)
        $('#product-name').val(produkName)

        // Sembunyikan semua elemen input dan label (dalam .mb-3)
        $('#form-product .mb-3').hide()

        // Tampilkan pesan konfirmasi
        $('#delete-confirmation-message').removeClass('d-none')

        // Ganti teks tombol
        $('#btn-submit')
          .text('Delete Product')
          .removeClass('btn-primary')
          .addClass('btn-danger')
        break

      default:
        modalTitle.text('Add New Product')
        $('#btn-submit').text('Add Product')
        break
    }
  })
})

function crudProduct(action, categoryId) {
  const formData = $('#form-product').serialize()
  $.ajax({
    url: base_url + '/public/crud/products.php',
    type: 'POST',
    dataType: 'json',
    data: {
      action: action,
      category: categoryId,
      productData: formData,
    },
  })
    .done(function (result) {
      if (result['response'] === 200) {
        // console.log(result['data'])
        $('#data-product').html('')
        if (result['data'] != null) {
          if (Array.isArray(result['data']) && result['data'].length > 0) {
            $('#data-product').html(generateTableProduct(result['data']))
          } else {
            // alert(result['data'])
            // Simpan pesan sukses ke localStorage sebelum reload
            localStorage.setItem('toastMessage', result.messages)
            localStorage.setItem('toastType', 'danger')

            // Reload halaman
            location.reload()

            $('#modalProduct').modal('hide')
          }
        } else {
          $('#modalProduct').modal('hide')
          // Simpan pesan sukses ke localStorage sebelum reload
          localStorage.setItem('toastMessage', result.messages)
          localStorage.setItem('toastType', 'success')

          // Reload halaman
          location.reload()
        }
        // console.log(result['response'], result['messages'])
      } else {
        alert(result['messages'] || 'Error occurred.')
      }
    })
    .fail(function (XHR, status, error) {
      alert(status, error)
      console.error('AJAX request failed:', status, error)
    })
}

$('#btn-submit').on('click', function (e) {
  e.preventDefault()

  const btn_action = {
    'Add Product': 'create',
    'Update Product': 'update',
    'Delete Product': 'delete',
  }

  const buttonText = $('#btn-submit').text().trim()
  const action = btn_action[buttonText] || null

  if (action) {
    crudProduct(action, null)
  } else {
    alert('Aksi tidak dikenali: ' + buttonText)
  }
})

document.querySelectorAll('#data-product tbody tr.hoverable').forEach((tr) => {
  tr.addEventListener('click', function () {
    document
      .querySelectorAll('#data-product tbody tr.hoverable')
      .forEach((el) => el.classList.remove('selected'))
    this.classList.add('selected')
    produkId = this.dataset.id || ''
    produkName = this.dataset.name || ''
    uomId = this.dataset.uom || ''
    categoryId = this.dataset.category || ''
  })
})

// document.querySelectorAll('#data-product tbody tr').forEach((tr) => {
//   tr.addEventListener('click', function () {
//     // Abaikan klik jika baris tidak punya data-id & data-product (misal baris kategori)
//     if (!this.dataset.id || !this.dataset.product) return

//     // Hapus class selected dari semua baris
//     document
//       .querySelectorAll('#data-product tbody tr.selected')
//       .forEach((el) => el.classList.remove('selected'))

//     // Tambahkan class selected ke baris yang diklik
//     this.classList.add('selected')

//     // console.log(this.dataset)

//     const productId = this.dataset.id
//     const productName = this.dataset.product
//     const UomId = this.dataset.uom
//     const UomName = this.dataset.uomName
//     const productCategoryId = this.dataset.categoryId
//     const productCategoryName = this.dataset.categoryName

//     // console.log('ID:', productId)
//     // console.log('Name:', productName)
//     // console.log('UOM:', productUOM)
//     // console.log('Category_ID:', productCategoryId)
//     // console.log('Category_Name:', productCategoryName)

//     // Bisa lanjutkan aksi lain, misal fetch detail product, dsb
//   })
// })

document.querySelectorAll('#data-categories tbody tr').forEach((tr) => {
  tr.addEventListener('click', function () {
    document
      .querySelectorAll('#data-categories tbody tr.selected')
      .forEach((el) => el.classList.remove('selected'))
    this.classList.add('selected')

    categoryId = this.dataset.id
    categoryName = this.dataset.category
    categoryUom = this.dataset.uom

    crudProduct('read', categoryId)
    // Hapus duplikasi panggilan crudPostProduct
  })
})

document
  .getElementById('category-select')
  .addEventListener('click', function () {
    crudProduct('create', this.value)
  })

// Event listener untuk select dropdown
const select = document.getElementById('category-select')
if (select && !select.dataset.listenerAdded) {
  select.addEventListener('change', function () {
    const categoryId = this.value
    const categoryName = this.options[this.selectedIndex].text

    // Ganti teks kategori
    const categoryRow = document.querySelector(
      '#data-product thead tr.table-warning th:nth-child(2)'
    )
    if (categoryRow) {
      categoryRow.textContent = 'Category : ' + (categoryName || '')
    }

    // Hapus class 'selected' dari semua baris
    document
      .querySelectorAll('#data-categories tbody tr.selected')
      .forEach((el) => el.classList.remove('selected'))

    // Kirim request ke PHP untuk ambil produk berdasarkan kategori
    fetch(`../Database/koneksi.php?category_id=${categoryId}`)
      .then((response) => response.text())
      .then((html) => {
        document.querySelector('#data-product tbody').innerHTML = html
      })
      .catch((error) => {
        console.error('Error fetching products:', error)
      })
  })
  select.dataset.listenerAdded = 'true'
}

// Fungsi untuk mereset filter
function resetFilters() {
  // Reset select dropdown
  const select = document.getElementById('category-select')
  if (select) {
    select.value = ''
  }

  // Hapus class 'selected' dari semua baris
  document
    .querySelectorAll('#data-categories tbody tr.selected')
    .forEach((el) => el.classList.remove('selected'))

  // Reload page
  location.reload()
}

// Saat input file berubah
$('#product-image').on('change', function () {
  const file = this.files[0]
  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp']
  const maxSize = 2 * 1024 * 1024 // 2 MB

  if (!file) return

  if (!allowedTypes.includes(file.type)) {
    alert('Only JPG, PNG, or WEBP images are allowed.')
    $(this).val('') // clear input
    $('#product-preview').attr('src', '').addClass('d-none')
    return
  }

  if (file.size > maxSize) {
    alert('Image size must be less than 2 MB.')
    $(this).val('')
    $('#product-preview').attr('src', '').addClass('d-none')
    return
  }

  const reader = new FileReader()
  reader.onload = function (e) {
    $('#product-preview').attr('src', e.target.result).removeClass('d-none')
  }
  reader.readAsDataURL(file)
})

// function crudProduct(action, categoryId) {
//   $.ajax({
//     url: base_url + '/public/crud/products.php',
//     type: 'POST',
//     dataType: 'json',
//     data: {
//       action: action,
//       category: categoryId,
//     },
//   })
//     .done(function (result) {
//       // Masukkan hasil ke tbody tabel produk
//       if (result['status'] === 1) {
//         $('#get-product').html('')
//         if (result['data'].length > 0) {
//           $('#get-product').html(generateTableProduct(result['data']))
//         }
//         console.log(result['response'], result['messages'])
//       } else {
//         console.log(result['data'])
//       }
//     })
//     .fail(function () {
//       console.error('AJAX request failed')
//     })
// }

// document
//   .querySelector('.form-product')
//   .addEventListener('submit', function (e) {
//     e.preventDefault() // cegah form submit ke server

//     // Ambil nilai inputan
//     const productId = document.getElementById('product-id').value
//     const productName = document.getElementById('product-name').value
//     const productCategory = document.getElementById('product-category').value
//     const productUom = document.getElementById('product-uom').value

//     // Tampilkan ke console (atau bisa pakai alert)
//     console.log('Product ID:', productId)
//     console.log('Product Name:', productName)
//     console.log('Product Category:', productCategory)
//     console.log('Product UOM:', productUom)

//     // Contoh alert:
//     // alert(`ID: ${productId}\nName: ${productName}\nCategory: ${productCategory}\nUOM: ${productUom}`);
//   })

function generateTableProduct(data) {
  let table = ''
  table += `
    <table class="table table-hover">
      <thead class="table-light">
        <tr class="table-warning">
          <th scope="col">Product</th>
          <th scope="col">UOM</th>
        </tr>
      </thead>
      <tbody>
  `

  data.forEach((item) => {
    table += `
      <tr>
        <td>${item.product_name}</td>
        <td>${item.uom_name}</td>
      </tr>
    `
  })

  table += `</tbody></table>`
  return table
}

function crudPostProduct(action, categoryId, productData = {}) {
  const data = { action: action, category: categoryId, ...productData }
  $.post(base_url + '/public/crud/products.php', data, function (result) {
    // Parse result (assuming JSON response)
    const response = JSON.parse(result)

    if (response.response == 200) {
      // Format message based on array data
      let message = `Fetch Product Successful:`
      if (Array.isArray(response.data) && response.data.length > 0) {
        // List product names or IDs from the array
        const productList = response.data
          .map((item) => item.name || item.product_name || `ID ${item.id}`)
          .join(', ')
        message += `<br/> Product: ${productList}`
      } else if (response.data && typeof response.data === 'object') {
        // Handle single object case
        message += `<br/> Product: ${
          response.data.name ||
          response.data.product_name ||
          `ID ${response.data.id}`
        }`
      } else {
        // Fallback if no data is provided
        message += ` Product: None`
      }
      // Simpan pesan sukses ke localStorage sebelum reload
      localStorage.setItem('toastMessage', result.messages)
      localStorage.setItem('toastType', 'success')

      // Reload halaman
      location.reload()
    } else {
      // Simpan pesan sukses ke localStorage sebelum reload
      localStorage.setItem('toastMessage', result.messages)
      localStorage.setItem('toastType', 'danger')

      // Reload halaman
      location.reload()
    }
  }).fail(function () {
    showToast('Error', 'Server error occurred.', 'danger')
  })
}

// Example toast function (integrating with your previous toast code)
// function showToast(title, message, type = 'success') {
//   const toastContainer =
//     document.getElementById('toast-container') ||
//     (() => {
//       const container = document.createElement('div')
//       container.id = 'toast-container'
//       container.style.position = 'fixed'
//       container.style.top = '20px'
//       container.style.right = '20px'
//       container.style.zIndex = '9999'
//       document.body.appendChild(container)
//       return container
//     })()

//   const toast = document.createElement('div')
//   toast.className = `toast fade align-items-center border-0 shadow-sm text-bg-${type}`
//   toast.setAttribute('role', 'alert')
//   toast.setAttribute('aria-live', 'assertive')
//   toast.setAttribute('aria-atomic', 'true')
//   toast.style.minWidth = '320px'
//   toast.style.marginBottom = '12px'
//   toast.style.borderRadius = '8px'
//   toast.innerHTML = `
//     <div class="toast-header" style="background: transparent; border-bottom: none; color: #fff;">
//       <span class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;background: rgba(255,255,255,0.2);">
//         <i class="bi bi-${
//           type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'
//         }" style="font-size:18px;color:#fff;"></i>
//       </span>
//       <strong class="me-auto" style="font-weight: 600;">${title}</strong>
//       <small style="opacity: 0.8;">Just now</small>
//       <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
//     </div>
//     <div class="toast-body" style="font-size: 1rem; font-weight: 400;">
//       ${message}
//     </div>
//   `

//   const style = document.createElement('style')
//   style.innerHTML = `
//     .toast {
//       transition: all 0.4s ease-in-out, opacity 0.3s ease-in-out;
//       opacity: 0;
//       transform: translateY(20px);
//     }
//     .toast.show {
//       opacity: 1;
//       transform: translateY(0);
//     }
//     .toast:hover {
//       transform: translateY(-2px);
//       box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
//     }
//   `
//   document.head.appendChild(style)

//   toastContainer.appendChild(toast)
//   const bsToast = new bootstrap.Toast(toast, {
//     animation: true,
//     autohide: true,
//     delay: 5000,
//   })
//   bsToast.show()
//   toast.addEventListener('hidden.bs.toast', () => toast.remove())
// }

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
      }
      .toast.show {
        opacity: 1;
        transform: translateY(0);
      }
      .toast:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
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
  toast.innerHTML = `
    <div class="toast-header" style="background: transparent; border-bottom: none; color: #fff;">
      <span class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;background: rgba(255,255,255,0.2);">
        <i class="bi bi-${
          type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'
        }" style="font-size:18px;color:#fff;"></i>
      </span>
      <strong class="me-auto" style="font-weight: 600;">${title}</strong>
      <small style="opacity: 0.8;">Just now</small>
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

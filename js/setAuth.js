$(document).ready(function () {
  $('#form-register').on('submit', function (e) {
    e.preventDefault() // Mencegah reload halaman

    // Ambil semua nilai input
    const username = $('#username').val().trim()
    const email = $('#email').val().trim()
    const password = $('#password').val().trim()
    const passwordRewrite = $('#password-rewrite').val().trim()
    const tanggal_lahir = $('#tanggal_lahir').val().trim()
    const jenis_kelamin = $('input[name="jenis_kelamin"]:checked').val()
    const alamat = $('#alamat').val().trim()
    const kebutuhan_khusus = $('#kebutuhan_khusus').val()

    // Validasi input
    if (username === '') {
      alert('Username tidak boleh kosong.')
      $('#username').focus()
      return
    }

    if (email === '') {
      alert('Email tidak boleh kosong.')
      $('#email').focus()
      return
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      alert('Format email tidak valid.')
      $('#email').focus()
      return
    }

    if (password === '') {
      alert('Password tidak boleh kosong.')
      $('#password').focus()
      return
    } else if (password !== passwordRewrite) {
      alert('Password tidak sesuai.')
      $('#password-rewrite').focus()
      return
    } else if (password.length < 6) {
      alert('Password minimal 6 karakter.')
      $('#password').focus()
      return
    }

    if (tanggal_lahir === '') {
      alert('Tanggal lahir wajib diisi.')
      $('#tanggal_lahir').focus()
      return
    }

    if (!jenis_kelamin) {
      alert('Pilih jenis kelamin.')
      return
    }

    if (alamat === '') {
      alert('Alamat tidak boleh kosong.')
      $('#alamat').focus()
      return
    }

    if (!kebutuhan_khusus) {
      alert('Pilih kebutuhan khusus.')
      $('#kebutuhan_khusus').focus()
      return
    }

    // Jika semua validasi lolos, kirim data ke server
    const formData = {
      username,
      email,
      password,
      tanggal_lahir,
      jenis_kelamin,
      alamat,
      kebutuhan_khusus,
    }

    const serializedData = $.param(formData)

    $.ajax({
      url: base_url + '/public/crud/users.php',
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'create',
        data: serializedData,
      },
      success: function (response) {
        console.log('Response:', response) // <- Tambahkan ini
        if (response.response === 200) {
          alert('Registrasi berhasil!')
          $('form')[0].reset()
          window.location.href = 'login.php'
        } else {
          alert('Registrasi gagal: ' + response.messages)
        }
      },
      error: function (xhr, status, error) {
        console.error(error)
        console.log(xhr.responseText)
        alert('Terjadi kesalahan saat mengirim data.')
      },
    })
  })

  $('#form-login').on('submit', function (e) {
    e.preventDefault() // Mencegah reload halaman

    // Ambil semua nilai input
    const email = $('#login_email').val().trim()
    const password = $('#login_password').val().trim()

    // Validasi input
    if (email === '') {
      alert('Email tidak boleh kosong.')
      $('#login_email').focus()
      return
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      alert('Format email tidak valid.')
      $('#login_email').focus()
      return
    }

    if (password === '') {
      alert('Password tidak boleh kosong.')
      $('#login_password').focus()
      return
    }

    // Jika semua validasi lolos, kirim data ke server
    const formData = {
      email,
      password,
    }

    const serializedData = $.param(formData)

    $.ajax({
      url: base_url + '/public/crud/users.php',
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'login',
        data: serializedData,
      },
      success: function (response) {
        console.log('Response:', response) // <- Tambahkan ini
        if (response.response === 200) {
          // alert('Login berhasil!')
          $('form')[0].reset()
          window.location.href = '../index.php'
        } else {
          alert('Login gagal: ' + response.messages)
          console.log('Login gagal: ' + response.messages) // <- Tambahkan ini
        }
      },
      error: function (xhr, status, error) {
        console.error(error)
        console.log(xhr.responseText)
        alert('Terjadi kesalahan saat mengirim data.')
      },
    })
  })
})

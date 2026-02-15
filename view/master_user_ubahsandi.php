<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
  session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

if (!isset($_SESSION['sess_usr_id'])) {
  echo "<div class='alert alert-danger'>Session tidak valid. Silakan login kembali.</div>";
  exit;
}
?>

<div class="container-fluid px-4 mt-4">

  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="fa fa-key me-2"></i>Ubah Kata Sandi</h5>
    </div>

    <div class="card-body">
      <form id="ubahPassForm">
        <input type="hidden" name="usr_id" value="<?php echo $_SESSION['sess_usr_id']; ?>">

        <!-- Password Lama -->
        <div class="mb-3">
          <label class="form-label">Password Lama</label>
          <input type="text" name="old_pass" id="old_pass" class="form-control" required>
        </div>

        <!-- Password Baru -->
        <div class="mb-3">
          <label class="form-label">Password Baru</label>
          <input type="text" name="new_pass" id="new_pass" class="form-control" placeholder="Masukkan password baru..." required minlength="6">
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="mb-3">
          <label class="form-label">Konfirmasi Password Baru</label>
          <input type="text" name="confirm_pass" id="confirm_pass" class="form-control" required minlength="6">
        </div>

        <div class="text-end">
          <button type="button" id="btnSavePass" class="btn btn-success">
            <i class="fa fa-save"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script>
  $(function () {
    // Show / Hide Password
    $(document).on('click', '.toggle-pass', function () {
      const target = $($(this).data('target'));

      if (target.attr('type') === 'password') {
        target.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        target.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
    });

    // Submit ubah password
    $('#btnSavePass').on('click', function () {
      const oldPass = $('#old_pass').val().trim();
      const newPass = $('#new_pass').val().trim();
      const confirmPass = $('#confirm_pass').val().trim();

      if (newPass.length < 6) {
        alert('Password baru minimal 6 karakter!');
        return;
      }
      if (newPass !== confirmPass) {
        alert('Konfirmasi password tidak cocok!');
        return;
      }

      $.ajax({
        url: 'controller/user/pro_ubah_sandi.php',
        method: 'POST',
        data: $('#ubahPassForm').serialize(),
        success: function (res) {
          if (res.trim() === 'OK') {
            alert('Password berhasil diperbarui!');
            $('#ubahPassForm')[0].reset();
          } else {
            alert('Gagal: ' + res);
          }
        },
        error: function (xhr, status, err) {
          alert('Terjadi kesalahan: ' + err);
        }
      });
    });
  });
</script>
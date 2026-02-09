<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

if (!isset($_SESSION['sess_usr_id'])) {
  echo "<div class='alert alert-danger'>Session tidak valid. Silakan login kembali.</div>";
  exit;
}

// ambil data user dari db
$usr_id = (int)$_SESSION['sess_usr_id'];
$stmt = $conn->prepare("SELECT usr_username, usr_nama, usr_email, usr_alamat, usr_tlp FROM tbl_user WHERE usr_id = ?");
$stmt->bind_param("i", $usr_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="container-fluid px-4 mt-4">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="fa fa-user-edit me-2"></i>Ubah Data Pribadi</h5>
    </div>
    <div class="card-body">
      <form id="usr_ubah_form">
        <input type="hidden" name="usr_ubah_id" id="usr_ubah_id" value="<?php echo $usr_id; ?>">

        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="usr_ubah_username" id="usr_ubah_username" class="form-control"
                 value="<?php echo htmlspecialchars($user['usr_username'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="usr_ubah_nama" id="usr_ubah_nama" class="form-control"
                 value="<?php echo htmlspecialchars($user['usr_nama'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="usr_ubah_email" id="usr_ubah_email" class="form-control"
                 value="<?php echo htmlspecialchars($user['usr_email'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Alamat</label>
          <textarea name="usr_ubah_alamat" id="usr_ubah_alamat" class="form-control" rows="3"
                    placeholder="Alamat lengkap..."><?php echo htmlspecialchars($user['usr_alamat'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="usr_ubah_tlp" id="usr_ubah_tlp" class="form-control"
                 value="<?php echo htmlspecialchars($user['usr_tlp'] ?? ''); ?>" maxlength="20">
        </div>

        <div class="text-end">
          <button type="button" id="usr_ubah_simpan" class="btn btn-success">
            <i class="fa fa-save"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script>
$(function() {
  $('#usr_ubah_simpan').on('click', function() {
    const formData = $('#usr_ubah_form').serialize();

    $.ajax({
      url: 'controller/user/pro_ubah_data.php',
      method: 'POST',
      data: formData,
      success: function(res) {
        if (res.trim() === 'OK') {
          alert('Data berhasil diperbarui!');
          location.reload();
        } else {
          alert('Gagal: ' + res);
        }
      },
      error: function(xhr, status, err) {
        alert('Terjadi kesalahan: ' + err);
      }
    });
  });
});
</script>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
  session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

// Hanya Admin yang bisa masuk ke manajemen user
if ($_SESSION['sess_usr_status'] !== 'Admin') {
  echo "<div class='alert alert-danger text-center mt-4'>
          Akses ditolak! Halaman ini hanya untuk Admin.
        </div>";
  exit;
}
?>

<div class="container-fluid px-4 mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">👥 Manajemen User</h4>
    <button class="btn btn-primary" id="btnAddUser">
      <i class="fa fa-user-plus"></i> Tambah User
    </button>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Username</th>
              <th>Nama Lengkap</th>
              <th>Email</th>
              <th>No. HP</th>
              <th>Vendor</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $q = $conn->query("SELECT u.*, v.vendor_name 
                               FROM tbl_user u 
                               LEFT JOIN tbl_vendor v ON u.vendor_id = v.vendor_id 
                               ORDER BY u.usr_id DESC");

            if ($q && $q->num_rows > 0) {
              while ($row = $q->fetch_assoc()) {
                echo "<tr>
                        <td class='text-center'>$no</td>
                        <td>{$row['usr_username']}</td>
                        <td>{$row['usr_nama']}</td>
                        <td>{$row['usr_email']}</td>
                        <td>{$row['usr_tlp']}</td>
                        <td>" . ($row['vendor_name'] ?? '-') . "</td>
                        <td class='text-center'>
                          <span style='color:white; padding:5px;' class='badge bg-" . (
                  $row['usr_status'] == 'Admin' ? 'primary' :
                  ($row['usr_status'] == 'BBTeknik' ? 'info' : 'success')
                ) . "'>{$row['usr_status']}</span>
                        </td>
                        <td class='text-center'>
                          <button class='btn btn-sm btn-warning me-1 btnEditUser' data-id='{$row['usr_id']}'>
                            <i class='fa fa-edit'></i> Ubah
                          </button>
                          <button class='btn btn-sm btn-danger me-1 btnDeleteUser' data-id='{$row['usr_id']}'>
                            <i class='fa fa-trash'></i> Hapus
                          </button>
                          <button class='btn btn-sm btn-info btnResetPass' data-id='{$row['usr_id']}'>
                            <i class='fa fa-key'></i> Reset Password
                          </button>
                        </td>
                      </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='8' class='text-center text-muted'>Belum ada user terdaftar</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="userModalLabel"><i class="fa fa-user-plus me-2"></i><span id="modalTitle">Tambah User</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="userForm" method="POST" action="controller/user/pro_master_user.php">
        <div class="modal-body">
          <input type="hidden" name="usr_id" id="usr_id">
          <input type="hidden" name="action" id="form_action" value="create">

          <!-- Username -->
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="usr_username" id="username" class="form-control" required placeholder="Tanpa spasi">
          </div>

          <!-- Nama -->
          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="usr_nama" id="nama" class="form-control" required>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="usr_email" id="email" class="form-control" placeholder="Masukkan email (opsional)">
          </div>

          <!-- No. HP -->
          <div class="mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" name="usr_tlp" id="nohp" class="form-control" placeholder="Masukkan nomor HP">
          </div>

          <!-- Password -->
          <div class="mb-3 position-relative" id="passwordField">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="password" name="usr_pass" id="password" class="form-control" required>
              <div class="input-group-append">
                <span class="input-group-text bg-white border-start-0" id="togglePassword" style="cursor:pointer;">
                  <i class="fa fa-eye-slash" id="eyeIcon"></i>
                </span>
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="mb-3">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="usr_status" id="status" class="form-control" required>
              <option value="" disabled selected>Pilih Status</option>
              <option value="Admin">Admin</option>
              <option value="BBTeknik">BB Teknik</option>
              <option value="Customer">Customer</option>
            </select>
          </div>

          <!-- Vendor -->
          <div class="mb-3">
            <label class="form-label">Vendor</label>
            <select name="vendor_id" id="vendor_id" class="form-control">
              <option value="">-- Pilih Vendor --</option>
              <?php
              $v = $conn->query("SELECT * FROM tbl_vendor ORDER BY vendor_name ASC");
              while ($ven = $v->fetch_assoc()) {
                echo "<option value='{$ven['vendor_id']}'>{$ven['vendor_name']}</option>";
              }
              ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="save_user" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL RESET PASSWORD -->
<div class="modal fade" id="resetPassModal" tabindex="-1" aria-labelledby="resetPassLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="fa fa-key me-2"></i>Reset Password User</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="resetPassForm">
        <div class="modal-body">
          <input type="hidden" name="usr_id" id="reset_usr_id">

          <div class="form-group mb-3">
            <label>Password Baru</label>
            <input type="text" name="new_pass" id="new_pass" class="form-control" required minlength="6" placeholder="Masukkan password baru">
          </div>

          <div class="form-group mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="text" name="confirm_pass" id="confirm_pass" class="form-control" required minlength="6" placeholder="Ulangi password baru">
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  setTimeout(function () {
    const userModal = $('#userModal');
    const resetModal = $('#resetPassModal');

    // Fokus ke input password baru saat modal reset tampil
    resetModal.on('shown.bs.modal', function () {
      $('#new_pass').trigger('focus');
    });

    // Tambah User
    $(document).off('click', '#btnAddUser').on('click', '#btnAddUser', function () {
      $('#userForm')[0].reset();
      $('#usr_id').val('');
      $('#form_action').val('create');
      $('#modalTitle').text('Tambah User');
      $('#passwordField').show();
      $('#password').attr('required', true);
      userModal.modal('show');
    });

    // Edit User
    $(document).off('click', '.btnEditUser').on('click', '.btnEditUser', function () {
      const id = $(this).data('id');
      fetch(`controller/user/pro_master_user.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (!data || !data.usr_id) {
            alert('Data user tidak ditemukan');
            return;
          }

          $('#usr_id').val(data.usr_id);
          $('#username').val(data.usr_username);
          $('#nama').val(data.usr_nama);
          $('#email').val(data.usr_email);
          $('#nohp').val(data.usr_tlp);
          $('#status').val(data.usr_status);
          $('#vendor_id').val(data.vendor_id || '');
          $('#form_action').val('edit');
          $('#modalTitle').text('Edit User');
          $('#passwordField').hide();
          $('#password').removeAttr('required');

          userModal.modal('show');
        })
        .catch(() => alert('Gagal mengambil data user.'));
    });

    // Simpan Tambah/Edit
    $(document).off('submit', '#userForm').on('submit', '#userForm', function (e) {
      e.preventDefault();
      const formData = $(this).serialize();

      $.ajax({
        url: 'controller/user/pro_master_user.php',
        method: 'POST',
        data: formData,
        success: function (res) {
          if (res.trim() === 'OK') {
            alert('Data user berhasil disimpan!');
            userModal.modal('hide');
            location.reload();
          } else {
            alert('Gagal menyimpan: ' + res);
          }
        },
        error: function (xhr, status, err) {
          alert('Terjadi kesalahan: ' + err);
        }
      });
    });

    // Delete User
    $(document).off('click', '.btnDeleteUser').on('click', '.btnDeleteUser', function () {
      const id = $(this).data('id');
      if (confirm('Yakin ingin menghapus user ini?')) {
        window.location.href = `controller/user/pro_master_user.php?action=delete&id=${id}`;
      }
    });

    // Reset Password
    $(document).off('click', '.btnResetPass').on('click', '.btnResetPass', function () {
      const id = $(this).data('id');
      $('#reset_usr_id').val(id);
      $('#new_pass, #confirm_pass').val('');
      resetModal.modal('show');
    });

    // Simpan Reset Password
    $(document).off('submit', '#resetPassForm').on('submit', '#resetPassForm', function (e) {
      e.preventDefault();
      const newPass = $('#new_pass').val();
      const confirmPass = $('#confirm_pass').val();

      if (newPass.length < 6) return alert('Password minimal 6 karakter!');
      if (newPass !== confirmPass) return alert('Konfirmasi password tidak cocok!');

      $.ajax({
        url: 'controller/user/pro_master_user.php',
        method: 'POST',
        data: {
          action: 'reset_password',
          usr_id: $('#reset_usr_id').val(),
          new_pass: newPass
        },
        success: function (res) {
          if (res.trim() === 'OK') {
            alert('Password berhasil direset!');
            resetModal.modal('hide');
          } else {
            alert('Gagal reset password: ' + res);
          }
        }
      });
    });

    // Username tanpa spasi
    $('#username').on('keypress', function (e) {
      if (e.key === ' ') e.preventDefault();
    }).on('input', function () {
      this.value = this.value.replace(/\s+/g, '');
    });

    // 👁️ Toggle Password
    $('#togglePassword').on('click', function () {
      const input = $('#password');
      const icon = $('#eyeIcon');
      const type = input.attr('type') === 'password' ? 'text' : 'password';
      input.attr('type', type);
      icon.toggleClass('fa-eye fa-eye-slash');
    });

  }, 300);
</script>
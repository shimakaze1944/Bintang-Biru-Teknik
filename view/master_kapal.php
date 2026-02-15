<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

// hanya admin & bbteknik
if (!in_array($_SESSION['sess_usr_status'], ['Admin', 'BBTeknik'])) {
  echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk Admin & BB Teknik.</div>";
  exit;
}
?>

<div class="container-fluid px-4 mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">🚢 Manajemen Kapal</h4>
    <button class="btn btn-primary" id="btnAddKapal">
      <i class="fa fa-plus"></i> Tambah Kapal
    </button>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama Kapal</th>
              <th>Vendor</th>
              <th>Keterangan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $q = $conn->query("SELECT k.*, v.vendor_name 
                               FROM tbl_kapal k
                               LEFT JOIN tbl_vendor v ON k.vendor_id = v.vendor_id
                               ORDER BY k.kapal_id DESC");

            if ($q && $q->num_rows > 0) {
              while ($row = $q->fetch_assoc()) {
                echo "<tr>
                        <td class='text-center'>$no</td>
                        <td>{$row['nama_kapal']}</td>
                        <td>" . ($row['vendor_name'] ?: '-') . "</td>
                        <td>" . ($row['keterangan'] ?: '-') . "</td>
                        <td class='text-center'>
                          <button class='btn btn-sm btn-warning me-1 btnEditKapal' data-id='{$row['kapal_id']}'>
                            <i class='fa fa-edit'></i> Ubah
                          </button>
                          <button class='btn btn-sm btn-danger btnDeleteKapal' data-id='{$row['kapal_id']}'>
                            <i class='fa fa-trash'></i> Hapus
                          </button>
                        </td>
                      </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada data kapal</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH / EDIT KAPAL -->
<div class="modal fade" id="kapalModal" tabindex="-1" role="dialog" aria-labelledby="kapalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-ship me-2"></i><span id="modalTitle">Tambah Kapal</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="kapalForm" method="POST" action="controller/master/kapal_controller.php">
        <div class="modal-body">
          <input type="hidden" name="kapal_id" id="kapal_id">
          <input type="hidden" name="action" id="form_action" value="create">

          <div class="form-group">
            <label>Nama Kapal</label>
            <input type="text" name="nama_kapal" id="nama_kapal" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Vendor</label>
            <select name="vendor_id" id="vendor_id" class="form-control" required>
              <option value="">-- Pilih Vendor --</option>
              <?php
              $vendors = $conn->query("SELECT vendor_id, vendor_name FROM tbl_vendor ORDER BY vendor_name ASC");
              while ($v = $vendors->fetch_assoc()) {
                echo "<option value='{$v['vendor_id']}'>{$v['vendor_name']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
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
(function waitForJQuery() {
  if (typeof $ === 'undefined') return setTimeout(waitForJQuery, 200);

  $(document).ready(function () {
    const kapalModal = $('#kapalModal');

    // Tambah Kapal
    $(document).on('click', '#btnAddKapal', function () {
      $('#kapalForm')[0].reset();
      $('#kapal_id').val('');
      $('#form_action').val('create');
      $('#modalTitle').text('Tambah Kapal');
      kapalModal.modal('show');
    });

    // Edit Kapal
    $(document).on('click', '.btnEditKapal', function () {
      const id = $(this).data('id');
      fetch(`controller/master/kapal_controller.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (!data || !data.kapal_id) {
            alert('Data kapal tidak ditemukan');
            return;
          }
          $('#kapal_id').val(data.kapal_id);
          $('#nama_kapal').val(data.nama_kapal);
          $('#vendor_id').val(data.vendor_id);
          $('#keterangan').val(data.keterangan || '');
          $('#form_action').val('edit');
          $('#modalTitle').text('Edit Kapal');
          kapalModal.modal('show');
        })
        .catch(err => {
          alert('Gagal mengambil data kapal.');
          console.error(err);
        });
    });

    // Simpan
    $(document).on('submit', '#kapalForm', function (e) {
      e.preventDefault();
      $.ajax({
        url: 'controller/master/kapal_controller.php',
        method: 'POST',
        data: $(this).serialize(),
        success: function (res) {
          if (res.trim() === 'OK') {
            alert('Data kapal berhasil disimpan!');
            kapalModal.modal('hide');
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

    // Delete Kapal
    $(document).on('click', '.btnDeleteKapal', function () {
      const id = $(this).data('id');
      if (confirm('Yakin ingin menghapus kapal ini?')) {
        window.location.href = `controller/master/kapal_controller.php?action=delete&id=${id}`;
      }
    });
  });
})();
</script>

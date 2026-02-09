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
    <h4 class="mb-0">🛠️ Manajemen Layanan</h4>
    <button class="btn btn-primary" id="btnAddLayanan">
      <i class="fa fa-plus"></i> Tambah Layanan
    </button>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama Layanan</th>
              <th>Harga (Rp)</th>
              <th>Keterangan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $q = $conn->query("SELECT * FROM tbl_layanan ORDER BY layanan_id DESC");

            if ($q && $q->num_rows > 0) {
              while ($row = $q->fetch_assoc()) {
                echo "<tr>
                        <td class='text-center'>$no</td>
                        <td>{$row['layanan_nama']}</td>
                        <td class='text-end'>Rp " . number_format($row['layanan_harga'], 0, ',', '.') . "</td>
                        <td>{$row['layanan_keterangan']}</td>
                        <td class='text-center'>
                          <button class='btn btn-sm btn-warning me-1 btnEditLayanan' data-id='{$row['layanan_id']}'>
                            <i class='fa fa-edit'></i> Ubah
                          </button>
                          <button class='btn btn-sm btn-danger btnDeleteLayanan' data-id='{$row['layanan_id']}'>
                            <i class='fa fa-trash'></i> Hapus
                          </button>
                        </td>
                      </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='4' class='text-center text-muted'>Belum ada layanan terdaftar</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH / EDIT LAYANAN -->
<div class="modal fade" id="layananModal" tabindex="-1" role="dialog" aria-labelledby="layananModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-wrench me-2"></i><span id="modalTitle">Tambah Layanan</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="layananForm" method="POST" action="controller/master/layanan_controller.php">
        <div class="modal-body">
          <input type="hidden" name="layanan_id" id="layanan_id">
          <input type="hidden" name="action" id="form_action" value="create">

          <div class="form-group">
            <label>Nama Layanan</label>
            <input type="text" name="layanan_nama" id="layanan_nama" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Harga (Rp)</label>
            <input type="number" name="layanan_harga" id="layanan_harga" class="form-control" value="0" required>
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="layanan_keterangan" id="layanan_keterangan" class="form-control" required>
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

<!-- Pastikan jQuery tersedia dulu -->
<script src="vendor/jquery/jquery.min.js"></script>

<script>
$(function () {
  console.log(' Script Master Layanan ready');

  const layananModal = $('#layananModal');

  // Tambah Layanan
  $(document).on('click', '#btnAddLayanan', function () {
    console.log(' Tombol tambah layanan diklik');
    $('#layananForm')[0].reset();
    $('#layanan_id').val('');
    $('#form_action').val('create');
    $('#modalTitle').text('Tambah Layanan');
    layananModal.modal('show');
  });

  // Edit Layanan
  $(document).on('click', '.btnEditLayanan', function () {
    const id = $(this).data('id');
    console.log(' Edit diklik untuk ID:', id);

    fetch(`controller/master/layanan_controller.php?action=get&id=${id}`)
      .then(res => res.json())
      .then(data => {
        if (!data || !data.layanan_id) {
          alert('Data layanan tidak ditemukan');
          return;
        }

        $('#layanan_id').val(data.layanan_id);
        $('#layanan_nama').val(data.layanan_nama);
        $('#layanan_harga').val(data.layanan_harga);
        $('#layanan_keterangan').val(data.layanan_keterangan);
        $('#form_action').val('edit');
        $('#modalTitle').text('Edit Layanan');

        layananModal.modal('show');
      })
      .catch(err => {
        console.error('Gagal ambil data layanan:', err);
        alert('Gagal mengambil data layanan.');
      });
  });

  // Simpan
  $(document).on('submit', '#layananForm', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    console.log(' Simpan diklik, action:', $('#form_action').val());

    $.ajax({
      url: 'controller/master/layanan_controller.php',
      method: 'POST',
      data: formData,
      success: function (res) {
        console.log('Respon:', res);
        if (res.trim() === 'OK') {
          alert('Data layanan berhasil disimpan!');
          layananModal.modal('hide');
          location.reload();
        } else {
          alert('Gagal menyimpan: ' + res);
        }
      },
      error: function (xhr, status, err) {
        alert('Terjadi kesalahan: ' + err);
        console.error(err);
      }
    });
  });

  // Delete Layanan
  $(document).on('click', '.btnDeleteLayanan', function () {
    const id = $(this).data('id');
    console.log(' Hapus diklik untuk ID:', id);

    if (confirm('Yakin ingin menghapus layanan ini?')) {
      window.location.href = `controller/master/layanan_controller.php?action=delete&id=${id}`;
    }
  });
});
</script>

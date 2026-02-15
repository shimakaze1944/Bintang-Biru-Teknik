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
    <h4 class="mb-0">👷‍♂️ Manajemen Pekerja / Teknisi</h4>
    <button class="btn btn-primary" id="btnAddPekerja">
      <i class="fa fa-plus"></i> Tambah Pekerja
    </button>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-light text-center">
            <tr>
              <th>No</th>
              <th>Nama Pekerja</th>
              <th>Kontak</th>
              <th>Alamat</th>
              <th>Keterangan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $q = $conn->query("SELECT * FROM tbl_pekerja ORDER BY pekerja_id DESC");

            if ($q && $q->num_rows > 0) {
              while ($row = $q->fetch_assoc()) {
                echo "<tr>
                        <td class='text-center'>$no</td>
                        <td>{$row['pekerja_nama']}</td>
                        <td>{$row['pekerja_kontak']}</td>
                        <td>{$row['pekerja_alamat']}</td>
                        <td>{$row['pekerja_keterangan']}</td>
                        <td class='text-center'>
                          <button class='btn btn-sm btn-warning me-1 btnEditPekerja' data-id='{$row['pekerja_id']}'>
                            <i class='fa fa-edit'></i> Ubah
                          </button>
                          <button class='btn btn-sm btn-danger btnDeletePekerja' data-id='{$row['pekerja_id']}'>
                            <i class='fa fa-trash'></i> Hapus
                          </button>
                        </td>
                      </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada pekerja terdaftar</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH / EDIT PEKERJA -->
<div class="modal fade" id="pekerjaModal" tabindex="-1" role="dialog" aria-labelledby="pekerjaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-user-cog me-2"></i><span id="modalTitle">Tambah Pekerja</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="pekerjaForm" method="POST" action="controller/master/pekerja_controller.php">
        <div class="modal-body">
          <input type="hidden" name="pekerja_id" id="pekerja_id">
          <input type="hidden" name="action" id="form_action" value="create">

          <div class="form-group">
            <label>Nama Pekerja</label>
            <input type="text" name="pekerja_nama" id="pekerja_nama" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Kontak</label>
            <input type="text" name="pekerja_kontak" id="pekerja_kontak" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Alamat</label>
            <textarea name="pekerja_alamat" id="pekerja_alamat" class="form-control" rows="2" required></textarea>
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="pekerja_keterangan" id="pekerja_keterangan" class="form-control" rows="2" required></textarea>
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
s
<script src="vendor/jquery/jquery.min.js"></script>

<script>
$(function () {
  console.log(' Script Master Pekerja siap');

  const pekerjaModal = $('#pekerjaModal');

  // Tambah
  $(document).on('click', '#btnAddPekerja', function () {
    console.log('Tambah pekerja diklik');
    $('#pekerjaForm')[0].reset();
    $('#pekerja_id').val('');
    $('#form_action').val('create');
    $('#modalTitle').text('Tambah Pekerja');
    pekerjaModal.modal('show');
  });

  // Edit
  $(document).on('click', '.btnEditPekerja', function () {
    const id = $(this).data('id');
    console.log('Edit pekerja id:', id);

    fetch(`controller/master/pekerja_controller.php?action=get&id=${id}`)
      .then(res => res.json())
      .then(data => {
        if (!data || !data.pekerja_id) {
          alert('Data pekerja tidak ditemukan');
          return;
        }

        $('#pekerja_id').val(data.pekerja_id);
        $('#pekerja_nama').val(data.pekerja_nama);
        $('#pekerja_kontak').val(data.pekerja_kontak);
        $('#pekerja_alamat').val(data.pekerja_alamat);
        $('#pekerja_keterangan').val(data.pekerja_keterangan);
        $('#form_action').val('edit');
        $('#modalTitle').text('Edit Pekerja');
        pekerjaModal.modal('show');
      })
      .catch(err => {
        console.error('Gagal ambil data pekerja:', err);
        alert('Gagal mengambil data pekerja.');
      });
  });

  // Simpan
  $(document).on('submit', '#pekerjaForm', function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
      url: 'controller/master/pekerja_controller.php',
      method: 'POST',
      data: formData,
      success: function (res) {
        if (res.trim() === 'OK') {
          alert('Data pekerja berhasil disimpan!');
          pekerjaModal.modal('hide');
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

  // Hapus
  $(document).on('click', '.btnDeletePekerja', function () {
    const id = $(this).data('id');
    if (confirm('Yakin ingin menghapus pekerja ini?')) {
      window.location.href = `controller/master/pekerja_controller.php?action=delete&id=${id}`;
    }
  });
});
</script>

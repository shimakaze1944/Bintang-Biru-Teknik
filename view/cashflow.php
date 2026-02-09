<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
  session_start();

include_once(__DIR__ . '/../controller/auth/db_connection.php');

// Akses hanya Admin / BB Teknik
if (!in_array($_SESSION['sess_usr_status'], ['Admin', 'BBTeknik'])) {
  echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk Admin & BB Teknik.</div>";
  exit;
}

// Ambil filter bulan
$bulan = $_GET['bulan'] ?? date('Y-m');

// Query data berdasarkan bulan
$q = $conn->query("
  SELECT * FROM tbl_cashflow
  WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'
  ORDER BY tanggal DESC
");

$total_kredit = 0;
$total_debit = 0;
$rows = [];

if ($q) {
  while ($row = $q->fetch_assoc()) {
    if ($row['tipe'] === 'Kredit') $total_kredit += $row['total'];
    else $total_debit += $row['total'];
    $rows[] = $row;
  }
}

$saldo = $total_kredit - $total_debit;
?>

<div class="container-fluid px-4 mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">💵 Manajemen Cashflow</h4>
    <button class="btn btn-primary" id="btnAdd">
      <i class="fa fa-plus"></i> Tambah Transaksi
    </button>
  </div>

  <form method="get" action="">
    <input type="hidden" name="cs" value="Cashflow">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
      <label class="me-2">Pilih Bulan:</label>
      <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>" class="form-control" style="width:180px;">
      <button type="submit" class="btn btn-secondary ms-2">Tampilkan</button>
    </div>
  </form>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Tipe</th>
              <th>Total</th>
              <th>Keterangan</th>
              <th>Dibuat Oleh</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['tanggal']) ?></td>
                  <td>
                    <?php if ($r['tipe'] === 'Kredit'): ?>
                      <span class="badge">Kredit</span>
                    <?php else: ?>
                      <span class="badge">Debit</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end"><?= number_format($r['total'], 0, ',', '.') ?></td>
                  <td class="text-start"><?= nl2br(htmlspecialchars($r['keterangan'])) ?></td>
                  <td><?= htmlspecialchars($r['created_by']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-warning btnEdit" data-id="<?= $r['id'] ?>">
                      <i class="fa fa-edit"></i> Ubah
                    </button>
                    <button class="btn btn-sm btn-danger btnDelete" data-id="<?= $r['id'] ?>">
                      <i class="fa fa-trash"></i> Hapus
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-muted">Belum ada transaksi bulan ini.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        <p><strong>Total Kredit:</strong> Rp <?= number_format($total_kredit, 0, ',', '.') ?></p>
        <p><strong>Total Debit:</strong> Rp <?= number_format($total_debit, 0, ',', '.') ?></p>
        <h5><strong>Saldo Bulan Ini:</strong> Rp <?= number_format($saldo, 0, ',', '.') ?></h5>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH / EDIT -->
<div class="modal fade" id="modalCashflow" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-plus-circle me-2"></i><span id="modalTitle">Tambah Transaksi</span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>

      <form id="formCashflow">
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <input type="hidden" name="action" id="action" value="create">

          <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Tipe Transaksi</label><br>
            <label><input type="radio" name="tipe" value="Kredit" required> Kredit (Masuk)</label>
            <label class="ms-3"><input type="radio" name="tipe" value="Debit"> Debit (Keluar)</label>
          </div>

          <div class="mb-3">
            <label>Total (Rp)</label>
            <input type="number" name="total" id="total" class="form-control" step="0.01" required>
          </div>

          <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .table td {
    vertical-align: middle !important;
  }
  .table td.text-start {
    text-align: left !important;
  }
  .badge {
    font-size: 0.9rem;
  }
</style>

<script>
document.addEventListener("DOMContentLoaded", function(){
  if (typeof window.jQuery === "undefined") {
    console.warn("jQuery belum termuat, memuat ulang dari CDN...");
    const script = document.createElement("script");
    script.src = "https://code.jquery.com/jquery-3.7.1.min.js";
    script.onload = initCashflow;
    document.body.appendChild(script);
  } else {
    initCashflow();
  }

  function initCashflow(){
    console.log("jQuery aktif, Cashflow initialized");

    const modal = $('#modalCashflow');

    // tambah
    $('#btnAdd').on('click', function(){
      console.log("Tombol Tambah diklik");
      $('#formCashflow')[0].reset();
      $('#action').val('create');
      $('#modalTitle').text('Tambah Transaksi');
      modal.modal('show');
    });

    // edit
    $(document).on('click', '.btnEdit', function(){
      const id = $(this).data('id');
      fetch(`controller/master/cashflow_controller.php?action=get&id=${id}`)
        .then(r=>r.json())
        .then(d=>{
          $('#id').val(d.id);
          $('#tanggal').val(d.tanggal);
          $(`input[name="tipe"][value="${d.tipe}"]`).prop('checked', true);
          $('#total').val(d.total);
          $('#keterangan').val(d.keterangan);
          $('#action').val('edit');
          $('#modalTitle').text('Edit Transaksi');
          modal.modal('show');
        });
    });

    // simpan
    $('#formCashflow').on('submit', function(e){
      e.preventDefault();
      $.post('controller/master/cashflow_controller.php', $(this).serialize(), function(res){
        if(res.trim()==='OK'){
          alert('Data berhasil disimpan!');
          modal.modal('hide');
          location.reload();
        } else {
          alert('Gagal menyimpan: ' + res);
        }
      });
    });

    // hapus
    $(document).on('click', '.btnDelete', function(){
      const id = $(this).data('id');
      if(confirm('Yakin ingin menghapus transaksi ini?')){
        $.get(`controller/master/cashflow_controller.php?action=delete&id=${id}`, function(res){
          if(res.trim()==='OK'){
            alert('Data berhasil dihapus!');
            location.reload();
          } else {
            alert('Gagal menghapus: '+res);
          }
        });
      }
    });
  }
});
</script>


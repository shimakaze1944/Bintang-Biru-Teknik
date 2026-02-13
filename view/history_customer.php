<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!in_array($_SESSION['sess_usr_status'], ['Customer'])) {
  echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk Customer.</div>";
  exit;
}

// pastikan user punya vendor id
if (!isset($_SESSION['sess_usr_vendor']) || empty($_SESSION['sess_usr_vendor'])) {
  echo "<div class='alert alert-warning'>Akun ini belum terhubung ke data vendor. Hubungi admin.</div>";
  exit;
}

$vendor_id = $_SESSION['sess_usr_vendor'];

// ambil filter tanggal
$tgl_dari = $_GET['tgl_dari'] ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';

$where = "WHERE s.vendor_id = $vendor_id";
if ($tgl_dari && $tgl_sampai) {
  $where .= " AND (s.tgl_masuk BETWEEN '$tgl_dari' AND '$tgl_sampai')";
}

$q = $conn->query("
  SELECT 
    s.svs_id,
    s.no_wo,
    s.nama_kapal,
    s.tgl_masuk,
    s.tgl_keluar,
    s.keterangan,
    s.status,
    GROUP_CONCAT(DISTINCT p.pekerja_nama SEPARATOR ', ') AS teknisi,
    GROUP_CONCAT(DISTINCT l.layanan_nama SEPARATOR ', ') AS layanan
  FROM tbl_servis s
  LEFT JOIN tbl_servis_pekerja sp ON s.svs_id = sp.svs_id
  LEFT JOIN tbl_pekerja p ON sp.pekerja_id = p.pekerja_id
  LEFT JOIN tbl_servis_layanan sl ON s.svs_id = sl.svs_id
  LEFT JOIN tbl_layanan l ON sl.layanan_id = l.layanan_id
  $where
  GROUP BY s.svs_id
  ORDER BY s.tgl_masuk DESC
");
?>

<div class="container-fluid px-4 mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fa fa-history"></i> Riwayat Servis Kapal Anda</h4>
  </div>

  <!-- FILTER RANGE TANGGAL -->
  <form method="GET" action="">
    <input type="hidden" name="cs" value="History">
    <div class="d-flex flex-wrap align-items-end gap-3 mb-3">
      <div>
        <label class="form-label">Dari Tanggal</label>
        <input type="date" name="tgl_dari" value="<?= htmlspecialchars($tgl_dari) ?>" class="form-control" style="width:180px;">
      </div>
      <div>
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" name="tgl_sampai" value="<?= htmlspecialchars($tgl_sampai) ?>" class="form-control" style="width:180px;">
      </div>
      <div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tampilkan</button>
        <a href="?cs=History" class="btn btn-secondary"><i class="fa fa-refresh"></i> Reset</a>
      </div>
    </div>
  </form>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>No WO</th>
              <th>Nama Kapal</th>
              <th>Teknisi</th>
              <th>Layanan</th>
              <th>Tgl Masuk</th>
              <th>Tgl Keluar</th>
              <th>Keterangan</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($q && $q->num_rows > 0) {
              $no = 1;
              while ($row = $q->fetch_assoc()) {
                $color = match ($row['status']) {
                  'Done' => 'success',
                  'On Progress' => 'warning',
                  'On Hold' => 'secondary',
                  'Canceled' => 'danger',
                  default => 'dark'
                };

                $tglMasuk = $row['tgl_masuk'] ? date('d/m/Y', strtotime($row['tgl_masuk'])) : '-';
                $tglKeluar = $row['tgl_keluar'] ? date('d/m/Y', strtotime($row['tgl_keluar'])) : '-';
                $teknisi = $row['teknisi'] ?: '-';
                $layanan = $row['layanan'] ?: '-';
                $ket = $row['keterangan'] ?: '-';

                echo "
                <tr>
                  <td>$no</td>
                  <td class='fw-bold'>{$row['no_wo']}</td>
                  <td>{$row['nama_kapal']}</td>
                  <td>$teknisi</td>
                  <td>$layanan</td>
                  <td>$tglMasuk</td>
                  <td>$tglKeluar</td>
                  <td class='text-start'>$ket</td>
                  <td><span style ='color:white' class='badge bg-$color px-3 py-2'>{$row['status']}</span></td>
                </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='9' class='text-muted'>Belum ada data servis pada periode ini.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
  .table td {
    vertical-align: middle !important;
    font-size: 0.9rem;
  }
  .table th {
    font-size: 0.9rem;
  }
  .table .badge {
    font-size: 0.8rem;
  }
</style>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
  session_start();

include_once(__DIR__ . '/../controller/auth/db_connection.php');

// Ambil filter tanggal
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = '';
if ($from && $to) {
  $where = "
    WHERE (
      (DATE(s.tgl_masuk) BETWEEN '$from' AND '$to')
      OR (DATE(s.tgl_keluar) BETWEEN '$from' AND '$to')
      OR ('$from' BETWEEN DATE(s.tgl_masuk) AND DATE(IFNULL(s.tgl_keluar, NOW())))
      OR ('$to' BETWEEN DATE(s.tgl_masuk) AND DATE(IFNULL(s.tgl_keluar, NOW())))
    )
  ";
} elseif ($from) {
  $where = "
    WHERE (
      DATE(s.tgl_masuk) >= '$from'
      OR DATE(s.tgl_keluar) >= '$from'
    )
  ";
} elseif ($to) {
  $where = "
    WHERE (
      DATE(s.tgl_masuk) <= '$to'
      OR DATE(s.tgl_keluar) <= '$to'
    )
  ";
}


// Query utama: ambil semua servis lengkap
$q = $conn->query("
  SELECT 
    s.svs_id,
    s.no_wo,
    s.nama_kapal,
    COALESCE(v.vendor_name, '-') AS vendor_name,
    GROUP_CONCAT(DISTINCT l.layanan_nama SEPARATOR ', ') AS layanan,
    GROUP_CONCAT(DISTINCT p.pekerja_nama SEPARATOR ', ') AS teknisi,
    s.tgl_masuk,
    s.tgl_keluar,
    s.status,
    s.keterangan
  FROM tbl_servis s
  LEFT JOIN tbl_vendor v ON s.vendor_id = v.vendor_id
  LEFT JOIN tbl_servis_layanan sl ON s.svs_id = sl.svs_id
  LEFT JOIN tbl_layanan l ON sl.layanan_id = l.layanan_id
  LEFT JOIN tbl_servis_pekerja sp ON s.svs_id = sp.svs_id
  LEFT JOIN tbl_pekerja p ON sp.pekerja_id = p.pekerja_id
  $where
  GROUP BY s.svs_id
  ORDER BY s.tgl_masuk DESC
");
?>

<div class="container-fluid px-4 mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">📜 Riwayat Servis Kapal</h4>
  </div>

  <!-- Filter tanggal -->
  <form id="formFilter" method="get" action="">
    <input type="hidden" name="cs" value="History">
    <div class="mb-4 d-flex flex-wrap align-items-end gap-3">
      <div class="me-3">
        <label class="form-label mb-0">Dari Tanggal:</label>
        <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
      </div>
      <div class="me-3">
        <label class="form-label mb-0">Sampai:</label>
        <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
      </div>
      <div class="mt-2">
        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
        <a href="?cs=History" class="btn btn-secondary"><i class="fa fa-refresh"></i> Reset</a>
      </div>
    </div>
  </form>

  <?php
  if (!$q) {
    echo "<div class='alert alert-danger'>Query error: " . htmlspecialchars($conn->error) . "</div>";
  } elseif ($q->num_rows > 0) {
    echo '<ul class="list-group">';
    while ($row = $q->fetch_assoc()) {
      $tglMasuk = $row['tgl_masuk'] ? date('d-m-Y', strtotime($row['tgl_masuk'])) : '-';
      $tglKeluar = $row['tgl_keluar'] ? date('d-m-Y', strtotime($row['tgl_keluar'])) : '-';
      $layanan = $row['layanan'] ?: '-';
      $teknisi = $row['teknisi'] ?: '-';
      $keterangan = $row['keterangan'] ?: '-';

      $badge = match ($row['status']) {
        'Done' => 'success',
        'On Progress' => 'warning',
        'On Hold' => 'secondary',
        'Canceled' => 'danger',
        default => 'dark'
      };

      echo '<li class="list-group-item shadow-sm mb-4">';
      echo '<div class="d-flex justify-content-between align-items-start">';
      echo '<div class="me-3">';
      echo '<div><strong>' . htmlspecialchars($row['no_wo']) . '</strong> — ' . htmlspecialchars($row['nama_kapal']) . '</div>';
      echo '<small class="text-muted">Vendor: ' . htmlspecialchars($row['vendor_name']) . '</small><br>';
      echo '<small><b>Layanan:</b> ' . htmlspecialchars($layanan) . '</small><br>';
      echo '<small><b>Teknisi:</b> ' . htmlspecialchars($teknisi) . '</small><br>';
      echo '<small><b>Tanggal Masuk:</b> ' . $tglMasuk . '</small><br>';
      echo '<small><b>Tanggal Keluar:</b> ' . $tglKeluar . '</small><br>';
      echo '<small><b>Keterangan:</b> ' . htmlspecialchars($keterangan) . '</small>';
      echo '</div>';
      echo "<span style='color:white; padding:10px;' class='badge bg-{$badge} align-self-start'>" . htmlspecialchars($row['status']) . "</span>";
      echo '</div>';
      echo '</li>';
    }
    echo '</ul>';
  } else {
    echo "<div class='text-muted'>Tidak ada data servis untuk rentang tanggal ini.</div>";
  }
  ?>
</div>

<style>
  .list-group-item {
    border-radius: 10px;
  }

  .list-group-item small {
    display: block;
    margin-bottom: 3px;
  }

  form#formFilter .form-control {
    min-width: 180px;
  }

  @media (max-width: 768px) {
    form#formFilter {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>
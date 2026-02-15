<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SESSION['sess_usr_status'] === 'Admin') {
  echo "<div class='alert alert-info'>Gunakan dashboard admin.</div>";
  exit;
}

if (!isset($_SESSION['sess_usr_vendor']) || empty($_SESSION['sess_usr_vendor'])) {
  echo "<div class='alert alert-danger'>Akun ini belum terhubung ke data vendor. Hubungi admin.</div>";
  exit;
}

include_once(__DIR__ . '/../controller/auth/db_connection.php');

$vendor_id = $_SESSION['sess_usr_vendor'];

$stmt = $conn->prepare("
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
  WHERE s.vendor_id = ? 
   AND s.status IN ('On Progress', 'On Hold')
  GROUP BY s.svs_id
  ORDER BY s.tgl_masuk DESC
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container-fluid px-4 mt-4">
  <h4 class="mb-3">👋 Selamat datang, <?= htmlspecialchars($_SESSION['sess_usr_nama']) ?></h4>
  <p class="text-muted">Berikut status servis kapal Anda yang sedang berjalan:</p>

  <?php
  if ($result->num_rows > 0) {
    echo '<ul class="list-group">';
    while ($row = $result->fetch_assoc()) {
      $badge = match ($row['status']) {
        'Done' => 'success',
        'On Progress' => 'warning',
        'On Hold' => 'secondary',
        'Canceled' => 'danger',
        default => 'dark'
      };

      $tglMasuk = $row['tgl_masuk'] ? date('d-m-Y', strtotime($row['tgl_masuk'])) : '-';
      $tglKeluar = $row['tgl_keluar'] ? date('d-m-Y', strtotime($row['tgl_keluar'])) : '-';
      $teknisi = $row['teknisi'] ?: '-';
      $layanan = $row['layanan'] ?: '-';
      $ket = $row['keterangan'] ?: '-';

      echo '<li class="list-group-item shadow-sm mb-4 riwayat-item" data-id="' . $row['svs_id'] . '">';
      echo '<div class="d-flex justify-content-between align-items-start">';
      echo '<div class="me-3">';
      echo '<div><strong>' . htmlspecialchars($row['no_wo']) . '</strong> — ' . htmlspecialchars($row['nama_kapal']) . '</div>';
      echo '<small><b>Layanan:</b> ' . htmlspecialchars($layanan) . '</small><br>';
      echo '<small><b>Teknisi:</b> ' . htmlspecialchars($teknisi) . '</small><br>';
      echo '<small><b>Tanggal Masuk:</b> ' . $tglMasuk . '</small><br>';
      echo '<small><b>Tanggal Keluar:</b> ' . $tglKeluar . '</small><br>';
      echo '<small><b>Keterangan:</b> ' . htmlspecialchars($ket) . '</small>';
      echo '</div>';
      echo "<span class='badge bg-{$badge} align-self-start px-3 py-2' style='color:white; font-size:0.9rem;'>" . htmlspecialchars($row['status']) . "</span>";
      echo '</div>';
      echo '</li>';
    }
    echo '</ul>';
  } else {
    echo "<div class='col-12 text-muted text-start mt-3 ps-0' style='margin-left:-1rem;'>
        Belum ada servis yang sedang berjalan.<br>
        <span class='text-secondary'>Untuk melihat riwayat servis, silakan buka halaman <b>Riwayat Servis</b>.</span>
      </div>";
  }
  ?>
</div>

<style>
  .list-group-item {
    border-radius: 10px;
    padding: 15px 20px;
    background-color: #fff;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .list-group-item:hover {
    background-color: #f1f5ff;
  }

  .list-group-item small {
    display: block;
    margin-bottom: 4px;
    color: #555;
  }

  .card {
    transition: all 0.2s ease-in-out;
    border-left: 4px solid #0B97A4;
  }

  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  }

  .card-body h5 {
    font-size: 1.05rem;
  }

  .card-body h6 {
    font-size: 0.9rem;
  }

  .card-body p {
    font-size: 0.85rem;
  }

  .status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 6px 12px;
    font-size: 0.85rem;
    border-radius: 8px;
  }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  // Klik item → tampilkan modal detail
  $(document).on('click', '.riwayat-item', function () {
    const id = $(this).data('id');
    if (id) showServisDetail(id);
  });
</script>

<?php include __DIR__ . '/components/modal_detail_service.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SESSION['sess_usr_status'] === 'Admin') {
  echo "<div class='alert alert-info'>Gunakan dashboard admin.</div>";
  exit;
}

// pastikan user punya vendor id
if (!isset($_SESSION['sess_usr_vendor']) || empty($_SESSION['sess_usr_vendor'])) {
  echo "<div class='alert alert-danger'>Akun ini belum terhubung ke data vendor. Hubungi admin.</div>";
  exit;
}

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
  GROUP BY s.svs_id
  ORDER BY s.tgl_masuk DESC
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
  <h3>Selamat datang, <?= htmlspecialchars($_SESSION['sess_usr_nama']) ?> 👋</h3>
  <p class="text-muted">Status servis kapal Anda yang sedang berjalan:</p>

  <div class="row">
    <?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $color = match ($row['status']) {
          'Done' => 'success',
          'On Progress' => 'warning',
          'On Hold' => 'secondary',
          'Canceled' => 'danger',
          default => 'light'
        };

        $bgColor = match ($row['status']) {
          'Done' => '#e8f5e9',
          'On Progress' => '#fff8e1',
          'On Hold' => '#eceff1',
          'Canceled' => '#ffebee',
          default => '#f8f9fa'
        };

        $tglMasuk = $row['tgl_masuk'] ? date('d/m/Y', strtotime($row['tgl_masuk'])) : '-';
        $tglKeluar = $row['tgl_keluar'] ? date('d/m/Y', strtotime($row['tgl_keluar'])) : '-';
        $teknisi = $row['teknisi'] ?: '-';
        $layanan = $row['layanan'] ?: '-';
        $ket = $row['keterangan'] ?: '-';

        echo "
        <div class='col-md-4 mb-4'>
          <div class='card shadow-sm border-0 h-100 position-relative' style='background-color:$bgColor'>
            <span class='badge bg-$color status-badge'>{$row['status']}</span>

            <div class='card-body'>
              <h5 class='fw-bold mb-1'>{$row['no_wo']}</h5>
              <h6 class='text-muted mb-3'>{$row['nama_kapal']}</h6>

              <p class='mb-1'><strong>Masuk:</strong> $tglMasuk</p>
              <p class='mb-1'><strong>Keluar:</strong> $tglKeluar</p>
              <p class='mb-1'><strong>Teknisi:</strong> $teknisi</p>
              <p class='mb-1'><strong>Layanan:</strong> $layanan</p>
              <p class='mb-0 text-muted'><strong>Keterangan:</strong> $ket</p>
            </div>
          </div>
        </div>";
      }
    } else {
      echo "<div class='col-12 text-muted text-center'>Belum ada servis untuk vendor ini.</div>";
    }
    ?>
  </div>
</div>

<style>
  .card {
    transition: all 0.2s ease-in-out;
    border-left: 4px solid #0B97A4;
  }
  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
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

  /* posisi status di pojok kanan atas */
  .status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 6px 12px;
    font-size: 0.85rem;
    border-radius: 8px;
  }
</style>

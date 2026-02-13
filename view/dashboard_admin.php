<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
  session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

// Hanya boleh Admin & BBTeknik
if (!in_array($_SESSION['sess_usr_status'], ['Admin', 'BBTeknik'])) {
  echo "<div class='alert alert-danger'>Akses ditolak! Halaman ini hanya untuk Admin dan BB Teknik.</div>";
  exit;
}
?>

<style>
  .dashboard-wrapper {
    width: 100%;
    padding: 0 30px;
  }

  .riwayat-card {
    width: 100%;
    margin-top: 2rem;
  }

  .riwayat-card .card-body {
    padding: 20px 30px;
  }

  .riwayat-table {
    width: 100%;
    table-layout: fixed;
  }

  .riwayat-table th,
  .riwayat-table td {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    vertical-align: middle;
  }

  .riwayat-table td.keterangan {
    white-space: normal;
    width: 35%;
  }

  .list-group-item strong {
    font-size: 1rem;
  }

  @media (min-width: 1400px) {
    .dashboard-wrapper {
      max-width: 95vw;
    }
  }
</style>

<div class="dashboard-wrapper">

  <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <div>
      <h3>
        Selamat datang, <b><?= htmlspecialchars($_SESSION['sess_usr_nama']); ?></b> 👋
        <?php if ($_SESSION['sess_usr_status'] === 'BBTeknik'): ?>
          <small>(BB Teknik)</small>
        <?php elseif ($_SESSION['sess_usr_status'] === 'Admin'): ?>
          <small>(Administrator)</small>
        <?php endif; ?>
      </h3>
      <p class="text-muted mb-0">Dashboard Bengkel Kapal Bintang Biru Teknik</p>

    </div>
  </div>

  <!-- CARD ATAS -->
  <div class="row mt-4 gx-4">

    <!-- Card 1: Pemasukan Bulan Ini -->
    <div class="col-md-4 mb-4">
      <?php
      $q3 = $conn->query("
        SELECT 
          SUM(CASE WHEN tipe='Debit' THEN total ELSE 0 END) AS total_debit
        FROM tbl_cashflow
        WHERE MONTH(tanggal)=MONTH(NOW()) 
          AND YEAR(tanggal)=YEAR(NOW())
      ");
      $rowCash = $q3 ? $q3->fetch_assoc() : ['total_debit' => 0];
      $total_debit = $rowCash['total_debit'] ?? 0;

      ?>
      <div class="card text-white h-100 shadow-sm border-0" style="background-color: darkgreen;">
        <div class="card-body">
          <h5 class="card-title">Pemasukan Bulan Ini</h5>
          <h5 class="mb-0"> Rp
            <?= number_format($total_debit, 0, ',', '.') ?>
          </h5>
        </div>
        <a href="?cs=Cashflow" class="card-footer text-white text-center border-0">Lihat Cashflow</a>
      </div>
    </div>

    <!-- Card 2: Pengeluaran Bulan Ini -->
    <div class="col-md-4 mb-4">
      <?php
      $q3 = $conn->query("
        SELECT 
          SUM(CASE WHEN tipe='Kredit' THEN total ELSE 0 END) AS total_kredit
        FROM tbl_cashflow
        WHERE MONTH(tanggal)=MONTH(NOW()) 
          AND YEAR(tanggal)=YEAR(NOW())
      ");
      $rowCash = $q3 ? $q3->fetch_assoc() : ['total_kredit' => 0];
      $total_kredit = $rowCash['total_kredit'] ?? 0;
      ?>
      <div class="card text-white h-100 shadow-sm border-0" style="background-color: firebrick;">
        <div class="card-body pb-5">
          <h5 class="card-title">Pengeluaran Bulan Ini</h5>
          <h5 class="mb-0"> Rp
            <?= number_format($total_kredit, 0, ',', '.') ?>
          </h5>
        </div>
        <a href="?cs=Cashflow" class="card-footer text-white text-center border-0">Lihat Cashflow</a>
      </div>
    </div>

    <!-- Card 3: Servis Bulan Ini -->
    <div class="col-md-4 mb-4">
      <div class="card text-white h-100 shadow-sm border-0" style="background-color: darkblue">
        <div class="card-body">
          <h5 class="card-title">🛠 Servis Bulan Ini</h5>
          <?php
          $q2 = $conn->query("SELECT COUNT(*) as total 
                              FROM tbl_servis 
                              WHERE (status='On Progress' OR status='Pending') 
                              AND MONTH(tgl_masuk)=MONTH(CURRENT_DATE()) 
                              AND YEAR(tgl_masuk)=YEAR(CURRENT_DATE())");
          $totalServis = $q2 ? $q2->fetch_assoc()['total'] : 0;
          ?>
          <h2><?php echo $totalServis; ?></h2>
        </div>
        <a href="?cs=Service" class="card-footer text-white text-center border-0">Lihat Servis</a>
      </div>
    </div>
  </div>

  <!-- RIWAYAT SERVIS -->
  <div class="card shadow-sm border-0 mt-4">
    <div class="card-body px-4 py-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0">Riwayat Servis Terbaru</h5>
        <a href="?cs=Service" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>

      <?php
      $q = $conn->query("
        SELECT 
          s.no_wo,
          s.nama_kapal,
          v.vendor_name,
          GROUP_CONCAT(DISTINCT p.pekerja_nama SEPARATOR ', ') AS teknisi,
          s.status,
          s.tgl_masuk,
          s.keterangan
        FROM tbl_servis s
        LEFT JOIN tbl_vendor v ON s.vendor_id = v.vendor_id
        LEFT JOIN tbl_servis_pekerja sp ON s.svs_id = sp.svs_id
        LEFT JOIN tbl_pekerja p ON sp.pekerja_id = p.pekerja_id
        GROUP BY s.svs_id
        ORDER BY s.tgl_masuk DESC
        LIMIT 5
      ");

      if (!$q) {
        echo "<div class='alert alert-danger'>Query error: " . htmlspecialchars($conn->error) . "</div>";
      } else {
        if ($q->num_rows > 0) {
          echo '<ul class="list-group">';
          while ($row = $q->fetch_assoc()) {
            $tgl = $row['tgl_masuk'] ? date('Y-m-d', strtotime($row['tgl_masuk'])) : '-';
            $teknisi = $row['teknisi'] ?: '-';
            $keterangan = $row['keterangan'] ?: '-';

            echo '<li class="list-group-item d-flex justify-content-between align-items-start">';
            echo '<div class="ms-2 me-auto">';
            echo '<div><strong>' . htmlspecialchars($row['no_wo']) . '</strong> — ' . htmlspecialchars($row['nama_kapal']) . '</div>';
            echo '<small class="text-muted">Vendor: ' . htmlspecialchars($row['vendor_name']) . ' • Tgl masuk: ' . $tgl . '</small><br>';
            echo '<small>Teknisi: ' . htmlspecialchars($teknisi) . '</small><br>';
            echo '<small><b>Keterangan:</b> ' . htmlspecialchars($keterangan) . '</small>';
            echo '</div>';

            $badge = match ($row['status']) {
              'Done' => 'success',
              'On Progress' => 'warning',
              'On Hold' => 'secondary',
              'Canceled' => 'danger',
              default => 'dark'
            };
            echo "<span style='color:white; padding:10px;' class='badge bg-{$badge}'>" . htmlspecialchars($row['status']) . "</span>";
            echo '</li>';
          }
          echo '</ul>';
        } else {
          echo "<div class='text-muted'>Belum ada data servis.</div>";
        }
      }
      ?>
    </div>
  </div>

</div>
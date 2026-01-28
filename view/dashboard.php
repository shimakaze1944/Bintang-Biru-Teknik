<?php 
include_once(__DIR__ . '/controller/auth/db_connection.php');
session_start();
$status = $_SESSION['sess_usr_status'];
$nama = $_SESSION['sess_usr_nama'];
?>

<div class="container-fluid">

  <h3 class="mb-4">Selamat Datang, <?= htmlspecialchars($nama) ?> 👋</h3>

  <?php if($status === 'Admin'): ?>
    <!-- ADMIN DASHBOARD -->
    <div class="row">
      <!-- CASHFLOW CARD -->
      <?php
        $q_pendapatan = $conn->query("SELECT SUM(total_service) AS total FROM tbl_servis");
        $pendapatan = $q_pendapatan->fetch_assoc()['total'] ?? 0;

        $q_biaya = $conn->query("SELECT SUM(biaya_pekerja + biaya_bahan) AS total FROM tbl_servis");
        $biaya = $q_biaya->fetch_assoc()['total'] ?? 0;

        $cashflow = $pendapatan - $biaya;
        $warna = ($cashflow >= 0) ? 'bg-success' : 'bg-danger';
      ?>
      <div class="col-xl-4 col-sm-6 mb-3">
        <div class="card text-white <?= $warna ?> o-hidden h-100">
          <div class="card-body">
            <div class="card-body-icon"><i class="fa fa-fw fa-money"></i></div>
            <div class="mr-5"><b>Cashflow Hari Ini</b><br>Rp <?= number_format($cashflow, 0, ',', '.') ?></div>
          </div>
        </div>
      </div>

      <!-- LINK MENU ADMIN -->
      <div class="col-xl-8 col-sm-6 mb-3">
        <div class="card o-hidden h-100">
          <div class="card-body">
            <h5>Menu Admin</h5>
            <ul>
              <li><a href="?cs=Master-User">Tambah User</a></li>
              <li><a href="?cs=Master-Mekanik">Tambah Pekerja</a></li>
              <li><a href="?cs=Master-Supplier">Tambah Vendor</a></li>
              <li><a href="?cs=Master-Mobil">Tambah Kapal</a></li>
              <li><a href="?cs=Transaksi-Servis">Tambah History / Servis Kapal</a></li>
              <li><a href="?cs=Transaksi-Klaim">Tambah Layanan Jasa</a></li>
              <li><a href="?cs=Laporan-Servis">Lihat Semua History</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  <?php else: ?>
    <!-- USER DASHBOARD -->
    <div class="row">
      <div class="col-xl-12 mb-3">
        <div class="card o-hidden h-100">
          <div class="card-body">
            <h5 class="mb-3"><i class="fa fa-fw fa-history"></i> History Servis Kapal Anda</h5>
            <div class="row">
              <?php
                $email = $_SESSION['sess_usr_email'];
                $q = $conn->query("SELECT nama_kapal, nama_pekerja, tgl_masuk, tgl_keluar, status 
                                   FROM tbl_servis WHERE usr_email = '$email' ORDER BY tgl_masuk DESC");
                if($q->num_rows > 0):
                  while($r = $q->fetch_assoc()):
                    switch(strtolower($r['status'])){
                      case 'canceled': $color='bg-danger'; break;
                      case 'on progress': $color='bg-warning'; break;
                      case 'on hold': $color='bg-secondary'; break;
                      case 'done': $color='bg-success'; break;
                      default: $color='bg-light text-dark'; break;
                    }
              ?>
                <div class="col-md-4 mb-3">
                  <div class="card text-white <?= $color ?> h-100">
                    <div class="card-body">
                      <b><?= htmlspecialchars($r['nama_kapal']) ?></b><br>
                      Pekerja: <?= htmlspecialchars($r['nama_pekerja']) ?><br>
                      Masuk: <?= $r['tgl_masuk'] ?><br>
                      Keluar: <?= $r['tgl_keluar'] ?><br>
                      Status: <?= ucfirst($r['status']) ?>
                    </div>
                  </div>
                </div>
              <?php
                  endwhile;
                else:
                  echo "<p class='ml-3'>Belum ada history servis.</p>";
                endif;
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

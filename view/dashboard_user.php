<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SESSION['sess_usr_status'] === 'Admin') {
  echo "<div class='alert alert-info'>Gunakan dashboard admin.</div>";
  exit;
}
?>

<div class="container mt-4">
  <h3>Selamat datang, <?php echo $_SESSION['sess_usr_nama']; ?> 👋</h3>
  <p class="text-muted">Status servis kapal Anda Yang Sedang Berjalan:</p>

  <div class="row">
    <?php
    $email = $_SESSION['sess_usr_nama'];

    $stmt = $conn->prepare("SELECT nama_kapal, teknisi, tgl_masuk, tgl_keluar, status 
                            FROM tbl_servis WHERE pelanggan_email = ? 
                            ORDER BY tgl_masuk DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {

        $color = match($row['status']) {
          'Done' => 'success',
          'On Progress' => 'warning',
          'On Hold' => 'secondary',
          'Canceled' => 'danger',
          default => 'light'
        };

        echo "
        <div class='col-md-4 mb-4'>
          <div class='card border-$color shadow'>
            <div class='card-body'>
              <h5>{$row['nama_kapal']}</h5>
              <p>Teknisi: {$row['teknisi']}</p>
              <p>Masuk: {$row['tgl_masuk']}</p>
              <p>Keluar: {$row['tgl_keluar']}</p>
              <span class='badge bg-$color'>{$row['status']}</span>
            </div>
          </div>
        </div>";
      }
    } else {
      echo "<div class='col-12 text-muted'>Belum ada servis.</div>";
    }
    ?>
  </div>
</div>

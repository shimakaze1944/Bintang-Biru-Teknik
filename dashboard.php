<?php 
include_once(__DIR__ . '/controller/auth/db_connection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container-fluid">
  <h3 class="mb-4">Selamat Datang, <?= htmlspecialchars($nama) ?> 👋</h3>
</div>

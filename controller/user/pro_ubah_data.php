<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

if (!isset($_SESSION['sess_usr_id'])) {
  echo "Session tidak valid!";
  exit;
}

$usr_id   = (int)($_POST['usr_ubah_id'] ?? 0);
$username = trim($_POST['usr_ubah_username'] ?? '');
$nama     = trim($_POST['usr_ubah_nama'] ?? '');
$email    = trim($_POST['usr_ubah_email'] ?? '');
$alamat   = trim($_POST['usr_ubah_alamat'] ?? '');
$tlp      = trim($_POST['usr_ubah_tlp'] ?? '');

if ($usr_id <= 0 || $username === '' || $nama === '' || $email === '') {
  echo "Data belum lengkap!";
  exit;
}

// pastikan username & email unik (kecuali miliknya sendiri)
$cek = $conn->prepare("SELECT usr_id FROM tbl_user WHERE (usr_username = ? OR usr_email = ?) AND usr_id != ?");
$cek->bind_param("ssi", $username, $email, $usr_id);
$cek->execute();
if ($cek->get_result()->fetch_assoc()) {
  echo "Username atau email sudah digunakan!";
  exit;
}

// update data
$stmt = $conn->prepare("UPDATE tbl_user 
  SET usr_username=?, usr_nama=?, usr_email=?, usr_alamat=?, usr_tlp=? 
  WHERE usr_id=?");
$stmt->bind_param("sssssi", $username, $nama, $email, $alamat, $tlp, $usr_id);

if ($stmt->execute()) {
  // update session
  $_SESSION['sess_usr_username'] = $username;
  $_SESSION['sess_usr_nama']     = $nama;
  $_SESSION['sess_usr_email']    = $email;
  $_SESSION['sess_usr_alamat']   = $alamat;
  $_SESSION['sess_usr_tlp']      = $tlp;
  echo "OK";
} else {
  echo "Gagal update: " . $conn->error;
}

$conn->close();
?>

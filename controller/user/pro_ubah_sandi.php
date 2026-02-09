<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

if (!isset($_SESSION['sess_usr_id'])) {
  echo "Session tidak valid!";
  exit;
}

$usr_id = (int)($_POST['usr_id'] ?? 0);
$old_pass = $_POST['old_pass'] ?? '';
$new_pass = $_POST['new_pass'] ?? '';
$confirm_pass = $_POST['confirm_pass'] ?? '';

if ($usr_id <= 0 || $old_pass === '' || $new_pass === '' || $confirm_pass === '') {
  echo "Data belum lengkap!";
  exit;
}

if ($new_pass !== $confirm_pass) {
  echo "Konfirmasi password tidak cocok!";
  exit;
}

// ambil password lama dari db
$stmt = $conn->prepare("SELECT usr_pass FROM tbl_user WHERE usr_id = ?");
$stmt->bind_param("i", $usr_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row || !password_verify($old_pass, $row['usr_pass'])) {
  echo "Password lama salah!";
  exit;
}

// cek biar password baru beda sama yang lama
if (password_verify($new_pass, $row['usr_pass'])) {
  echo "Password baru tidak boleh sama dengan password lama!";
  exit;
}

// update password baru (hash)
$new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE tbl_user SET usr_pass=? WHERE usr_id=?");
$update->bind_param("si", $new_hash, $usr_id);

if ($update->execute()) {
  echo "OK";
} else {
  echo "Gagal update password: " . $conn->error;
}

$conn->close();
?>

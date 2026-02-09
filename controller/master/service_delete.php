<?php
include_once(__DIR__ . '/../controller/auth/db_connection.php');
session_start();

if (!in_array($_SESSION['sess_usr_status'], ['Admin','BBTeknik'])) {
  echo "Akses ditolak!";
  exit;
}

$id = intval($_POST['svs_id'] ?? 0);
if ($id <= 0) {
  echo "Data tidak valid!";
  exit;
}

$stmt = $conn->prepare("DELETE FROM tbl_servis WHERE svs_id=?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
  header("Location: ../home.php?cs=Service");
  exit;
} else {
  echo "Gagal hapus servis: ".$conn->error;
}
?>

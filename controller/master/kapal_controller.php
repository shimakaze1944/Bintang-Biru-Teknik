<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

// Akses hanya Admin & BB Teknik
$allowed = ['Admin', 'BBTeknik'];
if (!isset($_SESSION['sess_usr_status']) || !in_array($_SESSION['sess_usr_status'], $allowed)) {
  echo "Akses ditolak!";
  exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

if ($method === 'GET') {
  // Ambil data kapal
  if ($action === 'get' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT kapal_id, nama_kapal, vendor_id, keterangan FROM tbl_kapal WHERE kapal_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($data ?: []);
    exit;
  }

  // Hapus
  if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM tbl_kapal WHERE kapal_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      header("Location: ../../home.php?cs=Master-Kapal");
    } else {
      echo "Gagal menghapus kapal: " . $conn->error;
    }
    exit;
  }
}

if ($method === 'POST') {
  $nama_kapal = trim($_POST['nama_kapal'] ?? '');
  $vendor_id  = (int)($_POST['vendor_id'] ?? 0);
  $keterangan = trim($_POST['keterangan'] ?? '');
  $kapal_id   = (int)($_POST['kapal_id'] ?? 0);
  $action     = $_POST['action'] ?? '';

  if ($nama_kapal === '' || $vendor_id === 0) {
    echo "Data belum lengkap!";
    exit;
  }

  // Tambah
  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO tbl_kapal (nama_kapal, vendor_id, keterangan) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nama_kapal, $vendor_id, $keterangan);
    if ($stmt->execute()) echo "OK"; else echo "Gagal tambah kapal: " . $conn->error;
    exit;
  }

  // Edit
  if ($action === 'edit' && $kapal_id > 0) {
    $stmt = $conn->prepare("UPDATE tbl_kapal SET nama_kapal=?, vendor_id=?, keterangan=? WHERE kapal_id=?");
    $stmt->bind_param("sisi", $nama_kapal, $vendor_id, $keterangan, $kapal_id);
    if ($stmt->execute()) echo "OK"; else echo "Gagal update: " . $conn->error;
    exit;
  }
}

echo "No action.";
$conn->close();
?>

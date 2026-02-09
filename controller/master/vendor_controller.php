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
  // Ambil data vendor
  if ($action === 'get' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM tbl_vendor WHERE vendor_id = ?");
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
    $stmt = $conn->prepare("DELETE FROM tbl_vendor WHERE vendor_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      header("Location: ../../home.php?cs=Master-Vendor");
    } else {
      echo "Gagal menghapus vendor: " . $conn->error;
    }
    exit;
  }
}

if ($method === 'POST') {
  $vendor_id = (int)($_POST['vendor_id'] ?? 0);
  $vendor_name = trim($_POST['vendor_name'] ?? '');
  $vendor_alamat = trim($_POST['vendor_alamat'] ?? '');
  $vendor_kontak = trim($_POST['vendor_kontak'] ?? '');
  $vendor_keterangan = trim($_POST['vendor_keterangan'] ?? '');
  $action = $_POST['action'] ?? '';

  if ($vendor_name === '') {
    echo "Nama vendor wajib diisi!";
    exit;
  }

  // Tambah
  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO tbl_vendor (vendor_name, vendor_alamat, vendor_kontak, vendor_keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $vendor_name, $vendor_alamat, $vendor_kontak, $vendor_keterangan);
    if ($stmt->execute()) echo "OK"; else echo "Gagal tambah vendor: " . $conn->error;
    exit;
  }

  // Edit
  if ($action === 'edit' && $vendor_id > 0) {
    $stmt = $conn->prepare("UPDATE tbl_vendor SET vendor_name=?, vendor_alamat=?, vendor_kontak=?, vendor_keterangan=? WHERE vendor_id=?");
    $stmt->bind_param("ssssi", $vendor_name, $vendor_alamat, $vendor_kontak, $vendor_keterangan, $vendor_id);
    if ($stmt->execute()) echo "OK"; else echo "Gagal update: " . $conn->error;
    exit;
  }
}

echo "No action.";
$conn->close();
?>

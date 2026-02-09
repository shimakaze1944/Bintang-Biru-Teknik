<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

$allowed = ['Admin', 'BBTeknik'];
if (!isset($_SESSION['sess_usr_status']) || !in_array($_SESSION['sess_usr_status'], $allowed)) {
  echo "Akses ditolak!";
  exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

if ($method === 'GET') {
  // Ambil data layanan
  if ($action === 'get' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT layanan_id, layanan_nama, layanan_harga, layanan_keterangan FROM tbl_layanan WHERE layanan_id = ?");
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
    $stmt = $conn->prepare("DELETE FROM tbl_layanan WHERE layanan_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      header("Location: ../../home.php?cs=Master-Layanan");
    } else {
      echo "Gagal menghapus layanan: " . $conn->error;
    }
    exit;
  }
}

if ($method === 'POST') {
  $layanan_id   = (int)($_POST['layanan_id'] ?? 0);
  $layanan_nama = trim($_POST['layanan_nama'] ?? '');
  $layanan_harga = (float)($_POST['layanan_harga'] ?? 0);
  $layanan_keterangan = trim($_POST['layanan_keterangan'] ?? '');
  $action       = $_POST['action'] ?? '';

  if ($layanan_nama === '') {
    echo "Nama layanan tidak boleh kosong!";
    exit;
  }

  // Tambah
  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO tbl_layanan (layanan_nama, layanan_harga, layanan_keterangan) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $layanan_nama, $layanan_harga, $layanan_keterangan);
    if ($stmt->execute()) echo "OK"; else echo "Gagal tambah layanan: " . $conn->error;
    exit;
  }

  // Edit
  if ($action === 'edit' && $layanan_id > 0) {
    $stmt = $conn->prepare("UPDATE tbl_layanan SET layanan_nama=?, layanan_harga=?, layanan_keterangan=? WHERE layanan_id=?");
    $stmt->bind_param("sdsi", $layanan_nama, $layanan_harga, $layanan_keterangan, $layanan_id);
    if ($stmt->execute()) echo "OK"; else echo "Gagal update: " . $conn->error;
    exit;
  }
}

echo "No action.";
$conn->close();
?>

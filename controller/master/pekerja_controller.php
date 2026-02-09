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

  // Ambil data pekerja
  if ($action === 'get' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT pekerja_id, pekerja_nama, pekerja_kontak, pekerja_alamat, pekerja_keterangan FROM tbl_pekerja WHERE pekerja_id = ?");
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
    $stmt = $conn->prepare("DELETE FROM tbl_pekerja WHERE pekerja_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      header("Location: ../../home.php?cs=Master-Pekerja");
    } else {
      echo "Gagal hapus: " . $conn->error;
    }
    exit;
  }
}

if ($method === 'POST') {
  $id = (int)($_POST['pekerja_id'] ?? 0);
  $nama = trim($_POST['pekerja_nama'] ?? '');
  $kontak = trim($_POST['pekerja_kontak'] ?? '');
  $alamat = trim($_POST['pekerja_alamat'] ?? '');
  $keterangan = trim($_POST['pekerja_keterangan'] ?? '');
  $action = $_POST['action'] ?? '';

  if ($nama === '' || $kontak === '' || $alamat === '' || $keterangan === '' ) {
    echo "Data belum lengkap!";
    exit;
  }

  // Tambah
  if ($action === 'create') {
    $stmt = $conn->prepare("INSERT INTO tbl_pekerja (pekerja_nama, pekerja_kontak, pekerja_alamat, pekerja_keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $kontak, $alamat, $keterangan);
    if ($stmt->execute()) echo "OK"; else echo "Gagal tambah: " . $conn->error;
    exit;
  }

  // Edit
  if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("UPDATE tbl_pekerja SET pekerja_nama=?, pekerja_kontak=?, pekerja_alamat=?, pekerja_keterangan=? WHERE pekerja_id=?");
    $stmt->bind_param("ssssi", $nama, $kontak, $alamat, $keterangan, $id);
    if ($stmt->execute()) echo "OK"; else echo "Gagal update: " . $conn->error;
    exit;
  }
}

echo "No action.";
$conn->close();
?>

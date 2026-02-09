<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

$action = $_REQUEST['action'] ?? '';

if ($action === 'create') {
  $tanggal = $_POST['tanggal'] ?? null;
  $tipe = $_POST['tipe'] ?? null;
  $total = $_POST['total'] ?? 0;
  $keterangan = $_POST['keterangan'] ?? '';
  $created_by = $_SESSION['sess_usr_nama'] ?? 'Unknown';

  if (!$tanggal || !$tipe || !$total) {
    echo "Field tidak boleh kosong!";
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO tbl_cashflow (tanggal, tipe, total, keterangan, created_by) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdss", $tanggal, $tipe, $total, $keterangan, $created_by);
  if ($stmt->execute()) echo "OK";
  else echo "Gagal: " . $conn->error;
  exit;
}

if ($action === 'edit') {
  $id = (int)$_POST['id'];
  $tanggal = $_POST['tanggal'];
  $tipe = $_POST['tipe'];
  $total = $_POST['total'];
  $keterangan = $_POST['keterangan'];

  $stmt = $conn->prepare("UPDATE tbl_cashflow SET tanggal=?, tipe=?, total=?, keterangan=? WHERE id=?");
  $stmt->bind_param("ssdsi", $tanggal, $tipe, $total, $keterangan, $id);
  if ($stmt->execute()) echo "OK";
  else echo "Gagal: " . $conn->error;
  exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $conn->query("DELETE FROM tbl_cashflow WHERE id=$id");
  echo "OK";
  exit;
}

if ($action === 'get' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $res = $conn->query("SELECT * FROM tbl_cashflow WHERE id=$id");
  echo json_encode($res->fetch_assoc());
  exit;
}

echo "No action";
$conn->close();

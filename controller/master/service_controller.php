<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

$action = $_REQUEST['action'] ?? '';

if ($action === 'get' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];

  $q = $conn->prepare("SELECT * FROM tbl_servis WHERE svs_id=?");
  $q->bind_param("i", $id);
  $q->execute();
  $data = $q->get_result()->fetch_assoc();

  // ambil layanan
  $layanan = [];
  $res1 = $conn->query("SELECT layanan_id FROM tbl_servis_layanan WHERE svs_id=$id");
  while ($r = $res1->fetch_assoc()) $layanan[] = $r['layanan_id'];

  // ambil pekerja
  $pekerja = [];
  $res2 = $conn->query("SELECT pekerja_id FROM tbl_servis_pekerja WHERE svs_id=$id");
  while ($r = $res2->fetch_assoc()) $pekerja[] = $r['pekerja_id'];

  $data['layanan'] = $layanan;
  $data['pekerja'] = $pekerja;

  header('Content-Type: application/json');
  echo json_encode($data ?: []);
  exit;
}

if ($action === 'create') {
  $no_wo = $_POST['no_wo'];
  $nama_kapal = $_POST['nama_kapal'];
  $vendor_id = $_POST['vendor_id'] ?? null;
  $tgl_masuk = $_POST['tgl_masuk'];
  $tgl_keluar = $_POST['tgl_keluar'] ?: null;
  $status = $_POST['status'];
  $ket = $_POST['keterangan'];

  $stmt = $conn->prepare("INSERT INTO tbl_servis (no_wo, nama_kapal, vendor_id, tgl_masuk, tgl_keluar, status, keterangan) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssissss", $no_wo, $nama_kapal, $vendor_id, $tgl_masuk, $tgl_keluar, $status, $ket);
  if (!$stmt->execute()) {
    echo "Gagal: " . $conn->error;
    exit;
  }
  $svs_id = $conn->insert_id;

  // simpan layanan
  if (!empty($_POST['layanan'])) {
    foreach ($_POST['layanan'] as $l) {
      $conn->query("INSERT INTO tbl_servis_layanan VALUES ($svs_id,$l)");
    }
  }

  // simpan pekerja
  if (!empty($_POST['pekerja'])) {
    foreach ($_POST['pekerja'] as $p) {
      $conn->query("INSERT INTO tbl_servis_pekerja VALUES ($svs_id,$p)");
    }
  }

  echo "OK";
  exit;
}

if ($action === 'edit') {
  $svs_id = (int)$_POST['svs_id'];
  $no_wo = $_POST['no_wo'];
  $nama_kapal = $_POST['nama_kapal'];
  $vendor_id = $_POST['vendor_id'] ?? null;
  $tgl_masuk = $_POST['tgl_masuk'];
  $tgl_keluar = $_POST['tgl_keluar'] ?: null;
  $status = $_POST['status'];
  $ket = $_POST['keterangan'];

  $stmt = $conn->prepare("UPDATE tbl_servis 
                          SET no_wo=?, nama_kapal=?, vendor_id=?, tgl_masuk=?, tgl_keluar=?, status=?, keterangan=? 
                          WHERE svs_id=?");
  $stmt->bind_param("ssissssi", $no_wo, $nama_kapal, $vendor_id, $tgl_masuk, $tgl_keluar, $status, $ket, $svs_id);
  if (!$stmt->execute()) {
    echo "Gagal update: " . $conn->error;
    exit;
  }

  // hapus relasi lama
  $conn->query("DELETE FROM tbl_servis_layanan WHERE svs_id=$svs_id");
  $conn->query("DELETE FROM tbl_servis_pekerja WHERE svs_id=$svs_id");

  // simpan relasi baru
  if (!empty($_POST['layanan'])) {
    foreach ($_POST['layanan'] as $l) {
      $conn->query("INSERT INTO tbl_servis_layanan VALUES ($svs_id,$l)");
    }
  }
  if (!empty($_POST['pekerja'])) {
    foreach ($_POST['pekerja'] as $p) {
      $conn->query("INSERT INTO tbl_servis_pekerja VALUES ($svs_id,$p)");
    }
  }

  echo "OK";
  exit;
}

if ($action === 'delete' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $conn->query("DELETE FROM tbl_servis_layanan WHERE svs_id=$id");
  $conn->query("DELETE FROM tbl_servis_pekerja WHERE svs_id=$id");
  $conn->query("DELETE FROM tbl_servis WHERE svs_id=$id");
  echo "OK";
  exit;
}

echo "No action";
$conn->close();
?>

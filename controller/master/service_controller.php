<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

$action = $_REQUEST['action'] ?? '';

// ================= GET DETAIL SERVIS =================
if ($action === 'get' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];

  // Ambil data utama + vendor
  $q = $conn->prepare("
    SELECT 
      s.*, 
      v.vendor_name 
    FROM tbl_servis s
    LEFT JOIN tbl_vendor v ON s.vendor_id = v.vendor_id
    WHERE s.svs_id = ?
  ");
  $q->bind_param("i", $id);
  $q->execute();
  $data = $q->get_result()->fetch_assoc();

  if (!$data) {
    echo json_encode([]);
    exit;
  }

  // Ambil detail layanan (nama + harga)
  $layanan_detail = [];
  $res1 = $conn->query("
    SELECT l.layanan_id, l.layanan_nama AS nama, l.layanan_harga AS harga
    FROM tbl_servis_layanan sl
    JOIN tbl_layanan l ON sl.layanan_id = l.layanan_id
    WHERE sl.svs_id = $id
  ");
  while ($r = $res1->fetch_assoc()) {
    $layanan_detail[] = $r;
  }

  // Ambil pekerja (nama)
  $pekerja = [];
  $pekerja_nama = [];
  $res2 = $conn->query("
    SELECT p.pekerja_id, p.pekerja_nama 
    FROM tbl_servis_pekerja sp
    JOIN tbl_pekerja p ON sp.pekerja_id = p.pekerja_id
    WHERE sp.svs_id = $id
  ");
  while ($r = $res2->fetch_assoc()) {
    $pekerja[] = $r['pekerja_id'];
    $pekerja_nama[] = $r['pekerja_nama'];
  }

  // Total harga (kalau kolom di DB 0, hitung dari layanan)
  $total = $data['total_harga'];
  if (floatval($total) <= 0 && count($layanan_detail) > 0) {
    $total = array_sum(array_column($layanan_detail, 'harga'));
  }

  // Tambahkan data hasil
  $data['layanan'] = array_column($layanan_detail, 'layanan_id');
  $data['layanan_detail'] = $layanan_detail;
  $data['pekerja'] = $pekerja;
  $data['pekerja_nama'] = implode(', ', $pekerja_nama);
  $data['total_harga'] = $total;

  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

// ================= GET TOTAL HARGA LAYANAN =================
if ($action === 'get_price' && isset($_GET['ids'])) {
  $ids = $_GET['ids'];
  $ids_clean = implode(',', array_map('intval', explode(',', $ids)));
  $q = $conn->query("SELECT SUM(layanan_harga) as total FROM tbl_layanan WHERE layanan_id IN ($ids_clean)");
  $total = $q->fetch_assoc()['total'] ?? 0;
  echo $total;
  exit;
}

// ================= CREATE SERVIS =================
if ($action === 'create') {
  $no_wo = $_POST['no_wo'];
  $nama_kapal = $_POST['nama_kapal'];
  $vendor_id = $_POST['vendor_id'] ?? null;
  $tgl_masuk = $_POST['tgl_masuk'];
  $tgl_keluar = $_POST['tgl_keluar'] ?: null;
  $status = $_POST['status'];
  $ket = $_POST['keterangan'];
  $total_harga = $_POST['total_harga'] ? str_replace(',', '', $_POST['total_harga']) : 0;

  $stmt = $conn->prepare("INSERT INTO tbl_servis 
  (no_wo, nama_kapal, vendor_id, tgl_masuk, tgl_keluar, status, keterangan, total_harga) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssissssd", $no_wo, $nama_kapal, $vendor_id, $tgl_masuk, $tgl_keluar, $status, $ket, $total_harga);

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

// ================= EDIT SERVIS =================
if ($action === 'edit') {
  $svs_id = (int)$_POST['svs_id'];
  $no_wo = $_POST['no_wo'];
  $nama_kapal = $_POST['nama_kapal'];
  $vendor_id = $_POST['vendor_id'] ?? null;
  $tgl_masuk = $_POST['tgl_masuk'];
  $tgl_keluar = $_POST['tgl_keluar'] ?: null;
  $status = $_POST['status'];
  $ket = $_POST['keterangan'];
  $total_harga = $_POST['total_harga'] ? str_replace(',', '', $_POST['total_harga']) : 0;

  $stmt = $conn->prepare("UPDATE tbl_servis 
  SET no_wo=?, nama_kapal=?, vendor_id=?, tgl_masuk=?, tgl_keluar=?, status=?, keterangan=?, total_harga=? 
  WHERE svs_id=?");
  $stmt->bind_param("ssissssdi", $no_wo, $nama_kapal, $vendor_id, $tgl_masuk, $tgl_keluar, $status, $ket, $total_harga, $svs_id);

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

// ================= DELETE SERVIS =================
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
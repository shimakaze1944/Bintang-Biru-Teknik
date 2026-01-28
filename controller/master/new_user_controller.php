<?php
header('Content-Type: text/plain; charset=utf-8');
session_start();
include_once(__DIR__ . '/controller/auth/db_connection.php'); // sesuaikan path

// Ambil input
$usr_status = $_POST['usr_status'] ?? '';
$usr_nama   = $_POST['usr_nama'] ?? '';
$usr_alamat = $_POST['usr_alamat'] ?? '';
$usr_email  = $_POST['usr_email'] ?? '';
$usr_tlp    = $_POST['usr_tlp'] ?? '';
$usr_pass   = $_POST['usr_pass'] ?? '';
$usr_konfir = $_POST['usr_konfir_pass'] ?? '';

// Validasi dasar
if(empty($usr_email) || empty($usr_pass) || empty($usr_konfir) || empty($usr_nama)){
  http_response_code(400);
  echo "Lengkapi semua data wajib!";
  exit;
}
if(strlen($usr_pass) < 8){
  http_response_code(400);
  echo "Password minimal 8 karakter!";
  exit;
}
if($usr_pass !== $usr_konfir){
  http_response_code(400);
  echo "Password dan konfirmasi tidak sama!";
  exit;
}

// Cek email unik
$stmt = $conn->prepare("SELECT usr_id FROM tbl_user WHERE usr_email = ?");
$stmt->bind_param('s', $usr_email);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows > 0){
  echo "Email sudah terdaftar!";
  $stmt->close();
  exit;
}
$stmt->close();

// Hash password
$hash = password_hash($usr_pass, PASSWORD_BCRYPT);

// Simpan ke database
$query = $conn->prepare("INSERT INTO tbl_user (usr_status, usr_nama, usr_alamat, usr_email, usr_tlp, usr_pass) VALUES (?, ?, ?, ?, ?, ?)");
$query->bind_param('ssssss', $usr_status, $usr_nama, $usr_alamat, $usr_email, $usr_tlp, $hash);

if($query->execute()){
  echo "✅ User berhasil ditambahkan!";
} else {
  http_response_code(500);
  echo "Gagal menyimpan user!";
}

$query->close();
$conn->close();
?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/db_connection.php');

$lgn_user = trim($_POST['lgn_user'] ?? '');
$lgn_pass = $_POST['lgn_pass'] ?? '';

if ($lgn_user === '' || $lgn_pass === '') {
    echo "Username dan Password wajib diisi!";
    exit;
}

// ambil user berdasarkan username
$stmt = $conn->prepare("SELECT * FROM tbl_user WHERE usr_username = ? LIMIT 1");
$stmt->bind_param("s", $lgn_user);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();

    if (password_verify($lgn_pass, trim($user['usr_pass']))) {
        $_SESSION['sess_usr_id'] = $user['usr_id'];
        $_SESSION['sess_usr_nama'] = $user['usr_nama'];
        $_SESSION['sess_usr_status'] = $user['usr_status'];
        $_SESSION['sess_usr_vendor'] = $user['vendor_id'] ?? null;

        echo "OK";
    } else {
        echo "Password salah!";
    }
} else {
    echo "User tidak ditemukan!";
}

$stmt->close();
$conn->close();
?>

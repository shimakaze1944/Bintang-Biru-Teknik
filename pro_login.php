<?php
ob_start();
session_start();
include_once("koneksi.php");

$lgn_user = $_POST['lgn_user'] ?? '';
$lgn_pass = $_POST['lgn_pass'] ?? '';

$stmt = $conn->prepare("SELECT * FROM tbl_user WHERE usr_email = ? AND usr_pass = ?");
$stmt->bind_param("ss", $lgn_user, $lgn_pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $array = $result->fetch_assoc();

    $_SESSION['sess_usr_id'] = $array['usr_id'];
    $_SESSION['sess_usr_status'] = $array['usr_status'];
    $_SESSION['sess_usr_nama'] = $array['usr_nama'];
    $_SESSION['sess_usr_jk'] = $array['usr_jk'];
    $_SESSION['sess_usr_tempat_lahir'] = $array['usr_tempat_lahir'];
    $_SESSION['sess_usr_tgl_lahir'] = $array['usr_tgl_lahir'];
    $_SESSION['sess_usr_alamat'] = $array['usr_alamat'];
    $_SESSION['sess_usr_email'] = $array['usr_email'];
    $_SESSION['sess_usr_tlp'] = $array['usr_tlp'];
    $_SESSION['sess_usr_pass'] = $array['usr_pass'];

    ob_clean();
    echo "OK"; // hanya kirim status
    exit;
} else {
    echo "Maaf, user tidak ditemukan!";
}

$stmt->close();
$conn->close();
ob_end_flush();
?>

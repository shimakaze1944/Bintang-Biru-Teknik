<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . '/db_connection.php');

$lgn_user = $_POST['lgn_user'] ?? '';
$lgn_pass = $_POST['lgn_pass'] ?? '';

if (empty($lgn_user) || empty($lgn_pass)) {
    echo "Email dan Password wajib diisi!";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM tbl_user WHERE usr_email = ? LIMIT 1");
$stmt->bind_param("s", $lgn_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($lgn_pass, trim($user['usr_pass']))) {
        $_SESSION['sess_usr_id'] = $user['usr_id'];
        $_SESSION['sess_usr_status'] = $user['usr_status'];
        $_SESSION['sess_usr_nama'] = $user['usr_nama'];
        $_SESSION['sess_usr_email'] = $user['usr_email'];

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

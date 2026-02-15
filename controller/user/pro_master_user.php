<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE)
    session_start();
include_once(__DIR__ . '/../auth/db_connection.php');

// Helper untuk validasi role
function require_role_management()
{
    if (session_status() === PHP_SESSION_NONE)
        session_start();
    $allowed = ['Admin', 'BBTeknik'];
    $role = $_SESSION['sess_usr_status'] ?? '';
    if (!in_array($role, $allowed)) {
        header("HTTP/1.1 403 Forbidden");
        echo "Akses ditolak. Hanya Admin & BB Teknik yang bisa mengakses.";
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? 'list';

// ============ GET ==============
if ($method === 'GET') {
    if ($action === 'get') {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode([]);
            exit;
        }

        $s = $conn->prepare("SELECT usr_id, usr_username, usr_nama, usr_email, usr_tlp, usr_status, vendor_id 
                             FROM tbl_user WHERE usr_id = ? LIMIT 1");
        $s->bind_param("i", $id);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($row ?: []);
        exit;
    }

    if ($action === 'delete' && isset($_GET['id'])) {
        require_role_management();
        $id = (int) $_GET['id'];
        if ($id > 0) {
            $del = $conn->prepare("DELETE FROM tbl_user WHERE usr_id = ?");
            $del->bind_param("i", $id);
            $del->execute();
        }
        header("Location: ../../home.php?cs=Master-User");
        exit;
    }
}

// ============ POST ==============
if ($method === 'POST') {
    require_role_management();

    // CREATE USER
    if ($action === 'create') {
        $username = trim($_POST['usr_username'] ?? '');
        $nama = trim($_POST['usr_nama'] ?? '');
        $email = trim($_POST['usr_email'] ?? '');
        $tlp = trim($_POST['usr_tlp'] ?? '');
        $status = $_POST['usr_status'] ?? 'Customer';
        $vendor_id = !empty($_POST['vendor_id']) ? (int) $_POST['vendor_id'] : null;
        $pass = $_POST['usr_pass'] ?? '';

        // hanya 4 field wajib
        if ($username === '' || $nama === '' || $pass === '' || $status === '') {
            echo "Data belum lengkap!";
            exit;
        }

        // cek duplikat username
        $c = $conn->prepare("SELECT usr_id FROM tbl_user WHERE usr_username = ? LIMIT 1");
        $c->bind_param("s", $username);
        $c->execute();
        if ($c->get_result()->fetch_assoc()) {
            echo "Username sudah digunakan.";
            exit;
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $i = $conn->prepare("INSERT INTO tbl_user 
            (usr_username, usr_pass, usr_nama, usr_email, usr_tlp, usr_status, vendor_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $i->bind_param("ssssssi", $username, $hash, $nama, $email, $tlp, $status, $vendor_id);

        if ($i->execute()) echo "OK";
        else echo "Gagal tambah user: " . $conn->error;
        exit;
    }

    // EDIT USER
    if ($action === 'edit') {
        $usr_id = (int) ($_POST['usr_id'] ?? 0);
        $nama = trim($_POST['usr_nama'] ?? '');
        $email = trim($_POST['usr_email'] ?? '');
        $tlp = trim($_POST['usr_tlp'] ?? '');
        $status = $_POST['usr_status'] ?? 'Customer';
        $vendor_id = !empty($_POST['vendor_id']) ? (int) $_POST['vendor_id'] : null;

        if ($usr_id <= 0) {
            echo "User tidak valid";
            exit;
        }

        $u = $conn->prepare("UPDATE tbl_user 
                             SET usr_nama=?, usr_email=?, usr_tlp=?, usr_status=?, vendor_id=? 
                             WHERE usr_id=?");
        $u->bind_param("ssssii", $nama, $email, $tlp, $status, $vendor_id, $usr_id);
        if ($u->execute())
            echo "OK";
        else
            echo "Gagal update: " . $conn->error;
        exit;
    }

    // RESET PASSWORD USER
    if ($action === 'reset_password') {
        $usr_id = (int) ($_POST['usr_id'] ?? 0);
        $new_pass = $_POST['new_pass'] ?? '';

        if ($usr_id <= 0 || $new_pass === '') {
            echo "Data tidak valid!";
            exit;
        }

        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE tbl_user SET usr_pass=? WHERE usr_id=?");
        $stmt->bind_param("si", $hash, $usr_id);
        echo $stmt->execute() ? "OK" : "Gagal reset password: " . $conn->error;
        exit;
    }
}

echo "No action.";
$conn->close();
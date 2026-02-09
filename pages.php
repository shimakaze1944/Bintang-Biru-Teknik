<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once(__DIR__ . '/controller/auth/db_connection.php');

$cs = $_GET['cs'] ?? '';

switch ($cs) {
  case 'Master-User':
    include_once(__DIR__ . '/view/master_user.php');
    break;

  case 'Master-Pekerja':
    include_once(__DIR__ . '/view/master_pekerja.php');
    break;

  case 'Master-Vendor':
    include_once(__DIR__ . '/view/master_vendor.php');
    break;

  case 'Master-Kapal':
    include_once(__DIR__ . '/view/master_kapal.php');
    break;

  case 'Master-Layanan':
    include_once(__DIR__ . '/view/master_layanan.php');
    break;

  case 'History':
    include_once(__DIR__ . '/view/history.php');
    break;

  case 'Service':
    include_once(__DIR__ . '/view/service.php');
    break;

  case 'Cashflow':
    include_once(__DIR__ . '/view/cashflow.php');
    break;

  case 'Ubah-Data':
    include_once(__DIR__ . '/view/master_user_ubahdata.php');
    break;

  case 'Ubah-Sandi':
    include_once(__DIR__ . '/view/master_user_ubahsandi.php');
    break;

  default:
    // DASHBOARD SESUAI ROLE
    $adminRoles = ['Admin', 'BBTeknik'];
    if (in_array($_SESSION['sess_usr_status'], $adminRoles)) {
        include_once(__DIR__ . '/view/dashboard_admin.php');
    } else {
        include_once(__DIR__ . '/view/dashboard_user.php');
    }
    break;

}


<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
  default:
    include_once(__DIR__ . '/view/dashboard.php');
    break;
}

echo "<div style='color:green;'>DEBUG: halaman.php aktif, cs = '$cs'</div>";

 ?>

<!--  
 <script>
 	$(document).ready(function(){
 		$('.usr_tabel').dataTable();
 	});
 </script> -->
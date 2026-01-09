<?php 
session_start();
if(empty($_SESSION['sess_usr_id'])){
  header('location:index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>PT. World Trans | Home</title>
  <link rel="shortcut icon" href="image/logo.png">

  <!-- Bootstrap & Icons -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!-- Plugin CSS -->
  <link href="vendor/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/sb-admin.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">

  <style>
    body {
      background-color: #f8f9fa;
    }
    #mainNav {
      background-color: #132A4A !important;
    }
    #mainNav .navbar-sidenav {
      background-color: #132A4A !important;
    }
    #mainNav .navbar-sidenav li a {
      color: #fff !important;
    }
    #mainNav .navbar-sidenav li a:hover {
      background-color: #0B97A4 !important;
      color: #fff;
    }
    .content-wrapper {
      background: #fff;
      padding: 20px;
      min-height: calc(100vh - 56px);
    }
    footer.sticky-footer {
      background-color: #e9ecef;
    }
    @media (max-width: 991px) {
      #mainNav .navbar-sidenav {
        position: fixed;
        top: 56px;
        left: -250px;
        width: 250px;
        height: 100%;
        transition: all 0.3s;
        z-index: 1030;
      }
      #mainNav .navbar-sidenav.active {
        left: 0;
      }
      .content-wrapper {
        margin-left: 0 !important;
        padding-top: 70px;
      }
    }
  </style>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="home.php">
      <img src="image/logo_home.png" alt="Logo" height="45">
    </a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
      data-target="#navbarResponsive">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

        <li class="nav-item active">
          <a class="nav-link" href="home.php">
            <i class="fa fa-fw fa-dashboard"></i> Dashboard
          </a>
        </li>

        <!-- Master -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#collapseMaster" data-toggle="collapse">
            <i class="fa fa-fw fa-cog"></i> Master
          </a>
          <ul class="sidenav-second-level collapse" id="collapseMaster">
            <li><a href="?cs=Master-User"><i class="fa fa-user-circle-o"></i> Master User</a></li>
            <li><a href="?cs=Master-Satuan"><i class="fa fa-cubes"></i> Master Satuan Barang</a></li>
            <li><a href="?cs=Master-Kategori"><i class="fa fa-cubes"></i> Master Kategori Barang</a></li>
            <li><a href="?cs=Master-Barang"><i class="fa fa-cubes"></i> Master Barang</a></li>
            <li><a href="?cs=Master-Mekanik"><i class="fa fa-user-circle-o"></i> Master Mekanik</a></li>
            <li><a href="?cs=Master-Supplier"><i class="fa fa-user-circle-o"></i> Master Supplier</a></li>
            <li><a href="?cs=Master-Supir"><i class="fa fa-user-circle-o"></i> Master Supir</a></li>
            <li><a href="?cs=Master-Mobil"><i class="fa fa-car"></i> Master Mobil</a></li>
            <li><a href="?cs=Master-Keterangan-Posisi"><i class="fa fa-cogs"></i> Master Keterangan Posisi</a></li>
          </ul>
        </li>

        <!-- Transaksi -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#collapseTransaksi" data-toggle="collapse">
            <i class="fa fa-fw fa-file"></i> Transaksi
          </a>
          <ul class="sidenav-second-level collapse" id="collapseTransaksi">
            <li><a href="?cs=Transaksi-Pembelian"><i class="fa fa-shopping-basket"></i> Transaksi Pembelian</a></li>
            <li><a href="?cs=Transaksi-Servis"><i class="fa fa-car"></i> Transaksi Servis Mobil</a></li>
            <li><a href="?cs=Transaksi-Klaim"><i class="fa fa-file"></i> Transaksi Klaim Bon</a></li>
          </ul>
        </li>

        <!-- Laporan -->
        <li class="nav-item">
          <a class="nav-link collapsed" href="#collapseLaporan" data-toggle="collapse">
            <i class="fa fa-fw fa-book"></i> Laporan
          </a>
          <ul class="sidenav-second-level collapse" id="collapseLaporan">
            <li><a href="?cs=Laporan-Pembelian"><i class="fa fa-book"></i> Laporan Pembelian</a></li>
            <li><a href="?cs=Laporan-Servis"><i class="fa fa-book"></i> Laporan Servis Mobil</a></li>
            <li><a href="?cs=Laporan-Persedian-Barang"><i class="fa fa-book"></i> Laporan Barang</a></li>
            <li><a href="?cs=Laporan-Klaim"><i class="fa fa-book"></i> Laporan Klaim Bon</a></li>
          </ul>
        </li>
      </ul>

      <!-- Top Nav -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle mr-lg-2" href="#" id="userDropdown" data-toggle="dropdown">
            <i class="fa fa-fw fa-user"></i> <?php echo $_SESSION['sess_usr_nama']; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="?cs=Ubah-Data"><i class="fa fa-edit text-danger"></i> Ubah Data Pribadi</a>
            <a class="dropdown-item" href="?cs=Ubah-Sandi"><i class="fa fa-key text-success"></i> Ubah Kata Sandi</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#logoutModal">
            <i class="fa fa-fw fa-sign-out"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Content -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active"><?php echo @$_GET['cs']; ?></li>
      </ol>
      <?php include_once('halaman.php'); ?>
    </div>
  </div>

  <!-- Footer -->
  <footer class="sticky-footer">
    <div class="container text-center">
      <small>Copyright &copy; PT. World Trans 2025</small>
    </div>
  </footer>

  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header"><h5>Anda Yakin Akan Keluar?</h5></div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a class="btn btn-primary" href="pro_logout.php">Logout</a>
      </div>
    </div></div>
  </div>

  <!-- JS Dependencies -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
  <script src="js/sb-admin.js"></script>

  <script>
    $(function(){
      // dropdown fix
      $('.nav-link.collapsed').on('click', function(e){
        const target = $(this).attr('href');
        $(target).collapse('toggle');
      });
    });
  </script>
</body>
</html>

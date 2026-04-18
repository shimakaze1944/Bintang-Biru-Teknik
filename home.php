<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['sess_usr_id'])) {
  header("Location: index.php");
  exit;
}

$isAdmin = in_array($_SESSION['sess_usr_status'], ['Admin', 'BBTeknik']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Bintang Biru Teknik | Home</title>
  <link rel="shortcut icon" href="image/BBTeknikLogo.png">

  <!-- Bootstrap & Icons -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!-- Plugin CSS -->
  <link href="vendor/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet">
  <link href="css/sb-admin.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">

  <style>
    #collapseMaster {
      padding-left: 12px;
      padding-right: 12px;
    }

    #collapseMaster li {
      margin-bottom: 4px;
    }


    #collapseMaster li a {
      display: block;
      padding: 10px 18px;
      text-align: left;
      font-size: 15px;
      color: #ffffffcc;
      border-radius: 6px;
    }

    #collapseMaster li a:hover {
      background-color: #0B97A4;
      color: #fff;
    }

    .content-wrapper {
      transition: padding-top 0.4s ease;
    }

    body {
      background-color: #f8f9fa;
    }

    /* Navbar */
    #mainNav {
      background-color: #132A4A !important;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1040;
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

    .navbar-sidenav .dropdown-menu {
      position: absolute;
      margin-left: 5px;
      border-radius: 8px;
      background-color: #173863;
      border: none;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
      min-width: 180px;
    }

    .navbar-sidenav .dropdown-menu .dropdown-item {
      color: #fff !important;
      font-size: 14px;
      padding: 8px 14px;
      border-radius: 6px;
    }

    .navbar-sidenav .dropdown-menu .dropdown-item:hover {
      background-color: #0B97A4 !important;
    }

    .navbar-sidenav .dropdown-toggle::after {
      margin-top: 7px;
    }

    .content-wrapper {
      background: #fff;
      padding: 100px 20px 20px 20px;
      min-height: calc(105vh - 56px);
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
        padding-top: 90px;
      }
    }
  </style>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav" style="background-color:#132A4A;">
    <div class="container-fluid">
      <!-- Brand -->
      <a class="navbar-brand" href="home.php">
        <img src="image/BBTeknikLogo.png" alt="Logo" height="45">
      </a>

      <!-- Burger / Toggle -->
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#mainNavbar"
        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu -->
      <div class="collapse navbar-collapse" id="mainNavbar">
        <!-- kiri -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php if ($isAdmin): ?>
            <li class="nav-item"><a class="nav-link" href="home.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="masterDropdown" data-toggle="dropdown"
                aria-expanded="false">
                <i class="fa fa-fw fa-database"></i> Master Data
              </a>
              <div class="dropdown-menu border-0 shadow" aria-labelledby="masterDropdown"
                style="background-color:#173863;">
                <?php if ($_SESSION['sess_usr_status'] === 'Admin'): ?>
                  <a class="dropdown-item text-white" href="?cs=Master-User"><i class="fa fa-user me-2"></i> User</a>
                <?php endif; ?>
                <a class="dropdown-item text-white" href="?cs=Master-Kapal"><i class="fa fa-ship me-2"></i> Kapal</a>
                <a class="dropdown-item text-white" href="?cs=Master-Pekerja"><i class="fa fa-building me-2"></i>
                  Teknisi</a>
                <a class="dropdown-item text-white" href="?cs=Master-Layanan"><i class="fa fa-wrench me-2"></i>
                  Layanan</a>
                <a class="dropdown-item text-white" href="?cs=Master-Vendor"><i class="fa fa-industry me-2"></i>
                  Vendor</a>
              </div>
            </li>
            <li class="nav-item"><a class="nav-link" href="?cs=Service"><i class="fa fa-fw fa-wrench"></i> Service</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="?cs=History"><i class="fa fa-fw fa-history"></i> History</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="?cs=Cashflow"><i class="fa fa-fw fa-money"></i> Cashflow</a>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="home.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="?cs=History"><i class="fa fa-fw fa-history"></i> History
                Servis</a></li>
          <?php endif; ?>
        </ul>

        <!-- spacer biar kanan kiri terpisah -->
        <div class="flex-grow-1"></div>

        <!-- kanan -->
        <ul class="navbar-nav ml-auto align-items-center">
          <li class="nav-item dropdown me-2">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-fw fa-user"></i> <?= htmlspecialchars($_SESSION['sess_usr_nama']); ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="userDropdown"
              style="background-color:#173863;">
              <a class="dropdown-item text-white" href="?cs=Ubah-Data"><i class="fa fa-edit text-danger me-2"></i> Ubah
                Data Pribadi</a>
              <a class="dropdown-item text-white" href="?cs=Ubah-Sandi"><i class="fa fa-key text-success me-2"></i> Ubah
                Kata Sandi</a>
            </div>
          </li>

          <li class="nav-item nav-logout">
            <a class="nav-link" data-toggle="modal" data-target="#logoutModal">
              <i class="fa fa-fw fa-sign-out"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>



  <!-- Content -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <?php include_once('pages.php'); ?>
    </div>
  </div>

  <!-- Footer -->
  <footer class="sticky-footer">
    <div class="container text-center">
      <small>Copyright &copy; Bintang Biru Teknik 2025</small>
    </div>
  </footer>

  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Anda Yakin Akan Keluar?</h5>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="controller/auth/logout_controller.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <script src="vendor/jquery/jquery.min.js"></script>
  <link rel="stylesheet" href="assets/select2/select2.min.css">
  <script src="assets/select2/select2.full.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
  <script src="js/sb-admin.js"></script>

  <script>
    $(function () {
      // Tambah padding-top kalo dropdown dibuka
      $('.navbar-sidenav .collapse').on('show.bs.collapse', function () {
        $('.content-wrapper').css('padding-top', '180px');
      });

      // Balikin padding-top kalau dropdown ditutup
      $('.navbar-sidenav .collapse').on('hide.bs.collapse', function () {
        $('.content-wrapper').css('padding-top', '100px');
      });

      // Toggle collapse menu
      $('.nav-link.collapsed').on('click', function (e) {
        const target = $(this).attr('href');
        $(target).collapse('toggle');
      });
    });
  </script>
  <script>
    $('.navbar-nav>li>a').on('click', function () {
      $('.navbar-collapse').collapse('hide');
    });
  </script>
</body>

</html>
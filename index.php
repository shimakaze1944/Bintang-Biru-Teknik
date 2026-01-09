<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PT. Bintang Biru Teknik | Login</title>
  <link rel="shortcut icon" type="image/x-icon" href="image/logo.png">  
  <link rel="stylesheet" href="css/logincss.css">

  <!-- Font Awesome buat ikon mata -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <script src="vendor/jquery/jquery.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <div class="container">
      <h1>PT. Bintang Biru Teknik<br><small>Login</small></h1>

      <form class="form" method="post" action="">
        <div class="form-group">
          <input type="email" placeholder="Email" name="user" id="lgn_user" maxlength="30" autocomplete="off">
        </div>

        <div class="form-group password-box">
          <input type="password" placeholder="Password" name="pass" id="lgn_pass" maxlength="20" autocomplete="off">
          <i class="fa fa-eye toggle-password" toggle="#lgn_pass"></i>
        </div>

        <button type="button" id="login-btn">Login</button>
      </form>
    </div>
  </div>

  <script>
  $(document).ready(function(){
    // toggle visibility password
    $(".toggle-password").click(function() {
      const input = $($(this).attr("toggle"));
      if (input.attr("type") === "password") {
        input.attr("type", "text");
        $(this).removeClass("fa-eye").addClass("fa-eye-slash");
      } else {
        input.attr("type", "password");
        $(this).removeClass("fa-eye-slash").addClass("fa-eye");
      }
    });

    // login click
    $("#login-btn").click(function(e){
      e.preventDefault();

      const user = $("#lgn_user").val().trim();
      const pass = $("#lgn_pass").val().trim();

      if (user === "" && pass === "") {
        alert("Email dan Password harus diisi!");
        $("#lgn_user").focus();
      } else if (user === "") {
        alert("Email tidak boleh kosong!");
        $("#lgn_user").focus();
      } else if (pass === "") {
        alert("Password tidak boleh kosong!");
        $("#lgn_pass").focus();
      } else {
        $.ajax({
          url: "pro_login.php",
          type: "POST",
          data: { lgn_user: user, lgn_pass: pass },
          cache: false,
          success: function(response) {
            if (response.trim() === "OK") {
              window.location.href = "home.php";
            } else {
              alert(response);
              $("#lgn_user").val("");
              $("#lgn_pass").val("");
            }
          },
          error: function(xhr, status, error) {
            alert("Terjadi kesalahan koneksi: " + error);
          }
        });
      }
    });

    // tekan Enter di input password = klik login
    $("#lgn_pass").keypress(function(e) {
      if (e.which == 13) {
        $("#login-btn").click();
      }
    });
  });
  </script>
</body>
</html>

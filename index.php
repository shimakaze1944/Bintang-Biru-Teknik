<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PT. Bintang Biru Teknik | Login</title>
  <link rel="shortcut icon" type="image/x-icon" href="image/logo.png">  
  <link rel="stylesheet" href="css/logincss.css">

  <!-- Font Awesome untuk ikon mata -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <!-- jQuery -->
  <script src="vendor/jquery/jquery.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <div class="container">
      <h1>PT. Bintang Biru Teknik<br><small>Login</small></h1>

      <form class="form" method="post" id="login-form">
        <div class="form-group">
          <input type="email" placeholder="Email" name="lgn_user" id="lgn_user" maxlength="50" autocomplete="off" required>
        </div>

        <div class="form-group password-box">
          <input type="password" placeholder="Password" name="lgn_pass" id="lgn_pass" maxlength="50" autocomplete="off" required>
          <i class="fa fa-eye toggle-password" toggle="#lgn_pass"></i>
        </div>

        <button type="submit" id="login-btn">Login</button>
      </form>
    </div>
  </div>

  <script>
  $(document).ready(function(){

    //Toggle visibility password
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

    //Proses login (AJAX)
    $("#login-form").on("submit", function(e){
      e.preventDefault();

      const user = $("#lgn_user").val().trim();
      const pass = $("#lgn_pass").val().trim();

      if (user === "" || pass === "") {
        alert("Email dan Password tidak boleh kosong!");
        return;
      }

      $.ajax({
        url: "controller/auth/login_controller.php",
        type: "POST",
        data: { lgn_user: user, lgn_pass: pass },
        cache: false,
        success: function(response) {
          const res = response.trim();
          if (res === "OK") {
            window.location.href = "home.php";
          } else {
            alert(res);
            $("#lgn_pass").val("");
          }
        },
        error: function(xhr, status, error) {
          alert("Terjadi kesalahan koneksi ke server:\n" + error);
        }
      });
    });

    //Enter button dipencet buat submit
    $("#lgn_pass").keypress(function(e) {
      if (e.which === 13) {
        $("#login-btn").click();
      }
    });
  });
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PT. Bintang Biru Teknik | Login</title>
  <link rel="shortcut icon" type="image/x-icon" href="image/logo.png">  
  <link rel="stylesheet" href="css/logincss.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="vendor/jquery/jquery.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <div class="container">
      <h1>PT. Bintang Biru Teknik<br><small>Login</small></h1>

      <form id="login-form" method="post">
        <div class="form-group">
          <input type="text" placeholder="Username" name="lgn_user" id="lgn_user" maxlength="50" autocomplete="off" required>
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
    // toggle password visibility
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

    // proses login
    $("#login-form").on("submit", function(e){
      e.preventDefault();
      const user = $("#lgn_user").val().trim();
      const pass = $("#lgn_pass").val().trim();

      if (user === "" || pass === "") {
        alert("Username dan Password harus diisi!");
        return;
      }

      $.ajax({
        url: "controller/auth/login_controller.php",
        type: "POST",
        data: { lgn_user: user, lgn_pass: pass },
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
  });
  </script>
</body>
</html>

<?php
session_start();
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';

if (!empty($message)) {
  echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = " . json_encode($message) . ";
                Swal.fire({
                    //title: 'ADVERTENCIA',
                    text: message,
                    icon: 'warning',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
  unset($_SESSION['message']);
}

if (isset($_POST['username']) && isset($_POST['password'])) {
  $username = mysqli_real_escape_string($con, $_POST['username']);
  $password = mysqli_real_escape_string($con, $_POST['password']);

  $query = "SELECT * FROM usuarios WHERE username='$username'";
  $result = mysqli_query($con, $query);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $hashed_password = $row['password'];

    if (password_verify($password, $hashed_password)) {
      $_SESSION['username'] = $username;
      $_SESSION['rol'] = $row['rol'];
      $_SESSION['message'] = "Bienvenido";
      header("Location: dashboard.php");
      exit();
    } else {
      $_SESSION['message'] = "La contraseña es incorrecta";
      header("Location: index.php");
      exit();
    }
  } else {
    $_SESSION['message'] = "El correo ingresado no existe, realiza tu registro.";
    header("Location: index.php");
    exit();
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/whats.css">
  <link rel="stylesheet" href="css/whats2.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico" />
  <title>Ingresar | Fastpack</title>
</head>

<body style="background-image: url(https://news.fastpack.mx/wp-content/uploads/2024/03/woman-safety-equipment-work-scaled.jpg);background-size: cover;">
  <?php include('componentes/menu.php'); ?>
  <?php include 'whatsapp.php'; ?>

  <div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="margin-top: 140px;margin-bottom:70px;">
      <div class="col-11 col-md-4 text-center bg-light p-5" style="border-radius: 10px;">
        <h2><b>INGRESAR</b></h2>
        <p style="margin-bottom:15px;">Escribe tus credenciales</p>
        <form action="" method="post" class="mb-3">
          <div class="form-floating mb-3">
            <input type="username" class="form-control" id="floatingInput" name="username" placeholder="Correo" autocomplete="off" required>
            <label style="margin-left: 0px;" for="floatingInput">Correo</label>
          </div>
          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="floatingInput" name="password" placeholder="Password" autocomplete="off" required>
            <label style="margin-left: 0px;" for="floatingInput">Contraseña</label>
          </div>
          <button type="submit" class="btn p-2" style="width: 100%;background-color:#1e375c;color:#fff;">Ingresar</button>
        </form>
        <a style="font-size:12px;text-decoration:none;color:#5c5c5c;" href="restablecer.php">¿Has olvidado tu contraseña?</a>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
  <script src="js/menu2.js"></script>

</body>

</html>
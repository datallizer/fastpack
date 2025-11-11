<?php
session_start();
require 'dbcon.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['delete'])) {
    $registro_id = mysqli_real_escape_string($con, $_POST['delete']);

    $query = "DELETE FROM usuarios WHERE id='$registro_id' ";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Usuario eliminado exitosamente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error al eliminar el usuario, contacte a soporte";
        header("Location: usuarios.php");
        exit(0);
    }
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidopaterno = mysqli_real_escape_string($con, $_POST['apellidopaterno']);
    $apellidomaterno = mysqli_real_escape_string($con, $_POST['apellidomaterno']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $estatus = mysqli_real_escape_string($con, $_POST['estatus']);
    // Obtener la nueva imagen cargada
    if ($_FILES['nuevaFoto']['size'] > 0) {
        $medio = file_get_contents($_FILES['nuevaFoto']['tmp_name']);
        $medio = mysqli_real_escape_string($con, $medio);

        // Actualizar la imagen en la base de datos
        $update_query = "UPDATE usuarios SET medio='$medio' WHERE id='$id'";
        $update_result = mysqli_query($con, $update_query);

        if ($update_result) {
            $query = "UPDATE `usuarios` SET `nombre` = '$nombre', `apellidopaterno` = '$apellidopaterno', `apellidomaterno` = '$apellidomaterno', `username` = '$username', `rol` = '$rol', `estatus` = '$estatus' WHERE `usuarios`.`id` = '$id'";
            $query_run = mysqli_query($con, $query);

            if ($query_run) {
                $idcodigo = $_SESSION['codigo'];
                $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
                $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
                $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un usuario, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol, estatus: $estatus', hora='$hora_actual', fecha='$fecha_actual'";
                $query_rundos = mysqli_query($con, $querydos);
                $_SESSION['message'] = "Usuario editado exitosamente";
                header("Location: usuarios.php");
                exit(0);
            } else {
                $_SESSION['message'] = "Error al editar el usuario, contácte a soporte";
                header("Location: usuarios.php");
                exit(0);
            }
        } else {
            $_SESSION['message'] = "Error al actualizar la imagen del usuario, contácte a soporte";
            header("Location: usuarios.php");
            exit(0);
        }
    } else {
        $query = "UPDATE `usuarios` SET `nombre` = '$nombre', `apellidop` = '$apellidopaterno', `apellidom` = '$apellidom', `codigo` = '$codigo', `rol` = '$rol', `estatus` = '$estatus' WHERE `usuarios`.`id` = '$id'";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d"); // Obtener fecha actual en formato Año-Mes-Día
            $hora_actual = date("H:i"); // Obtener hora actual en formato Hora:Minutos:Segundos
            $querydos = "INSERT INTO historial SET idcodigo='$idcodigo', detalles='Edito un usuario, nombre: $nombre $apellidop $apellidom, codigo: $codigo, rol: $rol, estatus: $estatus', hora='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $_SESSION['message'] = "Usuario editado exitosamente";
            header("Location: usuarios.php");
            exit(0);
        } else {
            $_SESSION['message'] = "Error al editar el usuario, contácte a soporte";
            header("Location: usuarios.php");
            exit(0);
        }
    }
}

if (isset($_POST['save'])) {
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $apellidopaterno = mysqli_real_escape_string($con, $_POST['apellidopaterno']);
    $apellidomaterno = mysqli_real_escape_string($con, $_POST['apellidomaterno']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $rol = mysqli_real_escape_string($con, $_POST['rol']);
    $estatus = "1";

    // Verificar el rol y asignar el nombre correspondiente
    if ($rol == 1) {
        $rol_nombre = "Administrador";
    } elseif ($rol == 2) {
        $rol_nombre = "Colaborador";
    } else {
        $rol_nombre = "Otro"; // Por si acaso el rol no es 1 ni 2
    }

    $check_email_query = "SELECT * FROM usuarios WHERE username='$username' LIMIT 1";
    $result = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = "El correo ingresado ya esta en uso, inicia sesión o registrate con un correo diferente";
        header("Location: usuarios.php");
        exit(0);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO usuarios SET nombre='$nombre', apellidopaterno='$apellidopaterno', apellidomaterno='$apellidomaterno', username='$username', password='$hashed_password', rol='$rol', estatus='$estatus'";

        $query_run = mysqli_query($con, $query);
        if ($query_run) {
            $asunto = 'Solicitud para colaborar';
            $cuerpo = '
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body style="font-family: system-ui;text-align: justify;background-color: #e7e7e7;">
                    <div style="max-width:500px;margin: 0 auto;">
                        <img style="width: 100%;background-color: #1e375c;" src="https://news.fastpack.mx/wp-content/uploads/2022/09/fastpack-ok-blanco-1.png" alt="Cintillo superior">
                    <div style="padding: 0px 30px;padding-top: 35px;">
                        <p>Estimado/a ' . $nombre . '</p>
                        <p>Tu cuenta para gestionar el catálogo de productos y servicios de Fastpack se creo exitosamente.</p>
                        <p>Por seguridad no compartas tus credenciales con nadie.</p>

                        <div style="padding: 3px 20px;background-color:#efefef;color:#000000;border-radius: 3px;margin: 50px 0px;text-align:left;">
                        <p style="margin-bottom: 0px;"><b>Conoce los detalles de tu cuenta:</b></p>
                        <div style="display: flex; flex-direction: column; margin: 0 auto;">
                            <div style="display: flex; flex-wrap: wrap;">
                                <p style="margin-right: 5px;margin-bottom: 0px;"><b>Nombre:</b></p>
                                <p style="flex: 2;margin-bottom: 0px;">' . $nombre . ' ' . $apellidopaterno . ' ' . $apellidomaterno . '</p>
                            </div>
                        </div>
                        
                        <p><b>Correo:</b> ' . $username . '</p>
                        <p><b>Rol solicitado:</b> ' . $rol_nombre . '</p>
                        </div>

                        <p style="text-align: center;margin-top:80px;margin-bottom:0px;">Atentamente</p>
                        <p style="text-align: center;margin-top:0px;margin-bottom:50px;"><b>Equipo administrativo</b></p>
                    </div>
                    <div style="background-color: #af3335;color: #ffffff;padding: 15px 15px;font-size: 10px;text-align: center;padding-bottom: 15px;margin-bottom: 25px;">
                        <p>Este correo es enviado de manera automática por nuestro sistema de respuesta rápida.</p>
                    </div>
                    </div>
                </body>
                
                </html>';

            $mail = new PHPMailer(true);

            try {
                //Configuraciones del servidor
                $mail->isSMTP();
                $mail->Host = 'mail.fastpack.mx'; // Especifica tu servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'fastpack'; // SMTP username
                $mail->Password = 'Jcasarin22.'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                //Configuración del correo
                $mail->setFrom('noreply@fastpack.mx', 'FASTPACK.MX');
                $mail->addAddress($username, $nombre . ' ' . $apellidopaterno); // Añade al destinatario
                $mail->addReplyTo('system@fastpack.mx', 'FASTPACK.MX');

                // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = $asunto;
                $mail->Body    = $cuerpo;

                // Enviar correo
                if ($mail->send()) {
                    $_SESSION['message'] = "Se envió tu solicitud exitosamente, revisa tu correo";
                    header("Location: usuarios.php");
                    exit(0);
                } else {
                    $_SESSION['message'] = "Tu solicitud se envío a nuestro equipo, parece que hubo un error al hacerte llegar la información a tu correo";
                    header("Location: usuarios.php");
                    exit(0);
                }
            } catch (Exception $e) {
                $_SESSION['message'] = "Error al enviar el correo: {$mail->ErrorInfo}";
                header("Location: usuarios.php");
                exit(0);
            }
        } else {
            $_SESSION['message'] = "Error, intenta más tarde";
            header("Location: usuarios.php");
            exit(0);
        }
    }
}

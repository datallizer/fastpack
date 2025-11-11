<?php
session_start();
require 'dbcon.php';

header("Content-Type: text/html; charset=UTF-8");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
  <title>Productos | Fastpack</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
</head>
<style>
  .category_list {
    position: sticky;
    top: 100px;
    /* Ajusta esto según la altura de tu menú fijo o encabezado */
    height: calc(100%);
    /* Altura completa de la ventana menos el espacio de arriba */
    overflow-y: auto;
    /* Agrega scroll interno si la lista de categorías es más alta que la ventana */
    padding-right: 10px;
    /* Espacio entre la lista y el contenido principal */
  }
</style>

<body style="background-color: #f5f5f5;">
  <?php include 'componentes/menu.php'; ?>
  <?php include 'whatsapp.php'; ?>
  <div class="container-fluid">
    <div class="row mb-5 mt-5 justify-content-start" style="margin-top: 100px !important;padding:0px 50px;">
      <div class="col-12 col-md-2 mt-3 category_list">

        <div class="form-floating mt-1 mb-3">
          <input type="text" id="searchInput" class="form-control mb-3" placeholder="Buscar producto...">
          <label style="padding-left: 0px;" for="floatingInput">Buscar producto...</label>
        </div>

        <p class="mb-1"><small>Catálogo:</small></p>
        <form action="generarcatalogo.php" method="post">
          <button type="submit" name="generar" style="background-color:#1e375c;color:#fff;" class="btn mb-3">Descargar PDF</button>
          <p class="small">Selecciona filtros de industrias o categorías si deseas generar un PDF de productos específicos.</p>

          <!-- Filtros de Categorías -->
          <p class="mb-0"><small>Categorías:</small></p>
          <label>
            <input type="checkbox" name="category[]" class="category_item" value="all">
            Todo
          </label><br>
          <?php

          $query = "SELECT * FROM categorias ORDER BY id DESC";
          $query_run = mysqli_query($con, $query);
          if (mysqli_num_rows($query_run) > 0) {
            foreach ($query_run as $registro) {
          ?>
              <label>
                <input type="checkbox" name="category[]" class="category_item" value="<?= $registro['categoria']; ?>">
                <?= $registro['categoria']; ?>
              </label><br>
          <?php
            }
          } else {
            echo "<div style='min-height:70vh;text-align:center;'><p>No se encontró ninguna categoría</p></div>";
          }
          ?>

          <!-- Filtros de Industrias -->
          <p class="mt-3 mb-0"><small>Industrias:</small></p>
          <?php
          $query = "SELECT * FROM industrias ORDER BY id DESC";
          $query_run = mysqli_query($con, $query);
          if (mysqli_num_rows($query_run) > 0) {
            foreach ($query_run as $registro) {
          ?>
              <label>
                <input type="checkbox" name="industry[]" class="industry_item" value="<?= htmlspecialchars($registro['industria'], ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars($registro['industria'], ENT_QUOTES, 'UTF-8'); ?>
              </label><br>
          <?php
            }
          } else {
            echo "<div style='min-height:70vh;text-align:center;'><p>No se encontró ninguna industria</p></div>";
          }
          ?>
        </form>
      </div>



      <div class=" col-12 col-md-10 card-contain">
        <div class="row justify-content-start" id="productList">
          <?php
          $query = "SELECT p.id AS productoID, 
           p.titulo, 
           p.subtitulo, 
           GROUP_CONCAT(DISTINCT c.categoria ORDER BY c.categoria ASC SEPARATOR ', ') AS categorias,
           GROUP_CONCAT(DISTINCT i.industria ORDER BY i.industria ASC SEPARATOR ', ') AS industrias,
           (SELECT medio FROM medios WHERE idproducto = p.id ORDER BY id LIMIT 1) AS primer_medio
    FROM productos p
    LEFT JOIN categoriasasociadas c ON p.id = c.idproducto
    LEFT JOIN industriaasociada i ON p.id = i.idproducto
    GROUP BY p.id
    ORDER BY p.id DESC;
";

          $query_run = mysqli_query($con, $query);
          if (mysqli_num_rows($query_run) > 0) {
            foreach ($query_run as $registro) {
          ?>
              <div class="col-12 col-md-3 mt-3 product-item" industry="<?= htmlspecialchars($registro['industrias'], ENT_QUOTES, 'UTF-8'); ?>" category="<?= htmlspecialchars($registro['categorias'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="card img-card-container" style="width: 100%;">
                  <a style="text-decoration: none; color: #000;" href="verproducto.php?id=<?= $registro['productoID']; ?>">
                    <?php if ($registro['primer_medio']) { ?>
                      <img src="<?= $registro['primer_medio']; ?>" class="card-img-top" alt="...">
                    <?php } else { ?>
                      <img src="images/ico.ico" class="card-img-top" alt="Default Image">
                    <?php } ?>
                    <div class="card-body">
                      <div style="min-height: 120px;">
                        <h5 style="text-transform: uppercase; font-weight: 600;" class="card-title"><?= htmlspecialchars($registro['titulo'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <pre style="font-size: 12px; margin-bottom: 0px;" class="card-text"><?= htmlspecialchars($registro['subtitulo'], ENT_QUOTES, 'UTF-8'); ?></pre>
                      </div>
                      <p style="font-size: 10px; margin-left: 5px; margin-bottom: 0px;"><b>Categoría:</b> <?= htmlspecialchars($registro['categorias'], ENT_QUOTES, 'UTF-8'); ?></p>
                      <p style="font-size: 10px; margin-left: 5px; margin-top: 0px;min-height:30px;"><b>Industria:</b> <?= htmlspecialchars($registro['industrias'], ENT_QUOTES, 'UTF-8'); ?></p>
                      <div class="d-flex">
                        <!-- Botón de WhatsApp -->
                        <a style="background-color: #1e375c; color: #fff;" href="verproducto.php?id=<?= $registro['productoID']; ?>" target="_blank" rel="noopener noreferrer" class="btn w-100 me-2">
                          <small><i class="bi bi-info-circle"></i> Ver más</small>
                        </a>
                        <!-- Botón de Compartir -->
                        <button onclick="copyLink('<?= $registro['productoID']; ?>')" style="background-color: #007bff; color: #fff;" class="btn w-10">
                          <small><i class="bi bi-share"></i></small>
                        </button>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
          <?php
            }
          } else {
            echo "<div style='min-height: 70vh;display: flex;justify-content: center;align-items: center;text-align: center;'><p>No se encontró ningún producto</p></div>";
          }
          ?>

        </div>
      </div>

    </div>
  </div>
  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/filtros.js"></script>
  <script>
    function copyLink(productoID) {
      // URL del producto
      var url = "https://fastpack.mx/productos/verproducto.php?id=" + productoID;
      console.log(url);

      // Crear un elemento de entrada temporal para copiar la URL
      var tempInput = document.createElement("input");
      tempInput.value = url;
      document.body.appendChild(tempInput);

      // Seleccionar y copiar el texto
      tempInput.select();
      document.execCommand("copy");

      // Eliminar el elemento temporal
      document.body.removeChild(tempInput);

      // Mostrar alerta con SweetAlert2
      Swal.fire({
        icon: 'success',
        title: '¡Enlace copiado!',
        text: 'El enlace del producto ha sido copiado al portapapeles.',
        confirmButtonText: 'OK'
      });
    }

    document.getElementById('searchInput').addEventListener('keyup', function() {
      let filter = this.value.toUpperCase();
      let products = document.getElementById('productList').getElementsByClassName('product-item');

      for (let i = 0; i < products.length; i++) {
        let title = products[i].getElementsByClassName('card-title')[0].innerText;
        let subtitle = products[i].getElementsByClassName('card-text')[0].innerText;
        if (title.toUpperCase().indexOf(filter) > -1 || subtitle.toUpperCase().indexOf(filter) > -1) {
          products[i].style.display = '';
        } else {
          products[i].style.display = 'none';
        }
      }
    });
  </script>
</body>

</html>
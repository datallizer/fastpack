<?php
session_start();
require 'dbcon.php';

if (isset($_GET['id'])) {
    $registro_id = mysqli_real_escape_string($con, $_GET['id']);

    $query = "SELECT p.id, 
                 p.titulo, 
                 p.subtitulo, p.detalles, p.preciounitario, p.descuento,
                 GROUP_CONCAT(DISTINCT c.categoria ORDER BY c.categoria ASC SEPARATOR ', ') AS categorias,
                 GROUP_CONCAT(DISTINCT i.industria ORDER BY i.industria ASC SEPARATOR ', ') AS industrias,
                 (SELECT medio FROM mediosventa WHERE idproducto = p.id ORDER BY id LIMIT 1) AS primer_medio
          FROM productosventa p
          LEFT JOIN categoriasasociadasventa c ON p.id = c.idproducto
          LEFT JOIN industriaasociadaventa i ON p.id = i.idproducto
          WHERE p.id = '$registro_id'";

    $query_run = mysqli_query($con, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $registro = mysqli_fetch_array($query_run);

        // Asigna la primera imagen a la variable $medio_base64
        $medio_base64 = $registro['primer_medio'];

        // Verifica si el medio_base64 es una imagen JPG
        $extension = pathinfo($medio_base64, PATHINFO_EXTENSION);
        if (strtolower($extension) === 'jpg' || strtolower($extension) === 'jpeg') {
            $og_image = "https://fastpack.mx/productosventa/" . $medio_base64;
        } else {
            // Manejar el caso en que no sea JPG
            $og_image = "https://fastpack.mx/productos/ico.ico"; // o cualquier otra lógica que necesites
        }

?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="<?= $registro['subtitulo']; ?>">
            <meta name="keywords" content="cintas,cajas,adhesivos,industria,insumos,chalecos,seguridad">
            <meta name="author" content="Fastpack">
            <meta property="og:title" content="<?= $registro['titulo']; ?> | Fastpack">
            <meta property="og:description" content="<?= $registro['subtitulo']; ?>">
            <meta property="og:url" content="https://fastpack.mx/ver-producto.php?id=<?= $registro_id; ?>">
            <meta property="og:image" content="<?= $og_image; ?>">
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta property="og:image" content="images/ics.ico">
            <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">.
            <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
            <link rel="stylesheet" href="css/styles.css">
            <title><?= $registro['titulo']; ?> | Fastpack</title>
            <link rel="shortcut icon" href="images/ico.ico" type="image/x-icon">
        </head>

        <body style="background-color: #f5f5f5;">
            <?php include 'componentes/menu.php'; ?>

            <div class="container-fluid">
                <div class="row justify-content-evenly align-items-top" style="margin-top: 120px;">

                    <div class="col-12 col-md-3">
                        <a href="<?php echo $medio_base64; ?>" data-fslightbox="gallery<?php echo $registro_id; ?>">
                            <img style="width: 100%;max-height:300px;object-fit:cover;" src="<?php echo $medio_base64; ?>" class="card-img-top" alt="...">
                        </a>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="slickcard">
                                    <?php
                                    // Consulta para obtener los medios del producto
                                    $query_medios = "SELECT id, medio FROM mediosventa WHERE idproducto = $registro_id";
                                    $result_medios = mysqli_query($con, $query_medios);
                                    $medios = [];
                                    $pdf_encontrado = false; // Variable para verificar si se encuentra un PDF

                                    while ($row = mysqli_fetch_assoc($result_medios)) {
                                        $medios[] = $row;
                                    }
                                    $total_medios = count($medios);
                                    foreach ($medios as $medio) {
                                        $ruta_medio = $medio['medio'];
                                        $extension = strtolower(pathinfo($ruta_medio, PATHINFO_EXTENSION));

                                        // Verificar si es un archivo PDF y activar el botón
                                        if ($extension === 'pdf') {
                                            $pdf_encontrado = true;
                                            $ruta_pdf = $ruta_medio; // Guarda la ruta del PDF
                                            continue; // Ignorar el PDF para la visualización como imagen o video
                                        }

                                        echo "<div class='slickc'>";

                                        // Verificar si el medio es un video
                                        if ($extension === 'mp4') {
                                            echo "<a href='$ruta_medio' data-fslightbox='gallery$registro_id' data-type='video'>
                    <video style='width: 100%; height: 110px; object-fit: cover;'>
                        <source src='$ruta_medio' type='video/mp4'>
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                  </a>";
                                        } else {
                                            if ($total_medios > 1) {
                                                // Si no es video, se muestra como imagen
                                                echo "<a href='$ruta_medio' data-fslightbox='gallery$registro_id'>
                                            <img src='$ruta_medio' style='width: 100%; height: 200px; object-fit: cover;' alt='...'>
                                          </a>";
                                            }
                                        }

                                        echo "</div>";
                                    }


                                    ?>



                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div style="min-height: 260px;">
                            <h2 style="text-transform: uppercase;"><?= $registro['titulo']; ?>
                            </h2>
                            <p style="font-weight: 500;"><?= $registro['subtitulo']; ?></p>
                            <pre style="font-size: 11px;min-height:110px;"><?= $registro['detalles']; ?></pre>

                            <p style="font-size: 10px; margin-left: 5px; margin-bottom: 0px;"><b>Categoría:</b> <?= $registro['categorias']; ?></p>
                            <p style="font-size: 10px; margin-left: 5px; margin-top: 0px;"><b>Industria:</b> <?= $registro['industrias']; ?></p>
                        </div>

                        <p style="margin-bottom: 30px !important;">$<?= htmlspecialchars($registro['preciounitario'] - $registro['descuento'], ENT_QUOTES, 'UTF-8'); ?> <?php if ($registro['descuento'] > 0): ?>
                                <span style="font-size:13px;color: #39ad19ff;text-decoration: line-through;"><b>$<?= htmlspecialchars($registro['preciounitario'], ENT_QUOTES, 'UTF-8'); ?></b></span>
                            <?php endif; ?>
                        </p>

                        <div class="d-flex align-items-center">
                            <!-- Botón de WhatsApp -->
                            <a style="background-color:#1e375c;color:#fff;" href="https://wa.me/524493523433?text=Hola,%20quiero%20información%20sobre:%0A*Producto:*%20<?= $registro['titulo']; ?>%0A%20<?= $registro['subtitulo']; ?>%0A%20https://fastpack.mx/ver-producto.php?id=<?= $registro_id; ?>" target="_blank" rel="noopener noreferrer" class="btn w-100 me-2">
                                <small><i class="bi bi-whatsapp"></i> Preguntar</small>
                            </a>
                            <?php

                            // Si se encontró un PDF, mostrar el botón de descarga
                            if ($pdf_encontrado) {
                                echo "<a href='$ruta_pdf' class='btn btn-warning btn-sm fichaTec me-2 p-2' download><i class='bi bi-download'></i> Ficha</a>";
                            }
                            ?>
                            <!-- Botón de Compartir -->
                            <button onclick="copyLink()" style="background-color:#007bff;color:#fff;" class="btn w-10"><small><i class="bi bi-share"></i></small></button>
                        </div>
                        <!-- Botón de aadir carrito -->
                        <div class="d-flex align-items-center mt-2">
                            <button onclick="addCart('<?= $registro_id; ?>')"
                                style="background-color: #c93434ff; color: #fff;"
                                class="btn w-100">
                                <small><i class="bi bi-cart2"></i> Añadir al carrito</small>
                            </button>

                            <div id="counter-<?= $registro_id; ?>" class="ms-2 align-items-center" style="display: none;">
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('<?= $registro_id; ?>', -1)">−</button>
                                <span id="qty-<?= $registro_id; ?>" class="mx-2">0</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('<?= $registro_id; ?>', 1)">+</button>
                            </div>
                        </div>


                    </div>
            <?php
        } else {
            echo "<h4>No se encontro el producto solicitado</h4>";
        }
    }
            ?>
                </div>

                <div class="row mt-5 justify-content-start p-5" style="margin-top: 100px !important;background-color: #e7e7e7;">
                    <div class="col-12">
                        <h3>PRODUCTOS SIMILARES</h3>
                    </div>

                    <?php
                    $query = "SELECT p.id, 
           p.titulo, 
           p.subtitulo, p.preciounitario, p.descuento,
           GROUP_CONCAT(DISTINCT c.categoria ORDER BY c.categoria ASC SEPARATOR ', ') AS categorias,
           GROUP_CONCAT(DISTINCT i.industria ORDER BY i.industria ASC SEPARATOR ', ') AS industrias,
           (SELECT medio FROM mediosventa WHERE idproducto = p.id ORDER BY id LIMIT 1) AS primer_medio
    FROM productosventa p
    LEFT JOIN categoriasasociadasventa c ON p.id = c.idproducto
    LEFT JOIN industriaasociadaventa i ON p.id = i.idproducto WHERE p.id <> $registro_id
    GROUP BY p.id
    ORDER BY p.id DESC LIMIT 4;
";

                    $query_run = mysqli_query($con, $query);
                    if (mysqli_num_rows($query_run) > 0) {
                        foreach ($query_run as $registro) {
                    ?>
                            <div class="col-12 col-md-3 mt-3 product-item" industry="<?= $registro['industrias']; ?>" category="<?= $registro['categorias']; ?>">
                                <div class="card img-card-container" style="width: 100%;">
                                    <a style="text-decoration: none; color: #000;" href="ver-producto.php?id=<?= $registro['id']; ?>">
                                        <?php if ($registro['primer_medio']) { ?>
                                            <img src="<?= $registro['primer_medio']; ?>" class="card-img-top" alt="...">
                                        <?php } else { ?>
                                            <img src="images/ico.ico" class="card-img-top" alt="Default Image">
                                        <?php } ?>
                                        <div class="card-body">
                                            <div style="min-height: 50px;">
                                                <h5 style="text-transform: uppercase; font-weight: 600;" class="card-title"><?= $registro['titulo']; ?></h5>
                                                <!-- <pre style="font-size: 12px; margin-bottom: 0px;" class="card-text"><?= $registro['subtitulo']; ?></pre> -->
                                            </div>
                                            <p style="font-size: 10px; margin-left: 5px; margin-bottom: 0px;"><b>Categoría:</b> <?= $registro['categorias']; ?></p>
                                            <p style="font-size: 10px; margin-left: 5px; margin-top: 0px;"><b>Industria:</b> <?= $registro['industrias']; ?></p>

                                            <p style="margin-bottom: 0px !important;">$<?= htmlspecialchars($registro['preciounitario'] - $registro['descuento'], ENT_QUOTES, 'UTF-8'); ?> <?php if ($registro['descuento'] > 0): ?>
                                                    <span style="font-size:13px;color: #39ad19ff;text-decoration: line-through;"><b>$<?= htmlspecialchars($registro['preciounitario'], ENT_QUOTES, 'UTF-8'); ?></b></span>
                                                <?php endif; ?>
                                            </p>
                                            <!-- Botón de aadir carrito -->
                                            <div class="d-flex align-items-center mt-2">
                                                <button onclick="addCart('<?= $registro['id']; ?>')"
                                                    style="background-color: #c93434ff; color: #fff;"
                                                    class="btn w-100">
                                                    <small><i class="bi bi-cart2"></i> Añadir al carrito</small>
                                                </button>

                                                <div id="counter-<?= $registro['id']; ?>" class="ms-2 align-items-center" style="display: none;">
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('<?= $registro['id']; ?>', -1)">−</button>
                                                    <span id="qty-<?= $registro['id']; ?>" class="mx-2">0</span>
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('<?= $registro['id']; ?>', 1)">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div style='min-height: 70vh; text-align: left;'><p>No se encontró ningún producto similar</p></div>";
                    }
                    ?>

                </div>
            </div>

            <div class="floating-button" id="cartButton" style="display: none;">
                <a href="carrito-de-compras.php">
                    <span style="background-color: #fff; color: #213443; padding: 5px 5px 5px 7px; border-radius: 50px; margin-right: 10px;">
                        <i class="bi bi-cart"></i>
                        <span id="cartCount" style="font-weight: bold; margin-left: 4px;"></span>
                    </span>
                    Carrito de compras
                </a>
            </div>

            <?php include 'footer.php'; ?>
            <?php include 'whatsapp.php'; ?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
            <script src="js/fslightbox.js"></script>
            <script src="js/slickslider.js"></script>
            <script>
                function copyLink() {
                    // URL del producto
                    var url = "https://fastpack.mx/ver-producto.php?id=<?= $registro_id; ?>";

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

                document.addEventListener("DOMContentLoaded", () => {
                    const cartButton = document.getElementById("cartButton");
                    const cartCount = document.getElementById("cartCount");

                    // Funciones base
                    function getCart() {
                        return JSON.parse(localStorage.getItem("fastpackCart")) || [];
                    }

                    function saveCart(cart) {
                        localStorage.setItem("fastpackCart", JSON.stringify(cart));
                        // 🔥 Dispara evento personalizado para que el botón se actualice en tiempo real
                        window.dispatchEvent(new Event("cartUpdated"));
                    }

                    // Función para mostrar/ocultar el botón del carrito
                    function actualizarBotonCarrito() {
                        const cart = getCart();
                        if (Array.isArray(cart) && cart.length > 0) {
                            cartButton.style.display = "block";
                            cartCount.textContent = cart.length; // Muestra longitud del arreglo
                        } else {
                            cartButton.style.display = "none";
                        }
                    }

                    // 🔁 Actualiza botón al cargar
                    actualizarBotonCarrito();

                    // 🔥 Escucha cuando el carrito cambie (evento personalizado)
                    window.addEventListener("cartUpdated", actualizarBotonCarrito);

                    // También escucha cambios de otras pestañas
                    window.addEventListener("storage", function(e) {
                        if (e.key === "fastpackCart") actualizarBotonCarrito();
                    });

                    // ---- Funciones de carrito ----
                    function addCart(id) {
                        let cart = getCart();
                        let existing = cart.find(item => item.id === id);
                        if (existing) {
                            existing.cantidad++;
                        } else {
                            cart.push({
                                id: id,
                                cantidad: 1
                            });
                        }
                        saveCart(cart);
                        updateQuantityDisplay(id);
                    }

                    function changeQuantity(id, change) {
                        let cart = getCart();
                        let existing = cart.find(item => item.id === id);

                        if (existing) {
                            existing.cantidad += change;
                            if (existing.cantidad <= 0) {
                                cart = cart.filter(item => item.id !== id);
                            }
                        } else if (change > 0) {
                            cart.push({
                                id: id,
                                cantidad: 1
                            });
                        }

                        saveCart(cart);
                        updateQuantityDisplay(id);
                    }

                    function updateQuantityDisplay(id) {
                        const cart = getCart();
                        const item = cart.find(i => i.id === id);
                        const qtySpan = document.getElementById(`qty-${id}`);
                        const counterDiv = document.getElementById(`counter-${id}`);

                        if (!qtySpan || !counterDiv) return;

                        if (item && item.cantidad > 0) {
                            qtySpan.textContent = item.cantidad;
                            counterDiv.style.display = "flex";
                        } else {
                            qtySpan.textContent = 0;
                            counterDiv.style.display = "none";
                        }
                    }

                    // 🔁 Inicializa cantidades visibles
                    getCart().forEach(item => updateQuantityDisplay(item.id));

                    // Exponer funciones al ámbito global (para onclick del HTML)
                    window.addCart = addCart;
                    window.changeQuantity = changeQuantity;
                });
            </script>
        </body>

        </html>
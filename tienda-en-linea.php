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
    <title>Tienda en línea | Fastpack</title>
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
            </div>



            <div class=" col-12 col-md-10 card-contain">
                <div class="row justify-content-start" id="productList">
                    <?php
                    $query = "SELECT p.id AS productoID, 
           p.titulo, 
           p.subtitulo, p.preciounitario, p.descuento, 
           GROUP_CONCAT(DISTINCT c.categoria ORDER BY c.categoria ASC SEPARATOR ', ') AS categorias,
           GROUP_CONCAT(DISTINCT i.industria ORDER BY i.industria ASC SEPARATOR ', ') AS industrias,
           (SELECT medio FROM mediosventa WHERE idproducto = p.id ORDER BY id LIMIT 1) AS primer_medio
    FROM productosventa p
    LEFT JOIN categoriasasociadasventa c ON p.id = c.idproducto
    LEFT JOIN industriaasociadaventa i ON p.id = i.idproducto
    GROUP BY p.id
    ORDER BY p.id DESC;
";

                    $query_run = mysqli_query($con, $query);
                    if (mysqli_num_rows($query_run) > 0) {
                        foreach ($query_run as $registro) {
                    ?>
                            <div class="col-12 col-md-3 mt-3 product-item" industry="<?= htmlspecialchars($registro['industrias'], ENT_QUOTES, 'UTF-8'); ?>" category="<?= htmlspecialchars($registro['categorias'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="card img-card-container" style="width: 100%;">
                                    <a style="text-decoration: none; color: #000;" href="ver-producto.php?id=<?= $registro['productoID']; ?>">
                                        <?php if ($registro['primer_medio']) { ?>
                                            <img src="<?= $registro['primer_medio']; ?>" class="card-img-top" alt="...">
                                        <?php } else { ?>
                                            <img src="images/ico.ico" class="card-img-top" alt="Default Image">
                                        <?php } ?>
                                        <div class="card-body" style="padding-bottom: 0px !important;">
                                            <div>
                                                <h5 style="text-transform: uppercase; font-weight: 600;min-height:50px" class="card-title"><?= htmlspecialchars($registro['titulo'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                                <!-- <pre style="font-size: 12px; margin-bottom: 0px;" class="card-text"><?= htmlspecialchars($registro['subtitulo'], ENT_QUOTES, 'UTF-8'); ?></pre> -->
                                            </div>
                                            <p style="font-size: 10px; margin-left: 5px; margin-bottom: 0px;"><b>Categoría:</b> <?= htmlspecialchars($registro['categorias'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p style="font-size: 10px; margin-left: 5px; margin-top: 0px;min-height:30px;"><b>Industria:</b> <?= htmlspecialchars($registro['industrias'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p style="margin-bottom: 0px !important;">$<?= htmlspecialchars($registro['preciounitario'] - $registro['descuento'], ENT_QUOTES, 'UTF-8'); ?> <?php if ($registro['descuento'] > 0): ?>
                                                    <span style="font-size:13px;color: #39ad19ff;text-decoration: line-through;"><b>$<?= htmlspecialchars($registro['preciounitario'], ENT_QUOTES, 'UTF-8'); ?></b></span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </a>
                                    <!-- Botón de aadir carrito -->
                                    <div class="d-flex align-items-center p-3">
                                        <button onclick="addCart('<?= $registro['productoID']; ?>')"
                                            class="btn btn-danger w-100">
                                            <small><i class="bi bi-cart2"></i> Añadir</small>
                                        </button>

                                        <div id="counter-<?= $registro['productoID']; ?>" class="ms-2 align-items-center" style="display: none;">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('<?= $registro['productoID']; ?>', -1)">−</button>
                                            <span id="qty-<?= $registro['productoID']; ?>" class="mx-2">0</span>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('<?= $registro['productoID']; ?>', 1)">+</button>
                                        </div>
                                    </div>
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

    <div class="floating-button" id="cartButton" style="display: none;">
        <a href="carrito-de-compras.php">
            <span style="background-color: #fff; color: #213443; padding: 5px 5px 5px 7px; border-radius: 50px; margin-right: 10px;">
                <i class="bi bi-cart"></i>
                <span id="cartCount" style="font-weight: bold; margin-left: 4px;"></span>
            </span>
            Carrito de compras
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/filtros.js"></script>
    <script>
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
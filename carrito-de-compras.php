<?php
session_start();
require 'dbcon.php';

header("Content-Type: text/html; charset=UTF-8");

$consultaEnvio = $con->query("SELECT valoruno, valordos FROM configuraciones WHERE nombre='Envio' LIMIT 1");
$env = $consultaEnvio->fetch_assoc();

$envioMinimo = $env['valoruno']; // Mínimo para envío gratis
$envioCosto = $env['valordos'];  // Costo de envío

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

<body style="background-color: #f5f5f5;">
    <?php include 'componentes/menu.php'; ?>
    <?php include 'whatsapp.php'; ?>
    <div class="container-fluid">
        <div class="row mb-5 mt-5 justify-content-evenly" style="margin-top: 100px !important;padding:0px 50px;">

            <div class="col-6 bg-light p-4">
                <h2>CARRITO DE COMPRAS</h2>
                <p><b>Resumen de compra</b></p>
                <p>Total de productos: <span id="totalProductos">0</span></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Producto</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="detalleCompra"></tbody>
                </table>
                <p>Subtotal: <span id="subtotal">$ 0.00</span></p>
                <p>Descuento: <span id="descuento">$ 0.00</span></p>
                <p>Cupón: <span id="cupon">$ 0.00</span></p>
                <p id="enviop">Costo de envío: <span id="envio">$ 0.00</span></p>
                <p>Total a pagar: <b><span id="totalPagar">$ 0.00</span></b></p>


                <!-- <button class="btn btn-secondary w-100 mt-5" disabled>Guardar carrito de compras</button> -->
                <button class="btn btn-danger w-100 mt-4">Pagar</button>

                <div class="mt-3">Tengo un cupón:
                    <div class="d-flex mt-1">
                        <input class="form-control ms-2" type="text" id="codigoCupon">
                        <button style="border-radius: 0px 10px 10px 0px;"
                            class="btn btn-secondary" id="canje">Canjear</button>
                    </div>
                </div>


                <div class="p-3 mt-3" id="envioCosto" style="background-color: #25456c2d;border:2px solid #25456c66;border-radius:10px">
                    <p class="text-dark" style="margin:0;"><small><i style="background-color: #25456c3b;color: #393939ff;padding:5px 5px 5px 10px;border-radius:50px;" class="bi bi-truck"></i> Para <b>envíos gratis</b> se requiere un <b>minimo de compra</b> de <b>$<?= number_format($envioMinimo) ?></b>.</small></p>
                </div>
                <div class="p-3 mt-3" id="envioGratis" style="background-color: #256c2a2d;border:2px solid #336c2566;border-radius:10px">
                    <p class="text-dark" style="margin:0;"><small><i style="background-color: #256c273b;color: #393939ff;padding:5px 5px 5px 10px;border-radius:50px;" class="bi bi-truck"></i> El <b>envío</b> de tus productos es <b>gratis</b>.</small></p>
                </div>
                <div class="p-3 mt-3" style="background-color: #ebbc5d78;border:2px solid #b5790066;border-radius:10px">
                    <p class="text-dark" style="margin:0;"><small><i style="background-color: #b692133b;color: #393939ff;padding:5px 5px 5px 10px;border-radius:50px;" class="bi bi-cash-coin"></i> El <b>pago es procesado</b> mediante <b>Openpay por BBVA</b> con maximos estandares de <b>seguridad y tecnología antifraude</b>, dentro de nuestro sitio web nunca te solicitaremos información bancaria.</small></p>
                </div>
            </div>
            <div class=" col-12 col-md-4 card-contain">
                <div class="row justify-content-start" id="productList">
                    <p>Cargando productos del carrito...</p>
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
        let cuponDescuento = 0;
        let bloqueandoCupon = false;
        let alertaMostrada = false;
        const ENVIO_MINIMO = <?= $envioMinimo ?>;
        const ENVIO_COSTO = <?= $envioCosto ?>;

        function getCart() {
            return JSON.parse(localStorage.getItem("fastpackCart")) || [];
        }

        function saveCart(cart) {
            localStorage.setItem("fastpackCart", JSON.stringify(cart));
        }

        // Agregar producto
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
            updateTotals();
        }

        // Cambiar cantidad
        function changeQuantity(id, change) {
            let cart = getCart();
            let existing = cart.find(item => item.id === id);

            if (existing) {
                existing.cantidad += change;
                if (existing.cantidad <= 0) {
                    cart = cart.filter(item => item.id !== id);
                    const card = document.getElementById(`card-${id}`);
                    if (card) card.remove();
                }
                saveCart(cart);
            } else if (change > 0) {
                cart.push({
                    id: id,
                    cantidad: 1
                });
                saveCart(cart);
            }
            updateQuantityDisplay(id);
            updateTotals();
        }

        // Actualiza cantidad visual
        function updateQuantityDisplay(id) {
            const cart = getCart();
            const item = cart.find(i => i.id === id);
            const qtySpan = document.getElementById(`qty-${id}`);
            if (qtySpan) qtySpan.textContent = item ? item.cantidad : 0;
        }

        function updateTotals() {
            const cart = getCart();
            let totalProductos = cart.length;
            let subtotal = 0;
            let totalDescuento = 0;
            let costoEnvioAplicado = 0;

            cart.forEach(item => {
                const precio = parseFloat(document.getElementById(`price-${item.id}`)?.dataset.precio || 0);
                const descuento = parseFloat(document.getElementById(`price-${item.id}`)?.dataset.descuento || 0);
                subtotal += precio * item.cantidad;
                totalDescuento += descuento * item.cantidad;
            });

            if (subtotal - totalDescuento - cuponDescuento < ENVIO_MINIMO) {
                // SE COBRA ENVÍO
                costoEnvioAplicado = ENVIO_COSTO;

                document.getElementById("envioCosto").style.display = "block";
                document.getElementById("envioGratis").style.display = "none";
                document.getElementById("enviop").style.display = "block";
                document.getElementById("envio").style.display = "inline";

                document.getElementById("envio").textContent = `$ ${ENVIO_COSTO.toFixed(2)}`;
            } else {
                // ES GRATIS
                costoEnvioAplicado = 0;

                document.getElementById("envioCosto").style.display = "none";
                document.getElementById("envioGratis").style.display = "block";
                document.getElementById("enviop").style.display = "none";
                document.getElementById("envio").style.display = "none";
            }

            // ⬅ Aquí ya se integra el cupón guardado globalmente
            let totalFinal = subtotal - totalDescuento - cuponDescuento + costoEnvioAplicado;


            document.getElementById("totalProductos").textContent = totalProductos;
            document.getElementById("subtotal").textContent = `$ ${subtotal.toFixed(2)}`;
            document.getElementById("descuento").textContent = `$ ${totalDescuento.toFixed(2)}`;
            document.getElementById("cupon").textContent = `$ ${cuponDescuento.toFixed(2)}`;
            document.getElementById("totalPagar").textContent = `$ ${totalFinal.toFixed(2)}`;

            // Tabla
            let detalle = "";
            cart.forEach(item => {
                const precio = parseFloat(document.getElementById(`price-${item.id}`)?.dataset.precio || 0);
                const titulo = document.getElementById(`title-${item.id}`)?.textContent || "";
                detalle += `
        <tr>
            <td>${item.cantidad}</td>
            <td>${titulo}</td>
            <td>$ ${(precio * item.cantidad).toFixed(2)}</td>
        </tr>`;
            });
            document.getElementById("detalleCompra").innerHTML = detalle;
            if (!bloqueandoCupon && cuponDescuento > 0) {
                reevaluarCupon();
            }
        }


        // Cargar productos del carrito
        document.addEventListener("DOMContentLoaded", () => {
            const cart = getCart();
            const ids = cart.map(item => item.id);

            if (ids.length === 0) {
                document.getElementById("productList").innerHTML = `
        <div style='min-height: 70vh; display: flex; justify-content: center; align-items: center; text-align: center;'>
            <div><p>No tienes productos en el carrito</p>
            <a href='tienda-en-linea.php' class='btn btn-secondary'>Tienda en línea</a></div>
        </div>`;
                updateTotals();
                return;
            }

            $.post("get_cart_products.php", {
                ids
            }, function(data) {
                if (!data || data.length === 0) {
                    $("#productList").html("<p>No se encontraron productos.</p>");
                    // Si no hay datos válidos, limpiar el carrito por completo
                    saveCart([]);
                    updateTotals();
                    return;
                }

                // 🔹 Validar que todos los IDs existan en la respuesta
                const validIDs = data.map(prod => String(prod.productoID));
                let cart = getCart();
                const filteredCart = cart.filter(item => validIDs.includes(String(item.id)));

                // Si se eliminaron productos inexistentes, actualizar el localStorage y mostrar alerta
                if (filteredCart.length !== cart.length) {
                    saveCart(filteredCart);
                    Swal.fire({
                        icon: 'info',
                        title: 'Productos actualizados',
                        text: 'Algunos productos ya no están disponibles y fueron eliminados de tu carrito.',
                        confirmButtonColor: '#c93434',
                        confirmButtonText: 'Entendido'
                    });
                }

                let html = "";
                data.forEach(prod => {
                    html += `
        <div class="col-12 mt-3" id="card-${prod.productoID}">
            <div class="card" style="width: 100%;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div style="height: 160px; overflow: hidden;">
                            <a href="ver-producto.php?id=${prod.productoID}">
                                <img src="${prod.primer_medio || 'images/ico.ico'}"
                                    class="img-fluid rounded-start"
                                    alt="${prod.titulo}"
                                    style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                            </a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 id="title-${prod.productoID}" class="card-title" style="font-size:15px;text-transform: uppercase; font-weight: 600;">
                                ${prod.titulo}
                            </h5>
                            <div class="ms-2 align-items-center">
                                <p id="price-${prod.productoID}" 
                                    data-precio="${prod.preciounitario}" 
                                    data-descuento="${prod.descuento}">
                                    Precio: $ ${parseFloat(prod.preciounitario).toFixed(2)} <br>
                                    Descuento: $ ${parseFloat(prod.descuento).toFixed(2)}
                                </p>
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('${prod.productoID}', -1)">−</button>
                                <span id="qty-${prod.productoID}" class="mx-2">0</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('${prod.productoID}', 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
                });

                $("#productList").html(html);

                filteredCart.forEach(item => updateQuantityDisplay(item.id));
                updateTotals();
            }, "json");
        });

        $("#canje").on("click", function(e) {
            e.preventDefault();

            let codigo = $("#codigoCupon").val().trim().toUpperCase();
            let total = parseFloat($("#subtotal").text().replace("$", "").trim());
            let descuento = parseFloat($("#descuento").text().replace("$", "").trim());
            let subtotal = total - descuento;

            if (codigo === "") {
                Swal.fire("Código vacío", "Escribe un cupón para continuar", "warning");
                return;
            }

            $.ajax({
                url: "validar_cupon.php",
                type: "POST",
                data: {
                    codigo,
                    subtotal
                },
                dataType: "json",
                success: function(res) {

                    if (!res.ok) {
                        Swal.fire("Cupón no válido", res.msg, "error");
                        return;
                    }

                    // Guardar el descuento global
                    cuponDescuento = res.descuento;

                    Swal.fire("¡Cupón aplicado!",
                        `Se aplicó un descuento de $${cuponDescuento.toFixed(2)}.`,
                        "success"
                    );

                    // Recalcular todo correctamente
                    updateTotals();
                }
            });
        });

        function reevaluarCupon() {
            if (bloqueandoCupon) return;
            bloqueandoCupon = true;

            let codigo = $("#codigoCupon").val().trim().toUpperCase();
            if (codigo === "") {
                bloqueandoCupon = false;
                return;
            }

            let total = parseFloat($("#subtotal").text().replace("$", "").trim());
            let descuento = parseFloat($("#descuento").text().replace("$", "").trim());
            let subtotal = total - descuento;

            $.ajax({
                url: "validar_cupon.php",
                type: "POST",
                data: {
                    codigo,
                    subtotal
                },
                dataType: "json",
                success: function(res) {

                    if (!res.ok) {
                        if (alertaMostrada) return; // evita alert infinito
                        alertaMostrada = true;

                        cuponDescuento = 0;
                        $("#cupon").text("$ 0.00");

                        Swal.fire("Cupón inválido", res.msg, "warning").then(() => {
                            cuponDescuento = 0;
                            $("#cupon").text("$ 0.00");

                            // Liberar bloqueo sólo después del update
                            setTimeout(() => {
                                alertaMostrada = false;
                                bloqueandoCupon = false;

                                // No volver a reevaluar porque el cupón ya se eliminó
                                updateTotals();
                            }, 80);
                        });


                    } else {
                        cuponDescuento = res.descuento;
                        bloqueandoCupon = false;
                        updateTotals();
                    }
                }
            });
        }
    </script>
</body>

</html>
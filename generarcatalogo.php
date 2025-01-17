<?php
require 'dbcon.php'; // Incluye el archivo de conexión

if (isset($_POST['generar'])) {
    // Construir filtros dinámicos
    $filters = [];

    // Recoger filtros de categorías
    if (isset($_POST['category']) && $_POST['category'][0] !== 'all') {
        $categories = $_POST['category'];
        $categories = array_map(function ($cat) use ($con) {
            return "'" . mysqli_real_escape_string($con, $cat) . "'";
        }, $categories);
        $categories_str = implode(", ", $categories);
        $filters[] = "c.categoria IN ($categories_str)";
    }

    // Recoger filtros de industrias
    if (isset($_POST['industry']) && !empty($_POST['industry'])) {
        $industries = $_POST['industry'];
        $industries = array_map(function ($ind) use ($con) {
            return "'" . mysqli_real_escape_string($con, $ind) . "'";
        }, $industries);
        $industries_str = implode(", ", $industries);
        $filters[] = "i.industria IN ($industries_str)";
    }

    // Construir consulta SQL
    $query = "SELECT p.id, p.titulo, p.subtitulo, p.detalles, m.medio, 
           GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias,
           GROUP_CONCAT(DISTINCT i.industria SEPARATOR ', ') AS industrias
    FROM productos p
    LEFT JOIN (
        SELECT idproducto, medio
        FROM medios
        WHERE id IN (
            SELECT MIN(id) 
            FROM medios 
            GROUP BY idproducto
        )
    ) m ON p.id = m.idproducto
    LEFT JOIN categoriasasociadas c ON p.id = c.idproducto
    LEFT JOIN industriaasociada i ON p.id = i.idproducto";

    // Añadir filtros dinámicos
    if (!empty($filters)) {
        $filters_str = implode(" AND ", $filters);
        $query .= " WHERE " . $filters_str;
    }

    $query .= " GROUP BY p.id ORDER BY p.id DESC";

    // Ejecutar consulta
    $result = mysqli_query($con, $query);


    // Verificar si se obtuvieron resultados
    if ($result && mysqli_num_rows($result) > 0) {
        $products = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $products[$id] = [
                'titulo' => $row['titulo'],
                'subtitulo' => $row['subtitulo'],
                'detalles' => $row['detalles'],
                'medio' => $row['medio'],
                'categoria' => $row['categorias'],
                'industria' => $row['industrias']
            ];
        }

        echo '
           <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <style>
        @page {
            size: letter;
            margin: 0;
        }
        .page {
            page-break-after: always;
            position: relative;
            width: 100%;
            min-height: 1950px;
            padding-top: 380px;
        }
        .cintillo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            z-index: 1;
        }
        .inferior {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: auto;
            z-index: 1;
        }
        .page-content {
            position: relative;
            z-index: 0;
            width: 80%;
            margin: 0 auto;
        }
        .card {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div id="content">
        <div class="container-fluid g-0">
            <div id="pdf-content">
                ';

        $cardCount = 0;
        $maxCardsPerPage = 3;

        foreach ($products as $product) {
            if ($cardCount % $maxCardsPerPage == 0) {
                if ($cardCount > 0) {
                    echo '</div></div>'; // Cierra el div de la página anterior
                }
                echo '<div class="page">
                    <img class="cintillo" src="images/pdfcintillotop.jpg" alt="PDF Cintillo">
                    <img class="inferior" src="images/cintilloinferiorcatalogo.png" alt="PDF Cintillo inferior">
                    <div class="page-content">';

                $maxLength = 500;
                $details = htmlspecialchars($product['detalles']);

                // Verifica si el texto necesita ser truncado
                if (strlen($details) > $maxLength) {
                    $details = substr($details, 0, $maxLength) . '...';
                }
            }

            echo '
            <div class="card" style="border:0px !important;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img style="width: 100%; height: 370px; object-fit: cover;" src="data:image/jpeg;base64,' . base64_encode($product['medio']) . '" class="img-fluid rounded-start" alt="...">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h2 style="text-transform:uppercase;font-weight:300;" class="card-title"><b>' . htmlspecialchars($product['titulo']) . '</b></h2>
                            <p class="card-text">' . htmlspecialchars($product['subtitulo']) . '</p>
                            <p style="min-height:170px" class="card-text">'. $details . '</p>
                            <p style="margin-bottom:0px;color:#c4c4c4;" class="card-text"><small class="text-body-secondary">Categoría: ' . htmlspecialchars($product['categoria']) . '</small></p>
                            <p style="margin-bottom:0px;color:#c4c4c4;" class="card-text"><small class="text-body-secondary">Industria: ' . htmlspecialchars($product['industria']) . '</small></p>
                        </div>
                    </div>
                </div>
            </div>';

            $cardCount++;
        }

        if ($cardCount > 0) {
            echo '</div></div>'; // Cierra el div de la última página
        }

        echo '
            </div>
        </div>
    </div>
</body>
</html>
';

        echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js' integrity='sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js'></script>
<script>
    window.onload = function() {
        var { jsPDF } = window.jspdf;
        var pdf = new jsPDF('p', 'pt', 'letter');
        var pages = document.querySelectorAll('.page');

        function capturePage(index) {
            if (index >= pages.length) {
                pdf.save('CatalogoProductosFastpack.pdf');
                setTimeout(function() {
                    window.location.href = 'index.php'; // Redirige después de guardar el PDF
                }, 1000); // Retraso de 1 segundo para asegurar que el archivo se guarda antes de redirigir
                return;
            }

            var page = pages[index];
            html2canvas(page, {scrollY: -window.scrollY}).then(function(canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pdfWidth = pdf.internal.pageSize.getWidth();
                var pdfHeight = pdf.internal.pageSize.getHeight();

                var imgWidth = pdfWidth;
                var imgHeight = (canvas.height * imgWidth) / canvas.width;

                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);

                if (index < pages.length - 1) {
                    pdf.addPage();
                }

                capturePage(index + 1);
            });
        }

        capturePage(0);
    };
</script>
</body>
            </html>";
    }
}

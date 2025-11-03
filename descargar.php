<?php
if (isset($_GET['file'])) {
    $file_path = $_GET['file']; // Obtiene la ruta del archivo desde la URL

    // Verifica que el archivo exista
    if (file_exists($file_path)) {
        // Configura los encabezados para la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Limpia el búfer de salida y lee el archivo
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        echo "El archivo no existe.";

        header("Location: catalogosyexperiencias.php");
    }
} else {
    echo "No se especificó un archivo para descargar.";

    header("Location: catalogosyexperiencias.php");
}

<?php
// Se establecen las diferentes formas de extraer la imágen. Se prioriza leer directamente del directorio local, pero existe la opción de buscar directamente en la base de datos en caso de pérdida.

require 'db_config.php';

// 1. Validamos que nos pasen el nombre de la foto
if (empty($_GET['nom'])) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

$nombre_fichero = basename($_GET['nom']); // Limpiamos por seguridad
$ruta_disco = 'uploads/' . $nombre_fichero;

// --- ESTRATEGIA A: Intentar leer del DISCO (Rápido) ---
if (file_exists($ruta_disco)) {
    // Averiguamos si es jpg, png, etc. automáticamente
    $tipo = mime_content_type($ruta_disco);
    header("Content-Type: $tipo");
    readfile($ruta_disco); // Enviamos la imagen directa al navegador
    exit();
}

// --- ESTRATEGIA B: Si no está en disco, leer de la BBDD (Rescate) ---
// Conectamos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Error conexión DB"); }

// Buscamos el BLOB
$stmt = $conn->prepare("SELECT imagen_datos FROM posts WHERE imagen = ?");
$stmt->bind_param("s", $nombre_fichero);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($datos_binarios);

if ($stmt->fetch() && !empty($datos_binarios)) {
    // ¡La tenemos en la base de datos!
    header("Content-Type: image/jpeg"); // Asumimos JPEG por defecto
    echo $datos_binarios;
} else {
    // No está ni en disco ni en BBDD -> Error 404 real
    header("HTTP/1.0 404 Not Found");
}

$stmt->close();
$conn->close();
?>
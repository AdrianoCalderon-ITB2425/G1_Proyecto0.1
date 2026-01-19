<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Cargamos configuración segura
require_once 'db_config.php';

// Verificamos si nos envían un post
if (!empty($_POST["post"])) {
    $photoid = NULL; 
    
    // Si envían foto, generamos ID y la movemos
    if(!empty($_FILES['photo']['name'])){
        $photoid = uniqid();
        // Mueve la imagen a la carpeta uploads
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photoid)) {
            die("Error al guardar la imagen en la carpeta uploads. Revisa permisos.");
        }
    }
    
    // 2. Conexión a BBDD usando variables importadas
    $db = new mysqli($servername, $username, $password, $dbname);
    
    // Verificamos conexión
    if ($db->connect_error) {
        die("Conexión fallida: " . $db->connect_error);
    }

    $stmt = $db->prepare("INSERT INTO posts(post, photourl) VALUES(?,?)");
    $stmt->bind_param("ss", $_POST["post"], $photoid);
    
    if (!$stmt->execute()) {
        die("Error al insertar en BBDD: " . $stmt->error);
    }
    
    $stmt->close();
    $db->close();
}

// Redirigimos al index
header("location: index.php");
?>
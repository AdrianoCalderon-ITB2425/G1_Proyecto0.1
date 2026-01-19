<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'db_config.php';


if (!empty($_POST["post"])) {
    $photoid = NULL; 
    

    if(!empty($_FILES['photo']['name'])){
        $photoid = uniqid();

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photoid)) {
            die("Error al guardar la imagen en la carpeta uploads. Revisa permisos.");
        }
    }
    

    $db = new mysqli($servername, $username, $password, $dbname);
    

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


header("location: index.php");
?>
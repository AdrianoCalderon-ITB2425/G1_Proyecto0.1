<?php
// Archivo que ejecuta S4 para subir las fotos al pulsar "subir"

require_once 'db_config.php';

// Verificamos si nos envían el formulario
if (!empty($_POST["post"])) {
    $photoid = NULL;
    $imagenBinaria = NULL; // Variable para el BLOB

    // 1. PROCESAR ARCHIVO (Subida a Disco)
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoid = uniqid();
        $rutaDestino = 'uploads/' . $photoid;
        
        // Mover archivo a la carpeta física
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $rutaDestino)) {
            // EXITO: Leemos el contenido binario para la BBDD
            $imagenBinaria = file_get_contents($rutaDestino);
        } else {
            // Si falla moverlo, cortamos
            die("Error al guardar en disco. Revisa permisos de carpeta uploads.");
        }
    }
    
    // 2. CONEXIÓN BBDD
    $db = new mysqli($servername, $username, $password, $dbname);
    if ($db->connect_error) {
        die("Conexión fallida: " . $db->connect_error);
    }

    // 3. INSERTAR EN BBDD (Texto + Nombre Archivo + BLOB Binario)
    // Usamos 'sss' porque PHP trata el binario como string en bind_param
    $stmt = $db->prepare("INSERT INTO posts(texto, imagen, imagen_datos) VALUES(?,?,?)");
    $stmt->bind_param("sss", $_POST["post"], $photoid, $imagenBinaria);
    
    if (!$stmt->execute()) {
        // Si falla la BBDD (ej: imagen muy grande para max_allowed_packet) mostramos error
        die("Error al guardar en BBDD: " . $stmt->error);
    }
    
    $stmt->close();
    $db->close();
}

// 4. REDIRECCIÓN (Vuelta al inicio)
// Esto es lo que hace que no veas mensajes y vuelva a la web
header("location: index.php");
exit();
?>

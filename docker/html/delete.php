<?php
// delete.php

// 1. Cargamos configuración segura
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    
    // 2. Conexión usando las variables importadas
    $db = new mysqli($servername, $username, $password, $dbname);
    
    // Verificación de conexión
    if ($db->connect_error) {
        die("Error de conexión: " . $db->connect_error);
    }
    
    // 3. Primero obtenemos el nombre de la foto para borrar el archivo físico
    // CAMBIO: 'photourl' -> 'imagen'
    $stmt = $db->prepare("SELECT imagen FROM posts WHERE id = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Si hay foto, la borramos de la carpeta uploads
        // CAMBIO: Usamos la clave 'imagen' que es la que devuelve la BD
        if (!empty($row['imagen'])) {
            $file_path = "uploads/" . $row['imagen'];
            if (file_exists($file_path)) {
                unlink($file_path); // Esto borra el archivo del disco compartido
            }
        }
    }
    $stmt->close();

    // 4. Ahora borramos el registro de la base de datos
    $stmt_del = $db->prepare("DELETE FROM posts WHERE id = ?");
    $stmt_del->bind_param("i", $_POST['id']);
    $stmt_del->execute();
    $stmt_del->close();
    
    $db->close();
}

// Volver al inicio
header("Location: index.php");
exit;
?>

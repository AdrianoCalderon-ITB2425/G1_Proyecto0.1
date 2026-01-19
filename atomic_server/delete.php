<?php

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    
  
    $db = new mysqli($servername, $username, $password, $dbname);
    

    if ($db->connect_error) {
        die("Error de conexión: " . $db->connect_error);
    }

    $stmt = $db->prepare("SELECT photourl FROM posts WHERE id = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {

        if (!empty($row['photourl'])) {
            $file_path = "uploads/" . $row['photourl'];
            if (file_exists($file_path)) {
                unlink($file_path); 
            }
        }
    }
    $stmt->close();

    $stmt_del = $db->prepare("DELETE FROM posts WHERE id = ?");
    $stmt_del->bind_param("i", $_POST['id']);
    $stmt_del->execute();
    $stmt_del->close();
    
    $db->close();
}


header("Location: index.php");
exit;
?>


<?php

// Llamada de S2 Y S3 a Extraer_BBDD para obtener las imágenes subidas

require_once 'db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Conexión fallida: " . $conn->connect_error); }

// Hardening: seleccionamos columnas específicas en lugar de *
$sql = "SELECT id, imagen, texto, fecha FROM posts ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        $timestamp = strtotime($row["fecha"]) + 3600; 
        $fecha_reloj_espania = date("d-m-Y H:i:s", $timestamp);
        
        // Sanitizamos el nombre de la imagen para el HTML
        $img_name = htmlspecialchars($row["imagen"], ENT_QUOTES, 'UTF-8');

        echo '<div class="post" style="display: flex !important; flex-direction: column !important; width: 100% !important; max-width: 600px; margin: 0 auto 20px auto; background: #fff; border: 1px solid #dbdbdb; border-radius: 3px;">';
        
        // 1. CABECERA
        echo '  <div class="post-header" style="display: flex !important; justify-content: space-between !important; align-items: center; width: 100%; padding: 10px; box-sizing: border-box; border-bottom: 1px solid #efefef;">';
        echo '      <div style="display:flex; align-items:center;">';
        echo '          <div style="width: 32px; height: 32px; background: #eee; border-radius: 50%; margin-right: 10px;"></div>';
        echo '          <span style="font-weight: bold; font-size: 14px; color: #262626;">Usuario Anónimo</span>';
        echo '      </div>';
        
        echo '      <form action="delete.php" method="POST" style="margin:0;">';
        echo '          <input type="hidden" name="id" value="'.(int)$row["id"].'">'; // Cast a int por seguridad
        echo '          <button type="submit" style="background-color: #dc3545; color: white; border: none; border-radius: 4px; padding: 5px 10px; cursor: pointer; font-size: 1rem;">🗑️</button>';
        echo '      </form>';
        echo '  </div>';
        
        // 2. IMAGEN (LÓGICA S5 + RESCATE BBDD)
        echo '  <div style="width: 100%; background-color: #fafafa;">';
        /* EXPLICACIÓN PARA EL PROFESOR:
           1. src="/uploads/..." -> El Proxy S1 intentará pedir la foto al servidor S5 (Red VPN).
           2. onerror -> Si S5 no tiene la foto o está caído, el navegador ejecuta este JS 
              y llama a Extraer_BBDD.php para sacar el BLOB de la Base de Datos (S7).
        */
        echo '      <img src="/uploads/' . $img_name . '" 
                     onerror="this.src=\'Extraer_BBDD.php?id=' . $img_name . '\';" 
                     style="width: 100%; height: auto; display: block;" 
                     alt="Imagen">';
        echo '  </div>';
        
        // 3. PIE DE FOTO
        echo '  <div class="post-content" style="padding: 10px; text-align: left;">';
        echo '      <p style="margin: 5px 0;"><strong>Usuari_Generic_ITB:</strong> ' . htmlspecialchars($row["texto"]) . '</p>';
        echo '      <p style="color: #8e8e8e; font-size: 11px; margin-top: 5px;">' . $fecha_reloj_espania . '</p>';
        echo '  </div>';
        
        echo '</div>'; 
    }
} else {
    echo '<p style="text-align:center; color:#888;">No hay publicaciones.</p>';
}
$conn->close();
?>

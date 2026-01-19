<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Extagram</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-content">
            <a href="index.php" class="brand-logo">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="insta-gradient" x1="0%" y1="100%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#f09433;stop-opacity:1" />
                            <stop offset="25%" style="stop-color:#e6683c;stop-opacity:1" />
                            <stop offset="50%" style="stop-color:#dc2743;stop-opacity:1" />
                            <stop offset="75%" style="stop-color:#cc2366;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#bc1888;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect x="2" y="4" width="20" height="16" rx="4" ry="4" stroke="url(#insta-gradient)" stroke-width="2"/>
                    <circle cx="12" cy="12" r="4" stroke="url(#insta-gradient)" stroke-width="2"/>
                    <circle cx="18" cy="7.5" r="1" fill="url(#insta-gradient)"/>
                </svg>
                <span class="brand-text">Extagram</span>
            </a>
        </div>
    </nav>

    <form class="upload-form" method="POST" enctype="multipart/form-data" action="upload.php">
        <input type="text" name="post" placeholder="Write something..." required>
        
        <input id="file" type="file" name="photo" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(event.target.files[0])">
        
        <label for="file">
            <img id="preview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 24 24' fill='none' stroke='%23b0b0b0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/%3E%3Cpolyline points='17 8 12 3 7 8'/%3E%3Cline x1='12' y1='3' x2='12' y2='15'/%3E%3C/svg%3E" alt="Click to upload image" style="display: block; margin: 20px auto;">
        </label>
        
        <input type="submit" value="Publish">
    </form>

    <?php

    require_once 'db_config.php';


    $db = new mysqli($servername, $username, $password, $dbname);

    if ($db->connect_error) {
        die("<div style='text-align:center; color:red; margin-top:20px'>Error de conexión: " . $db->connect_error . "</div>");
    }

    $sql = "SELECT * FROM posts ORDER BY id DESC";
    $result = $db->query($sql);

    if ($result) {
        while($fila = $result->fetch_assoc()) {
            

            echo "<div class='post'>";
            

                echo "<div class='post-content'>";
                    

                    echo "<p>".htmlspecialchars($fila['post'])."</p>";
                    

                    if (!empty($fila['photourl'])) {
 
                        echo "<img src='uploads/".$fila['photourl']."' alt='Foto subida'>";
                    }
                
                echo "</div>"; 


                echo "<div class='post-sidebar'>";
                    

                    echo "<form method='POST' action='delete.php' onsubmit='return confirm(\"¿Estás seguro de querer borrar esta publicación?\");' style='width:100%; height:100%; margin:0; padding:0; border:none;'>";
                        echo "<input type='hidden' name='id' value='".$fila['id']."'>";
                        
                        echo "<button type='submit' class='btn-delete' title='Eliminar publicación'>";

                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 6v18h18v-18h-18zm5 14c0 .552-.448 1-1 1s-1-.448-1-1v-10c0-.552.448-1 1-1s1 .448 1 1v10zm5 0c0 .552-.448 1-1 1s-1-.448-1-1v-10c0-.552.448-1 1-1s1 .448 1 1v10zm5 0c0 .552-.448 1-1 1s-1-.448-1-1v-10c0-.552.448-1 1-1s1 .448 1 1v10zm4-18v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711z"/></svg>';
                        echo "</button>";
                    echo "</form>";

                echo "</div>"; 

            echo "</div>"; 
        }
    } else {
        echo "<p style='text-align:center; color:#888'>No hay publicaciones todavía.</p>";
    }

    $db->close();
    ?>

</body>
</html>
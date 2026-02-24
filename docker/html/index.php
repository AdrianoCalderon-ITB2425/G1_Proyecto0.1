<!DOCTYPE html>
// Página principal del servicio. Aquí se verán las imágenes subidas a Extagram
<html lang="es">
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<head>
    <meta charset="UTF-8">
    <title>Extagram-G1</title>
    <link rel="stylesheet" href="style.css">
        
    <style>
        /* Estilo para cuando el botón está deshabilitado */
        input[type="submit"]:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>
</head>
<body>
		<?php echo "Hola! t'aten el server amb IP: " . $_SERVER['SERVER_ADDR']; ?>
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

    <form id="uploadForm" class="upload-form" method="POST" enctype="multipart/form-data" action="upload.php" onsubmit="return manejarSubida()">
        <input type="text" name="post" placeholder="Escribe algo..." required>
        
        <input id="file" type="file" name="photo" accept="image/*" onchange="document.getElementById('preview').src=window.URL.createObjectURL(event.target.files[0])">
        
        <label for="file">
            <img id="preview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 24 24' fill='none' stroke='%23b0b0b0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/%3E%3Cpolyline points='17 8 12 3 7 8'/%3E%3Cline x1='12' y1='3' x2='12' y2='15'/%3E%3C/svg%3E" alt="Click to upload" style="display: block; margin: 20px auto; cursor: pointer;">
        </label>
        
        <input type="submit" id="submitBtn" value="Publicar">
    </form>

    <hr style="border: 0; border-top: 1px solid #dbdbdb; margin: 20px auto; max-width: 600px;">

    <div id="muro-container">
        <p style="text-align:center; color:#888">Cargando publicaciones...</p>
    </div>

    <script>
        // 1. FUNCIÓN PARA EVITAR MULTIPLES CLICS EN SUBIDA
        function manejarSubida() {
            const btn = document.getElementById('submitBtn');
            btn.value = 'Subiendo...';
            btn.disabled = true; // Bloquea el botón inmediatamente
            return true; // Permite que el formulario se envíe
        }

        // 2. FUNCIÓN PARA CARGAR EL MURO DINÁMICAMENTE (AJAX)
        function cargarMuro() {
            fetch('fetch_posts.php')
                .then(response => {
                    if (!response.ok) throw new Error("Error en el servidor");
                    return response.text();
                })
                .then(html => {
                    // Solo actualizamos el contenido si ha cambiado (opcional)
                    document.getElementById('muro-container').innerHTML = html;
                })
                .catch(error => console.error("Error al actualizar el muro:", error));
        }

        // Cargar el muro al abrir la página
        cargarMuro();

        // Actualizar cada 3 segundos para ver fotos nuevas sin refrescar
        setInterval(cargarMuro, 3000);
    </script>

</body>
</html>

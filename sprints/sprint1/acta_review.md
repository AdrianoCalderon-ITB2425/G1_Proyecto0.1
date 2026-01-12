# Sprint 1: Configuración del Servidor Web

## Objetivos
- Crear un servidor web funcional con Apache
- Configurar PHP y MySQL
- Implementar la aplicación Extagram en un solo servidor
- Habilitar la subida de imágenes

---

## Instalación de Componentes

### 1. Actualizar el sistema e instalar paquetes necesarios
```bash
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql -y
```

**Paquetes instalados:**
- `apache2` - Servidor web Apache HTTP Server
- `mysql-server` - Sistema de gestión de base de datos MySQL
- `php` - Lenguaje de programación PHP
- `libapache2-mod-php` - Módulo para integrar PHP con Apache
- `php-mysql` - Extensión para conectar PHP con MySQL

---

## Configuración de Archivos de la Aplicación

### 2. Crear el archivo principal (index.php)
```bash
sudo nano /var/www/html/index.php
```

**Contenido:**
```php
<!DOCTYPE html>
<link rel="stylesheet" href="https://static.extagram.itb/style.css">
 
<form method="POST" enctype="multipart/form-data" action="upload.php">
    <input type="text" name="post" placeholder="Write something...">
    <input id="file" type="file" name="photo" onchange="document.getElementById('preview').src=window.URL.createObjectURL(event.target.files[0])">
    <label for="file">
        <img id="preview" src="https://static.extagram.itb/preview.svg">
    </label>
    <input type="submit" value="Publish">
</form>

<?php
$db = new mysqli("db.extagram.itb", "extagram_admin", "pass123", "extagram_db");
 
foreach ($db->query("SELECT * FROM posts") as $fila) {
    echo "<div class='post'>";
    echo "<p>".$fila['post']."</p>";
    if (!empty($fila['photourl'])) {
        echo "<img src='https://storage.extagram.itb/".$fila['photourl']."'>";
    }
    echo "</div>";
}
?>
```

---

### 3. Crear el script de subida de archivos (upload.php)
```bash
sudo nano /var/www/html/upload.php
```

**Contenido:**
```php
<?php
if (!empty($_POST["post"])) {
    $photoid;
    if(!empty($_FILES['photo']['name'])){
        $photoid = uniqid();
        move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/' . $photoid);
    }
    $db = new mysqli("db.extagram.itb", "extagram_admin", "pass123", "extagram_db");
    $stmt = $db->prepare("INSERT INTO posts VALUES(?,?)");
    $stmt->bind_param("ss", $_POST["post"], $photoid);
    $stmt->execute();
    $stmt->close();
}
 
header("location: /");
?>
```

---

### 4. Crear el archivo de estilos CSS (style.css)
```bash
sudo nano /var/www/html/style.css
```

**Contenido:**
```css
body {
    background: #fafafa;
    font-family: sans;
    margin: 0;
}

form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 1em;
    background: white;
    border-bottom: 1px solid #dbdbdb;
    padding: 8px;
}

input[type=text] {
    border: 1px solid #dbdbdb;
    padding: 8px;
    width: 300px;
}

input[type=submit] {
    background: #0096f7;
    color: white;
    border: 0;
    border-radius: 3px;
    width: 300px;
    padding: 8px;
}

#file { display: none; }

#preview { max-width: 300px; }

.post {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    display: flex;
    flex-direction: column;
    border: 1px solid #dbdbdb;
    border-radius: 3px;
    margin-bottom: 24px;
}

.post img { max-width: 600px; }

.post p { padding: 16px; }
```

---

### 5. Crear el icono de vista previa (preview.svg)
```bash
sudo nano /var/www/html/preview.svg
```

**Contenido:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="300" height="300">
<g>
<rect width="100" height="100" fill="#cecece"/>
<path fill="#ffffff" transform="translate(25 25)" d="M48.1,26.3c0,4.3,0,7.2-0.1,8.8c-0.2,3.9-1.3,6.9-3.5,9s-5.1,3.3-9,3.5c-1.6,0.1-4.6,0.1-8.8,0.1c-4.3,0-7.2,0-8.8-0.1c-3.9-0.2-6.9-1.3-9-3.5c-2.1-2.1-3.3-5.1-3.5-9c-0.1-1.6-0.1-4.6-0.1-8.8s0-7.2,0.1-8.8c0.2-3.9,1.3-6.9,3.5-9c2.1-2.1,5.1-3.3,9-3.5c1.6-0.1,4.6-0.1,8.8-0.1c4.3,0,7.2,0,8.8,0.1c3.9,0.2,6.9,1.3,9,3.5s3.3,5.1,3.5,9C48,19.1,48.1,22,48.1,26.3z M28.8,8.7c-1.3,0-2,0-2.1,0c-0.1,0-0.8,0-2.1,0c-1.3,0-2.3,0-2.9,0c-0.7,0-1.6,0-2.7,0.1c-1.1,0-2.1,0.1-2.9,0.3c-0.8,0.1-1.5,0.3-2,0.5c-0.9,0.4-1.7,0.9-2.5,1.6c-0.7,0.7-1.2,1.5-1.6,2.5c-0.2,0.5-0.4,1.2-0.5,2s-0.2,1.7-0.3,2.9c0,1.1-0.1,2-0.1,2.7c0,0.7,0,1.7,0,2.9c0,1.3,0,2,0,2.1s0,0.8,0,2.1c0,1.3,0,2.3,0,2.9c0,0.7,0,1.6,0.1,2.7c0,1.1,0.1,2.1,0.3,2.9s0.3,1.5,0.5,2c0.4,0.9,0.9,1.7,1.6,2.5c0.7,0.7,1.5,1.2,2.5,1.6c0.5,0.2,1.2,0.4,2,0.5c0.8,0.1,1.7,0.2,2.9,0.3s2,0.1,2.7,0.1c0.7,0,1.7,0,2.9,0c1.3,0,2,0,2.1,0c0.1,0,0.8,0,2.1,0c1.3,0,2.3,0,2.9,0c0.7,0,1.6,0,2.7-0.1c1.1,0,2.1-0.1,2.9-0.3c0.8-0.1,1.5-0.3,2-0.5c0.9-0.4,1.7-0.9,2.5-1.6c0.7-0.7,1.2-1.5,1.6-2.5c0.2-0.5,0.4-1.2,0.5-2c0.1-0.8,0.2-1.7,0.3-2.9c0-1.1,0.1-2,0.1-2.7c0-0.7,0-1.7,0-2.9c0-1.3,0-2,0-2.1s0-0.8,0-2.1c0-1.3,0-2.3,0-2.9c0-0.7,0-1.6-0.1-2.7c0-1.1-0.1-2.1-0.3-2.9c-0.1-0.8-0.3-1.5-0.5-2c-0.4-0.9-0.9-1.7-1.6-2.5c-0.7-0.7-1.5-1.2-2.5-1.6c-0.5-0.2-1.2-0.4-2-0.5c-0.8-0.1-1.7-0.2-2.9-0.3c-1.1,0-2-0.1-2.7-0.1C31.1,8.7,30.1,8.7,28.8,8.7z M34.4,18.5c2.1,2.1,3.2,4.7,3.2,7.8s-1.1,5.6-3.2,7.8c-2.1,2.1-4.7,3.2-7.8,3.2c-3.1,0-5.6-1.1-7.8-3.2c-2.1-2.1-3.2-4.7-3.2-7.8s1.1-5.6,3.2-7.8c2.1-2.1,4.7-3.2,7.8-3.2C29.7,15.3,32.3,16.3,34.4,18.5z M31.7,31.3c1.4-1.4,2.1-3.1,2.1-5s-0.7-3.7-2.1-5.1c-1.4-1.4-3.1-2.1-5.1-2.1c-2,0-3.7,0.7-5.1,2.1s-2.1,3.1-2.1,5.1s0.7,3.7,2.1,5c1.4,1.4,3.1,2.1,5.1,2.1C28.6,33.4,30.3,32.7,31.7,31.3z M39.9,13c0.5,0.5,0.8,1.1,0.8,1.8c0,0.7-0.3,1.3-0.8,1.8c-0.5,0.5-1.1,0.8-1.8,0.8s-1.3-0.3-1.8-0.8c-0.5-0.5-0.8-1.1-0.8-1.8c0-0.7,0.3-1.3,0.8-1.8c0.5-0.5,1.1-0.8,1.8-0.8S39.4,12.5,39.9,13z"/>
</g>
</svg>
```

---

## Configuración de Virtual Hosts

### 6. Crear el archivo de configuración de Apache
```bash
sudo nano /etc/apache2/sites-available/extagram.conf
```

**Contenido:**
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName static.extagram.itb
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName storage.extagram.itb
    DocumentRoot /var/www/html/uploads
    <Directory /var/www/html/uploads>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Descripción de Virtual Hosts:**
- **localhost** - Servidor principal de la aplicación
- **static.extagram.itb** - Servidor de contenido estático (CSS, SVG)
- **storage.extagram.itb** - Servidor de almacenamiento de imágenes

---

### 7. Activar el sitio y recargar Apache
```bash
sudo a2ensite extagram.conf
sudo systemctl reload apache2
```

---

## Configuración de Base de Datos

### 8. Crear la base de datos
```bash
sudo mysql -e "CREATE DATABASE extagram_db;"
```

### 9. Crear la tabla de posts
```bash
sudo mysql -e "CREATE TABLE extagram_db.posts(post TEXT, photourl TEXT);"
```

**Estructura de la tabla:**
- `post` (TEXT) - Contenido del texto de la publicación
- `photourl` (TEXT) - ID único del archivo de imagen

### 10. Crear usuario y asignar privilegios
```bash
sudo mysql -e "CREATE USER 'extagram_admin'@'localhost' IDENTIFIED BY 'pass123';"
sudo mysql -e "GRANT ALL PRIVILEGES ON extagram_db.* TO 'extagram_admin'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

**Credenciales de la base de datos:**
- **Usuario:** extagram_admin
- **Contraseña:** pass123
- **Base de datos:** extagram_db

---

### 11. Crear directorio de uploads
```bash
sudo mkdir -p /var/www/html/uploads
sudo chown -R www-data:www-data /var/www/html/uploads
sudo chmod -R 755 /var/www/html/uploads
```

---

## Verificación

### 12. Verificar estado de los servicios
```bash
sudo systemctl status apache2
sudo systemctl status mysql
```

### 13. Acceder a la aplicación

Abrir navegador y acceder a:
```
http://localhost
```

---

## Resultado

Al finalizar este sprint, tendremos:
- Servidor web Apache funcional
- Base de datos MySQL configurada
- Aplicación Extagram operativa
- Sistema de subida de imágenes funcional
- Tres virtual hosts configurados (main, static, storage)

---

## Notas

- Todos los archivos se encuentran en `/var/www/html/`
- Las imágenes se guardan en `/var/www/html/uploads/`
- La conexión a la base de datos usa credenciales en texto plano (será mejorado en Sprint 2)
- Los virtual hosts simulan la arquitectura distribuida que se implementará en Docker
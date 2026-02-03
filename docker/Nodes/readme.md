# Configuración de S5 y S6 - Eduard Pérez Ortuño

**Servidores asignados:** S5 (Image Server) y S6 (Static Content Server)

**IP de trabajo:** 192.168.10.140

**Fecha:** 26/01/2026

---

## Índice

1. [Introducción](#introducción)
2. [Preparación del Entorno](#preparación-del-entorno)
3. [S5 - Image Server](#s5---image-server)
4. [S6 - Static Content Server](#s6---static-content-server)
5. [Construcción y Ejecución](#construcción-y-ejecución)
6. [Verificación](#verificación)
7. [Troubleshooting](#troubleshooting)

---

## Introducción

Este documento detalla la configuración completa de los servidores S5 y S6 del proyecto Extagram, ambos ejecutándose en la misma máquina virtual (192.168.10.140) mediante contenedores Docker.

### Características de S5 (Image Server)

- **Tecnología:** NGINX Alpine + Apache
- **Función:** Servir las imágenes subidas por los usuarios
- **Puerto:** 8085 (host) → 80 (contenedor)
- **Volumen compartido:** `/dockers/data/uploads` (con S4)
- **Hostname:** storage.extagram.itb

### Características de S6 (Static Content Server)

- **Tecnología:** NGINX Alpine + Apache
- **Función:** Servir archivos estáticos (CSS y SVG)
- **Puerto:** 8086 (host) → 80 (contenedor)
- **Archivos servidos:** `style.css`, `preview.svg`
- **Hostname:** static.extagram.itb

---

## Preparación del Entorno

### Paso 1: Crear estructura de directorios

Primero creamos toda la estructura de carpetas necesaria para organizar los archivos de configuración y datos de ambos servidores.
```bash
# Crear directorios principales para S5 y S6
mkdir -p ~/dockers/s5/nginx-conf
mkdir -p ~/dockers/s6/static

# Crear directorio para el docker-compose
cd ~/dockers
ls
```

**Resultado esperado:**
```
docker-compose.yml  s1  s5  s6
```

### Paso 2: Crear directorio para datos compartidos (uploads)

Este directorio será compartido entre S4 (Upload Server) y S5 (Image Server).
```bash
# Crear directorio de datos para uploads
sudo mkdir -p /dockers/data/uploads

# Establecer permisos adecuados
sudo chmod -R 777 /dockers/data/uploads

# Verificar creación
ls -la /dockers/data/
```

**Resultado esperado:**
```
drwxrwxrwx 2 root root 4096 Jan 26 16:07 uploads
```

---

## 🖼️ S5 - Image Server

### Paso 1: Crear Dockerfile para S5

El servidor S5 utiliza Apache con PHP para servir las imágenes almacenadas.
```bash
# Editar Dockerfile
sudo nano /dockers/s5/Dockerfile
```

**Contenido del Dockerfile:**
```dockerfile
FROM php:8.1-apache

# Habilitar mod_rewrite y otros módulos necesarios
RUN a2enmod rewrite headers

# Crear directorio para las imágenes
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# Copiar configuración de Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
```

**Explicación:**
- `FROM php:8.1-apache` - Usa imagen base de PHP 8.1 con Apache
- `RUN a2enmod rewrite headers` - Habilita módulos necesarios de Apache
- `mkdir -p /var/www/html/uploads` - Crea directorio para imágenes
- `chown` y `chmod` - Establece permisos correctos para Apache
- `COPY apache-config.conf` - Copia la configuración personalizada
- `EXPOSE 80` - Expone el puerto 80
- `CMD ["apache2-foreground"]` - Inicia Apache en primer plano

### Paso 2: Configurar Apache para S5
```bash
# Editar configuración de Apache
sudo nano /dockers/s5/apache-config.conf
```

**Contenido del archivo:**
```apache
<VirtualHost *:80>
    ServerName storage.extagram.itb
    DocumentRoot /var/www/html/uploads

    <Directory /var/www/html/uploads>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted

        # CORS headers
        Header set Access-Control-Allow-Origin "*"
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/s5_error.log
    CustomLog ${APACHE_LOG_DIR}/s5_access.log combined
</VirtualHost>
```

**Explicación:**
- `ServerName storage.extagram.itb` - Define el hostname del servidor
- `DocumentRoot /var/www/html/uploads` - Directorio raíz donde están las imágenes
- `Options Indexes FollowSymLinks` - Permite listar directorios y seguir enlaces simbólicos
- `AllowOverride All` - Permite usar archivos .htaccess
- `Header set Access-Control-Allow-Origin "*"` - Permite CORS para acceso desde otros dominios
- `ErrorLog` y `CustomLog` - Configuración de logs

---

## S6 - Static Content Server

### Paso 1: Crear Dockerfile para S6

El servidor S6 sirve los archivos estáticos CSS y SVG de la aplicación.
```bash
# Editar Dockerfile
sudo nano /dockers/s6/Dockerfile
```

**Contenido del Dockerfile:**
```dockerfile
FROM php:8.1-apache

# Habilitar módulos
RUN a2enmod rewrite headers

# Copiar archivos estáticos
COPY static/ /var/www/html/

# Copiar configuración de Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
```

**Explicación:**
- `COPY static/ /var/www/html/` - Copia todos los archivos estáticos al contenedor
- El resto de configuración es similar a S5 pero adaptada para archivos estáticos

### Paso 2: Configurar Apache para S6
```bash
# Editar configuración de Apache
sudo nano /dockers/s6/apache-config.conf
```

**Contenido del archivo:**
```apache
<VirtualHost *:80>
    ServerName static.extagram.itb
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # CORS headers
        Header set Access-Control-Allow-Origin "*"

        # Cache para archivos estáticos
        <FilesMatch "\.(css|svg|js)$">
            Header set Cache-Control "max-age=31536000, public"
        </FilesMatch>
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/s6_error.log
    CustomLog ${APACHE_LOG_DIR}/s6_access.log combined
</VirtualHost>
```

**Explicación:**
- `Options -Indexes` - Desactiva el listado de directorios (seguridad)
- `<FilesMatch "\.(css|svg|js)$">` - Aplica reglas específicas a archivos estáticos
- `Header set Cache-Control "max-age=31536000, public"` - Cache de 1 año para mejor rendimiento

### Paso 3: Crear directorio static y copiar archivos
```bash
# Crear directorio para archivos estáticos
sudo mkdir -p /dockers/s6/static
```

**Copiar style.css:**
```bash
# Editar style.css
sudo nano /dockers/s6/static/style.css
```

**Contenido completo de style.css:**
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

#file { 
    display: none; 
}

#preview { 
    max-width: 300px; 
}

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

.post img { 
    max-width: 600px; 
}

.post p { 
    padding: 16px; 
}
```

**Copiar preview.svg:**
```bash
# Editar preview.svg
sudo nano /dockers/s6/static/preview.svg
```

**Contenido completo de preview.svg:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="300" height="300">
<g>
<rect width="100" height="100" fill="#cecece"/>
<path fill="#ffffff" transform="translate(25 25)"
d="M48.1,26.3c0,4.3,0,7.2-0.1,8.8c-0.2,3.9-1.3,6.9-3.5,9s-5.1,3.3-9,3.5c-1.6,0.1-4.6,0.1-8.8,0.1c-4.3,0-7.2,0-8.8-0.1
c-3.9-0.2-6.9-1.3-9-3.5c-2.1-2.1-3.3-5.1-3.5-9c-0.1-1.6-0.1-4.6-0.1-8.8s0-7.2,0.1-8.8c0.2-3.9,1.3-6.9,3.5-9
c2.1-2.1,5.1-3.3,9-3.5c1.6-0.1,4.6-0.1,8.8-0.1c4.3,0,7.2,0,8.8,0.1c3.9,0.2,6.9,1.3,9,3.5s3.3,5.1,3.5,9
C48,19.1,48.1,22,48.1,26.3z M28.8,8.7c-1.3,0-2,0-2.1,0c-0.1,0-0.8,0-2.1,0c-1.3,0-2.3,0-2.9,0c-0.7,0-1.6,0-2.7,0.1
c-1.1,0-2.1,0.1-2.9,0.3c-0.8,0.1-1.5,0.3-2,0.5c-0.9,0.4-1.7,0.9-2.5,1.6c-0.7,0.7-1.2,1.5-1.6,2.5c-0.2,0.5-0.4,1.2-0.5,2
s-0.2,1.7-0.3,2.9c0,1.1-0.1,2-0.1,2.7c0,0.7,0,1.7,0,2.9c0,1.3,0,2,0,2.1s0,0.8,0,2.1c0,1.3,0,2.3,0,2.9c0,0.7,0,1.6,0.1,2.7
c0,1.1,0.1,2.1,0.3,2.9s0.3,1.5,0.5,2c0.4,0.9,0.9,1.7,1.6,2.5c0.7,0.7,1.5,1.2,2.5,1.6c0.5,0.2,1.2,0.4,2,0.5
c0.8,0.1,1.7,0.2,2.9,0.3s2,0.1,2.7,0.1c0.7,0,1.7,0,2.9,0c1.3,0,2,0,2.1,0c0.1,0,0.8,0,2.1,0c1.3,0,2.3,0,2.9,0
c0.7,0,1.6,0,2.7-0.1c1.1,0,2.1-0.1,2.9-0.3c0.8-0.1,1.5-0.3,2-0.5c0.9-0.4,1.7-0.9,2.5-1.6c0.7-0.7,1.2-1.5,1.6-2.5
c0.2-0.5,0.4-1.2,0.5-2c0.1-0.8,0.2-1.7,0.3-2.9c0-1.1,0.1-2,0.1-2.7c0-0.7,0-1.7,0-2.9c0-1.3,0-2,0-2.1s0-0.8,0-2.1
c0-1.3,0-2.3,0-2.9c0-0.7,0-1.6-0.1-2.7c0-1.1-0.1-2.1-0.3-2.9c-0.1-0.8-0.3-1.5-0.5-2c-0.4-0.9-0.9-1.7-1.6-2.5
c-0.7-0.7-1.5-1.2-2.5-1.6c-0.5-0.2-1.2-0.4-2-0.5c-0.8-0.1-1.7-0.2-2.9-0.3c-1.1,0-2-0.1-2.7-0.1C31.1,8.7,30.1,8.7,28.8,8.7z
M34.4,18.5c2.1,2.1,3.2,4.7,3.2,7.8s-1.1,5.6-3.2,7.8c-2.1,2.1-4.7,3.2-7.8,3.2c-3.1,0-5.6-1.1-7.8-3.2c-2.1-2.1-3.2-4.7-3.2-7.8
s1.1-5.6,3.2-7.8c2.1-2.1,4.7-3.2,7.8-3.2C29.7,15.3,32.3,16.3,34.4,18.5z M31.7,31.3c1.4-1.4,2.1-3.1,2.1-5s-0.7-3.7-2.1-5.1
c-1.4-1.4-3.1-2.1-5.1-2.1c-2,0-3.7,0.7-5.1,2.1s-2.1,3.1-2.1,5.1s0.7,3.7,2.1,5c1.4,1.4,3.1,2.1,5.1,2.1
C28.6,33.4,30.3,32.7,31.7,31.3z M39.9,13c0.5,0.5,0.8,1.1,0.8,1.8c0,0.7-0.3,1.3-0.8,1.8c-0.5,0.5-1.1,0.8-1.8,0.8
s-1.3-0.3-1.8-0.8c-0.5-0.5-0.8-1.1-0.8-1.8c0-0.7,0.3-1.3,0.8-1.8c0.5-0.5,1.1-0.8,1.8-0.8S39.4,12.5,39.9,13z"/>
</g>
</svg>
```

---

## Construcción y Ejecución

### Verificar estructura de archivos

Antes de construir, verificamos que todos los archivos están en su lugar:
```bash
# Verificar S5
ls -la /dockers/s5/
```

**Salida esperada:**
```
total 16
drwxr-xr-x 2 root root 4096 Jan 20 16:36 .
drwxr-xr-x 4 root root 4096 Jan 20 16:31 ..
-rw-r--r-- 1 root root  415 Jan 20 16:36 Dockerfile
-rw-r--r-- 1 root root  441 Jan 20 16:36 apache-config.conf
```
```bash
# Verificar S6
ls -la /dockers/s6/
```

**Salida esperada:**
```
total 20
drwxr-xr-x 3 root root 4096 Jan 20 16:37 .
drwxr-xr-x 4 root root 4096 Jan 20 16:31 ..
-rw-r--r-- 1 root root  359 Jan 20 16:37 Dockerfile
-rw-r--r-- 1 root root  597 Jan 20 16:37 apache-config.conf
drwxr-xr-x 2 root root 4096 Jan 20 16:44 static
```
```bash
# Verificar archivos estáticos de S6
ls -la /dockers/s6/static/
```

**Salida esperada:**
```
total 16
drwxr-xr-x 2 root root 4096 Jan 20 16:44 .
drwxr-xr-x 3 root root 4096 Jan 20 16:37 ..
-rw-r--r-- 1 root root 2196 Jan 20 16:44 preview.svg
-rw-r--r-- 1 root root  829 Jan 20 16:43 style.css
```

### Construir las imágenes Docker
```bash
# Construir imagen de S5
sudo docker-compose build s5-images
```

**Salida:** Docker construirá la imagen descargando las capas necesarias y ejecutando los comandos del Dockerfile.

---

## Verificación

### Verificar contenido dentro del contenedor S5
```bash
# Acceder al contenedor S5
sudo docker exec -it s5-images bash

# Ver contenido del directorio uploads
ls
```

**Salida esperada:**
```
bin   dev  home  lib64  mnt  proc  run   srv  tmp  var
boot  etc  lib   media  opt  root  sbin  sys  usr
```
```bash
# Ver directorio de Apache
ls -la /usr/local/apache2/htdocs/uploads/
```

**Salida esperada:**
```
total 24
drwxr-xr-x 2 www-data www-data  4096 Jan 26 15:40 .
drwxr-xr-x 1 root     root      4096 Jan 26 15:37 ..
drwxrwxrwx 1 www-data www-data  4676 Jan 26 15:03 6977832715f33
-rw-r--r-- 1 www-data www-data  4676 Jan 26 15:07 6977832715f33
-rw-r--r-- 1 www-data www-data  7697 Jan 26 15:07 6977878eb592
-rw-r--r-- 1 www-data www-data 195361 Jan 26 15:13 697849c025cf
-rw-r--r-- 1 www-data www-data 34825 Jan 26 15:25 6977878eb592
-rw-r--r-- 1 www-data www-data  9403 Jan 26 15:28 6977881359430
-rw-r--r-- 1 www-data www-data 21329 Jan 26 15:40 6977afd7a20
```

### Salir del contenedor
```bash
exit
```

---

## Troubleshooting

### Problema: Permisos denegados en /dockers/data/uploads

**Error:**
```
mkdir: cannot create directory '/home/isard/dockers/data/uploads': Permission denied
```

**Solución:**
```bash
# Usar sudo
sudo mkdir -p /dockers/data/uploads
sudo chmod -R 777 /dockers/data/uploads
```

### Problema: No se puede acceder a las imágenes

**Verificar:**
```bash
# Comprobar que el contenedor está corriendo
sudo docker ps | grep s5

# Verificar logs del contenedor
sudo docker logs s5-images

# Verificar permisos dentro del contenedor
sudo docker exec -it s5-images ls -la /var/www/html/uploads
```

### Problema: CSS no se carga correctamente

**Verificar:**
```bash
# Comprobar que el archivo existe en S6
sudo docker exec -it s6-static cat /var/www/html/style.css

# Verificar Content-Type
curl -I http://localhost:8086/style.css
```

---

**Responsable:** Eduard Pérez Ortuño

**Equipo:** Proyecto Extagram - Grupo 1

**Scrum Master:** Carlos Rodríguez Díaz

**Product Owner:** Francesc Martínez Ridao
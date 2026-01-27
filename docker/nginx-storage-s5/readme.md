# S5 - Image Server (Eduard)

## Descripció

S5 és el servidor especialitzat en servir les imatges pujades pels usuaris a través de S4. Utilitza Apache HTTP Server per proporcionar accés ràpid i eficient als arxius multimèdia emmagatzemats.

**Funció principal:** Servir imatges des del directori `/uploads/` de forma optimitzada i amb accés de només lectura.

---

## Arquitectura
```
Client Browser
      ↓
[S1] Load Balancer
      ↓
[S2/S3] Genera HTML amb:
      <img src="https://storage.extagram.itb/abc123.jpg">
      ↓
[S5] Apache serveix la imatge
      ↓
/data/uploads/abc123.jpg (volum compartit amb S4)
```

---

## Configuració Inicial

### Crear estructura de directoris
```bash
# Crear directoris per S5
mkdir -p ~/dockers/s5/nginx-conf
mkdir -p ~/dockers/s6/static

# Crear directori per volums compartits
sudo mkdir -p ~/dockers/data/uploads
sudo chmod -R 755 ~/dockers/data/uploads
```

**Explicació:**
- `/dockers/s5/`: Directori de configuració del servidor S5
- `/dockers/data/uploads/`: Volum compartit on S4 guarda les imatges i S5 les serveix

---

## Dockerfile de S5

**Ubicació:** `/dockers/s5/Dockerfile`
```dockerfile
FROM php:8.1-apache

# Habilitar mod_rewrite i altres mòduls necessaris
RUN a2enmod rewrite headers

# Crear directori per les imatges
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# Copiar configuració d'Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
```

**Explicació:**
1. **FROM php:8.1-apache**: Utilitzem la imatge oficial d'Apache amb PHP 8.1
2. **RUN a2enmod rewrite headers**: Habilitem mòduls necessaris per reescriptura d'URLs i capçaleres CORS
3. **RUN mkdir**: Creem el directori `/uploads/` amb permisos correctes
4. **COPY apache-config.conf**: Copiem la configuració personalitzada d'Apache
5. **EXPOSE 80**: Exposem el port HTTP estàndard
6. **CMD**: Iniciem Apache en primer pla

---

## Configuració d'Apache

**Ubicació:** `/dockers/s5/apache-config.conf`
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

**Explicació de les directives:**
- **ServerName storage.extagram.itb**: Nom de domini del servidor d'imatges
- **DocumentRoot /var/www/html/uploads**: Directori arrel on es troben les imatges
- **Options Indexes FollowSymLinks**: Permet llistar directoris i seguir enllaços simbòlics
- **AllowOverride All**: Permet usar fitxers `.htaccess`
- **Require all granted**: Permet accés públic a tothom
- **Header set Access-Control-Allow-Origin "*"**: Permet CORS per a peticions cross-origin
- **ErrorLog / CustomLog**: Logs d'errors i accessos per debugging

---

## Configuració Docker Compose

**Fragment del `/dockers/docker-compose.yml` corresponent a S5:**
```yaml
  # S5 - Image Server (Eduard)
  s5-images:
    image: httpd:2.4
    container_name: s5-images
    volumes:
      - uploads-shared:/usr/local/apache2/htdocs/uploads:ro
    networks:
      - extagram-net
    restart: unless-stopped
```

**Explicació:**
- **image: httpd:2.4**: Utilitza la imatge oficial d'Apache 2.4 (més lleugera que php:apache)
- **container_name: s5-images**: Nom del contenidor per identificació
- **volumes: uploads-shared:/usr/local/apache2/htdocs/uploads:ro**: 
  - Munta el volum compartit `uploads-shared` 
  - Ubicació dins del contenidor: `/usr/local/apache2/htdocs/uploads`
  - **`:ro`** = Read Only (només lectura, per seguretat)
- **networks: extagram-net**: Connectat a la xarxa interna dels contenidors
- **restart: unless-stopped**: Reinicia automàticament si falla

---

## Comandes de Desplegament

### Construir la imatge
```bash
cd /dockers

# Reconstruir només S5
sudo docker-compose build s5-images
```

### Aixecar el contenidor
```bash
# Aixecar S5 en mode detached (background)
sudo docker-compose up -d s5-images

# Aixecar S5 i veure els logs en temps real
sudo docker-compose up s5-images
```

### Veure l'estat
```bash
# Veure contenidors en execució
sudo docker ps | grep s5

# Veure logs del contenidor
sudo docker logs s5-images

# Veure logs en temps real
sudo docker-compose logs -f s5-images
```

### Reiniciar el contenidor
```bash
# Parar el contenidor
sudo docker-compose stop s5-images

# Aixecar-lo de nou
sudo docker-compose start s5-images

# O reiniciar directament
sudo docker-compose restart s5-images
```

### Reconstruir després de canvis
```bash
# Parar, reconstruir i aixecar
sudo docker-compose down s5-images
sudo docker-compose build s5-images
sudo docker-compose up -d s5-images
```

---

## Verificació i Testing

### Entrar al contenidor
```bash
# Obrir bash dins del contenidor
sudo docker exec -it s5-images bash

# Dins del contenidor:
ls -la /usr/local/apache2/htdocs/uploads/
exit
```

### Veure les imatges pujades
```bash
# Des de l'host
ls -la /dockers/data/uploads/

# Des del contenidor
sudo docker exec -it s5-images ls -la /usr/local/apache2/htdocs/uploads/
```

### Comprovar Apache
```bash
# Veure si Apache està funcionant
sudo docker exec -it s5-images apachectl -t

# Fer una petició HTTP de prova
sudo docker exec -it s5-images curl localhost/uploads/
```

### Obtenir la IP del contenidor
```bash
# Veure la IP interna
sudo docker inspect s5-images | grep IPAddress
```

**Sortida esperada:**
```json
"IPAddress": "172.18.0.5"
```

### Provar accés HTTP
```bash
# Petició a la IP del contenidor (substituir X per la IP real)
curl http://172.18.0.X/uploads/

# Provar una imatge específica
curl -I http://172.18.0.X/uploads/abc123.jpg
```

---

## Volums i Persistència

### Volum compartit `uploads-shared`
```bash
# Veure informació del volum
sudo docker volume inspect dockers_uploads-shared

# Ubicació física al host
sudo ls -la /var/lib/docker/volumes/dockers_uploads-shared/_data/
```

### Flux de dades
```
1. Usuari puja imatge → S4 (upload.php)
2. S4 guarda a → /var/www/html/uploads/abc123.jpg
3. Docker sincronitza → volum uploads-shared
4. S5 llegeix des de → /usr/local/apache2/htdocs/uploads/abc123.jpg (read-only)
5. Client descarrega ← http://storage.extagram.itb/abc123.jpg
```

---

## Debugging i Troubleshooting

### El contenidor no arranca
```bash
# Veure tots els contenidors (inclosos els parats)
sudo docker ps -a | grep s5

# Veure els logs d'error
sudo docker logs s5-images

# Forçar reconstrucció
sudo docker-compose build --no-cache s5-images
```

### No es veuen les imatges
```bash
# Comprovar que el volum està muntat
sudo docker inspect s5-images | grep -A 10 Mounts

# Verificar permisos
sudo ls -la /dockers/data/uploads/

# Comprovar dins del contenidor
sudo docker exec -it s5-images ls -la /usr/local/apache2/htdocs/uploads/
```

### Error 403 Forbidden
```bash
# Comprovar permisos del directori uploads
sudo chmod -R 755 /dockers/data/uploads/

# Reiniciar el contenidor
sudo docker-compose restart s5-images
```

### Error 404 Not Found
```bash
# Verificar que la imatge existeix
sudo docker exec -it s5-images ls /usr/local/apache2/htdocs/uploads/abc123.jpg

# Comprovar configuració d'Apache
sudo docker exec -it s5-images cat /usr/local/apache2/conf/httpd.conf | grep DocumentRoot
```

---

## Monitorització

### Veure ús de recursos
```bash
# Estadístiques en temps real
sudo docker stats s5-images

# Ús de disc del volum
sudo du -sh /dockers/data/uploads/
```

### Analitzar logs d'accés
```bash
# Últims accessos
sudo docker exec -it s5-images tail -f /usr/local/apache2/logs/access_log

# Últims errors
sudo docker exec -it s5-images tail -f /usr/local/apache2/logs/error_log
```

---

## Seguretat

### Read-Only Volume

El volum està muntat com **:ro (read-only)** per evitar que S5 modifiqui o elimini imatges per error.
```yaml
volumes:
  - uploads-shared:/usr/local/apache2/htdocs/uploads:ro
```

### CORS Headers

Permet peticions cross-origin per a que la web principal pugui carregar imatges:
```apache
Header set Access-Control-Allow-Origin "*"
```

---

## Referències

- Apache HTTP Server Documentation: https://httpd.apache.org/docs/2.4/
- Docker Compose Reference: https://docs.docker.com/compose/
- PHP Apache Official Image: https://hub.docker.com/_/php

---

## Checklist de Verificació

- [ ] Directori `/dockers/s5/` creat
- [ ] Dockerfile configurat
- [ ] apache-config.conf creat
- [ ] Volum `uploads-shared` creat i amb permisos
- [ ] Contenidor construït amb `docker-compose build`
- [ ] Contenidor aixecat amb `docker-compose up -d`
- [ ] Apache funcionant: `docker exec -it s5-images apachectl -t`
- [ ] Imatges visibles dins del contenidor
- [ ] Accés HTTP funcional des de la IP del contenidor
- [ ] Logs sense errors crítics

---

**Documentat per:** Eduard  
**Data:** 27 de gener de 2026  
**Projecte:** Extagram - Desplegament d'aplicació amb alta disponibilitat  
**Mòdul:** 0379 - Projecte intermodular d'administració de sistemes informàtics en xarxa
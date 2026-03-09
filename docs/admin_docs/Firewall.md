# Configuración de Firewall - Proyecto Extagram

## 1. Firewall del contenedor S1-Proxy (iptables)

### ¿Por qué?
El contenedor s1-proxy es el punto de entrada de toda la aplicación. Necesitamos que solo se pueda acceder al puerto 80/443 desde el exterior, mientras que la red interna Docker tiene acceso total.

### 1.1 Dockerfile del S1-Proxy
Se añadieron las herramientas necesarias y las reglas de firewall directamente en el CMD del contenedor para que se apliquen automáticamente cada vez que arranca.
```dockerfile
FROM ubuntu:24.04

# Evitar preguntas interactivas al instalar
ENV DEBIAN_FRONTEND=noninteractive

# 1. Instalar Apache y ModSecurity (WAF)
RUN apt-get update && apt-get install -y \
    apache2 \
    libapache2-mod-security2 \
    modsecurity-crs \
    && rm -rf /var/lib/apt/lists/*

# 2. Activar todos los módulos de proxy y seguridad
RUN a2enmod proxy proxy_http proxy_balancer lbmethod_byrequests slotmem_shm security2 headers rewrite ssl

# 3. Configurar WAF en modo BLOQUEO
RUN cp /etc/modsecurity/modsecurity.conf-recommended /etc/modsecurity/modsecurity.conf \
    && sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/modsecurity/modsecurity.conf

# 4. Instalar iptables, curl y ping
RUN apt-get update && apt-get install -y iptables curl iputils-ping && rm -rf /var/lib/apt/lists/*

# 5. Arrancar firewall y Apache
CMD ["/bin/bash", "-c", "iptables -F && iptables -P INPUT DROP && iptables -P FORWARD ACCEPT && iptables -P OUTPUT ACCEPT && iptables -A INPUT -i lo -j ACCEPT && iptables -A INPUT -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT && iptables -A INPUT -s 172.18.0.0/16 -j ACCEPT && iptables -A INPUT -p tcp --dport 80 -j ACCEPT && iptables -A INPUT -p icmp --icmp-type echo-request -j ACCEPT && apachectl -D FOREGROUND"]
```

### 1.2 Explicación de las reglas

| Regla | Descripción |
|-------|-------------|
| `iptables -F` | Limpia todas las reglas anteriores |
| `INPUT DROP` | Política por defecto: bloquear todo el tráfico entrante |
| `FORWARD ACCEPT` | Permite el enrutamiento entre contenedores Docker |
| `OUTPUT ACCEPT` | El contenedor puede enviar tráfico sin restricciones |
| `-i lo -j ACCEPT` | Permite tráfico interno del propio contenedor (necesario para Apache) |
| `ESTABLISHED,RELATED` | Permite respuestas a conexiones ya abiertas |
| `-s 172.18.0.0/16 -j ACCEPT` | La red interna Docker tiene acceso total |
| `--dport 80 -j ACCEPT` | Externos solo pueden acceder al puerto 80 (HTTP) |
| `icmp echo-request -j ACCEPT` | Permite ping para diagnóstico |

### 1.3 Build y despliegue del contenedor
```bash
cd ~/dockers
sudo docker-compose build --no-cache s1-proxy
sudo docker-compose stop s1-proxy
sudo docker-compose rm -f s1-proxy
sudo docker-compose up -d s1-proxy
```

### 1.4 Verificación de las reglas
```bash
sudo docker exec -it s1-proxy iptables -L -n -v --line-numbers
```

Resultado esperado:
```
Chain INPUT (policy DROP 0 packets, 0 bytes)
num   pkts bytes target     prot opt in     out     source               destination
1        0     0 ACCEPT     0    --  lo     *       0.0.0.0/0            0.0.0.0/0
2        0     0 ACCEPT     0    --  *      *       0.0.0.0/0            0.0.0.0/0            ctstate RELATED,ESTABLISHED
3        0     0 ACCEPT     0    --  *      *       172.18.0.0/16        0.0.0.0/0
4        0     0 ACCEPT     6    --  *      *       0.0.0.0/0            0.0.0.0/0            tcp dpt:80
5        0     0 ACCEPT     1    --  *      *       0.0.0.0/0            0.0.0.0/0            icmptype 8

Chain FORWARD (policy ACCEPT 0 packets, 0 bytes)

Chain OUTPUT (policy ACCEPT 0 packets, 0 bytes)
```

---

## 2. Pruebas del firewall de S1-Proxy

### Prueba 1 — La web es accesible desde el exterior

Ejecutado desde una máquina externa de la red:
```bash
curl -L -k http://192.168.10.30
```

Resultado: se recibe el HTML de la página de login de Extagram. 

### Prueba 2 — Puertos no permitidos están bloqueados
```bash
curl --max-time 5 http://192.168.10.30:8080
curl --max-time 5 http://192.168.10.30:3306
```

Resultados:
- Puerto 8080: `Connection refused` 
- Puerto 3306: `Operation timed out` 

### Prueba 3 — Ping funciona desde el exterior
```bash
ping -c 3 192.168.10.30
```

Resultado: 3 paquetes enviados, 3 recibidos, 0% packet loss. 

### Prueba 4 — Comunicación interna entre contenedores
```bash
sudo docker exec -it s2-app ping -c 3 s1-proxy
```

Resultado: comunicación interna funcionando correctamente, 0% packet loss. 

---

## 3. Firewall del host — Protección de Kibana (puerto 5601)

### ¿Por qué?
El contenedor s9-kibana expone el puerto 5601 directamente al exterior (`0.0.0.0:5601`). Kibana es una herramienta de monitorización interna y no debe ser accesible desde fuera de la red del equipo. Las reglas de iptables del contenedor s1-proxy no afectan a Kibana porque el tráfico no pasa por ese contenedor — llega directamente al host.

Por eso las reglas se aplican en el **host B-N05**, en la cadena **DOCKER-USER**, que es la cadena correcta para controlar tráfico de contenedores Docker desde el host.

### 3.1 Aplicar las reglas

Permitir solo la red interna del equipo (`192.168.10.0/24`) y bloquear el resto:
```bash
# Permitir acceso desde la red interna del equipo
sudo iptables -A DOCKER-USER -p tcp --dport 5601 -s 192.168.10.0/24 -j ACCEPT

# Bloquear cualquier otro acceso externo
sudo iptables -A DOCKER-USER -p tcp --dport 5601 -j DROP
```

> ⚠️ El orden importa: el ACCEPT debe estar antes que el DROP. De lo contrario el DROP bloquearía también la red interna.

### 3.2 Verificar el orden de las reglas
```bash
sudo iptables -L DOCKER-USER -n -v --line-numbers
```

Resultado esperado:
```
Chain DOCKER-USER (1 references)
num   pkts bytes target   prot opt in  out  source            destination
1        0     0 ACCEPT   tcp  --  *   *    192.168.10.0/24   0.0.0.0/0    tcp dpt:5601
2        0     0 DROP     tcp  --  *   *    0.0.0.0/0         0.0.0.0/0    tcp dpt:5601
```

### 3.3 Guardar las reglas para que persistan tras reinicios
```bash
sudo sh -c "iptables-save > /etc/iptables/rules.v4"
```

### 3.4 Prueba — Acceso a Kibana desde la red interna

Desde cualquier máquina de la red `192.168.10.0/24`, abre el navegador y accede a:
```
http://192.168.10.30:5601
```

Resultado esperado: se carga la interfaz de Kibana. 

### 3.5 Prueba — Acceso bloqueado desde fuera de la red

Desde una máquina externa (fuera de `192.168.10.0/24`):
```bash
curl --max-time 5 http://192.168.10.30:5601
```

Resultado esperado: `Operation timed out` 
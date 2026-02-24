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
RUN a2enmod proxy proxy_http proxy_balancer lbmethod_byrequests slotmem_shm security2 headers rewrite

# 3. Configurar WAF en modo BLOQUEO
RUN cp /etc/modsecurity/modsecurity.conf-recommended /etc/modsecurity/modsecurity.conf \
    && sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/modsecurity/modsecurity.conf

# 4. Arrancar Apache
CMD ["apachectl", "-D", "FOREGROUND"]
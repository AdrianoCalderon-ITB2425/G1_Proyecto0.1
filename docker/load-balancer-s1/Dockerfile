FROM httpd:2.4
# Activamos los módulos de Apache para que funcione como balanceador
RUN sed -i \
    -e 's/^#\(LoadModule proxy_module\)/\1/' \
    -e 's/^#\(LoadModule proxy_http_module\)/\1/' \
    -e 's/^#\(LoadModule proxy_balancer_module\)/\1/' \
    -e 's/^#\(LoadModule lbmethod_byrequests_module\)/\1/' \
    -e 's/^#\(LoadModule slotmem_shm_module\)/\1/' \
    conf/httpd.conf

# Añadimos nuestra configuración de balanceo
RUN echo "Include conf/extra/balancer.conf" >> conf/httpd.conf

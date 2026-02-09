FROM httpd:2.4

RUN sed -i \
    -e 's/^#\(LoadModule proxy_module\)/\1/' \
    -e 's/^#\(LoadModule proxy_http_module\)/\1/' \
    -e 's/^#\(LoadModule proxy_balancer_module\)/\1/' \
    -e 's/^#\(LoadModule lbmethod_byrequests_module\)/\1/' \
    -e 's/^#\(LoadModule slotmem_shm_module\)/\1/' \
    conf/httpd.conf


COPY balancer.conf /usr/local/apache2/conf/extra/balancer.conf


RUN echo "Include conf/extra/balancer.conf" >> conf/httpd.conf

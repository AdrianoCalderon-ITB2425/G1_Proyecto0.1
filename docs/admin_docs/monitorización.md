# Documentación de Administrador: Monitorización (Extagram)

## 1. Despliegue de Elasticsearch (Motor de Datos)
Núcleo donde se almacenan los logs. Requiere límite de memoria para no saturar el servidor.

**Configuración en `docker-compose.yml`:**
```yaml
  s8-elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.17.10
    container_name: s8-elasticsearch
    environment:
      - discovery.type=single-node
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    volumes:
      - es_data:/usr/share/elasticsearch/data

```

*Verificación interna:* `curl -X GET "localhost:9200/"`

---

## 2. Despliegue de Kibana (Interfaz Gráfica)

Se conecta a Elasticsearch para visualizar los datos en Dashboards.

**Configuración en `docker-compose.yml`:**

```yaml
  s9-kibana:
    image: docker.elastic.co/kibana/kibana:7.17.10
    container_name: s9-kibana
    ports:
      - "5601:5601"
    environment:
      - ELASTICSEARCH_HOSTS=http://s8-elasticsearch:9200

```

---

## 3. Recolección de Logs (Filebeat)

Para enviar los logs de los contenedores a Elasticsearch de forma automática.

**Configuración en `docker-compose.yml`:**

```yaml
  filebeat:
    image: docker.elastic.co/beats/filebeat:7.17.10
    user: root
    volumes:
      - /var/lib/docker/containers:/var/lib/docker/containers:ro
      - /var/run/docker.sock:/var/run/docker.sock:ro
    command: filebeat -e -strict.perms=false

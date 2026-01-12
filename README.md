# G1_Proyecto0.1

**Proyecto de Administración de Sistemas Informáticos en Red**

---

## ℹ️ Información del Proyecto

* **Módulo:** M0379  
* **Grupo:** G1  
* **Duración:** 6 semanas  (15/12/25 - 10/02/26)
* **Sprints:** 3 sprints de 10h cada uno

---

## 🎯 Objetivo

Desplegar una infraestructura completa para la aplicación multicapa **extagram**, que incluya:

* Servidor web (NGINX)
* Balanceo de carga
* Servicios PHP-FPM
* Almacenamiento de imágenes (en carpeta y en base de datos)
* Base de datos (MySQL)
* Segmentación de servicios en contenedores Docker
* Documentación y control de versiones en GitHub

El objetivo principal es simular un entorno real de empresa, aplicando buenas prácticas de segmentación de servicios, alta disponibilidad y despliegue automatizado.

---

## 👥 Equipo

* Carlos Rodríguez  
* Cesc Martínez  
* Jordi Eduard  
* Adriano Calderón  

---

## 🏗️ Arquitectura y Justificación

La infraestructura se ha diseñado con una arquitectura distribuida basada en contenedores Docker, segmentando los servicios para garantizar disponibilidad, escalabilidad y facilidad de mantenimiento.

**Componentes principales:**

* **S1 – nginx:alpine:** Proxy inverso y balanceador de carga.
* **S2 y S3 – php:fpm:** Servidores de aplicación para la lógica de extagram.php.
* **S4 – php:fpm:** Servicio para la subida de imágenes (upload.php).
* **S5 – nginx:alpine:** Servidor de archivos estáticos (imágenes).
* **S6 – nginx:alpine:** Servidor de archivos estáticos (CSS y SVG).
* **S7 – mysql:** Base de datos para la aplicación y almacenamiento alternativo de imágenes.

### 🔹 Justificación de la Arquitectura

1. **Alta disponibilidad:** Balanceo de carga entre S2 y S3.
2. **Escalabilidad:** Servicios desacoplados y fácilmente replicables.
3. **Seguridad:** Separación de servicios dinámicos y estáticos.
4. **Automatización:** Uso de Docker para facilitar el despliegue y la gestión.

> ⚠️ Esta arquitectura permite simular la operación de una aplicación real en producción, facilitando la práctica de administración, despliegue y documentación de servicios.

---

## 🖼️ Diagrama de la Topología

El diagrama refleja la arquitectura seleccionada, con los servicios distribuidos en contenedores y comunicados en red interna Docker.  
*(Incluye aquí tu diagrama en `/docs/arquitectura.md` o como imagen en `/imagenes/`)*

---

## 🪪 Credenciales

- Usuario: bchecker
- Contraseña: bchecker121

---

## 📂 Estructura del Repositorio

```
/
├── README.md
├── app/
│   ├── config/
│   │   └── config.sample.php
│   ├── public/
│   │   ├── extagram.php
│   │   ├── index.php
│   │   └── upload.php
│   └── static/
│       ├── preview.svg
│       └── style.css
├── docker/
│   ├── docker-compose.yml
│   ├── load-balancer-s1/
│   │   ├── Dockerfile
│   │   └── nginx.conf
│   ├── mysql-s7/
│   │   └── bbbd/
│   ├── nginx-static-s6/
│   │   ├── Dockerfile
│   │   └── nginx.conf
│   ├── nginx-storage-s5/
│   │   ├── Dockerfile
│   │   └── nginx.conf
│   ├── php-s2/
│   │   └── Dockerfile
│   ├── php-s3/
│   │   └── Dockerfile
│   └── php-upload-s4/
│       └── Dockerfile
├── docs/
│   ├── arquitectura.md
│   ├── bbbd.md
│   ├── proves.md
│   └── annexos/
│       └── enunciat.md
├── scripts/
│   ├── backup_db.sh
│   ├── deploy.sh
│   └── restore_db.sh
├── sprints/
│   ├── sprint1/
│   │   ├── acta_planning.md
│   │   ├── acta_review.md
│   │   └── proofhub_screenshot.png
│   ├── sprint2/
│   │   ├── acta_planning.md
│   │   ├── acta_review.md
│   │   └── proofhub_screenshot2.png
│   └── sprint3/
│       ├── acta_planning.md
│       ├── acta_review.md
│       └── proofhub_screenshot3.png
└── Tecnologia/
    ├── analisis.html
    └── readme.md
```

---

## 🚀 Sprints

* **Sprint 1:**  
  * Análisis y documentación inicial  
  * Primer prototipo funcional en una sola máquina  
  * Instalación y configuración básica de servicios  
  * Subida de imágenes por carpeta o base de datos

* **Sprint 2:**  
  * Segregación de servicios en Docker  
  * Comunicación entre contenedores  
  * Implementación de balanceo y proxy inverso  
  * Definición de la topología de red

* **Sprint 3:**  
  * Pruebas de alta disponibilidad  
  * Documentación final  
  * Mejoras de seguridad y automatización  
  * Presentación y defensa del proyecto

---

**Capacidades clave del equipo:**  
* Comunicación  
* Trabajo en equipo  
* Resolución de problemas  
* Gestión del tiempo  
* Liderazgo  
* Adaptabilidad  
* Pensamiento crítico  
* Empatía  

---
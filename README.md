# Proyecto 0.1 - Desplegament d'aplicació Extagram

## Información del Proyecto

- **Módulo:** 0379 - Projecte intermodular d'administració de sistemes informàtics en xarxa  
- **Actividad:** Pràctica P0.1 - Desplegament d'aplicació extagram  
- **Repositorio:** [G1_Proyecto0.1](https://github.com/AdrianoCalderon-ITB2425/G1_Proyecto0.1)  
- **Documentación:** [Google Docs](https://docs.google.com/document/d/1THLl41WrUCbAfqNdsbIqzLCdOVsk8ueJG0qNdfdMLiY/edit?tab=t.0#heading=h.jzzouqwst4le)

---

## Índice

1. [Objetivo](#objetivo)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Estructura del Repositorio](#estructura-del-repositorio)
4. [Documentación Técnica](#documentación-técnica)
   - [Enunciado del Proyecto](#enunciado-del-proyecto)
   - [Análisis Tecnológico](#análisis-tecnológico)
   - [Arquitectura](#arquitectura)
5. [Sprints](#sprints)
   - [Planificación General](#planificación-general)
   - [Sprint 1 - MVP](#sprint-1---mvp)
   - [Sprint 2 - Dockerización](#sprint-2---dockerización)
   - [Sprint 3 - Pruebas y Documentación](#sprint-3---pruebas-y-documentación)
   - [Sprint 4 - Seguridad (P0.2)](#sprint-4---seguridad-p02)
6. [Configuración Docker](#configuración-docker)
7. [Tecnologías Utilizadas](#tecnologías-utilizadas)
8. [Equipo](#equipo)

---

## Objetivo

Desplegar la aplicación web **Extagram** con alta disponibilidad y escalabilidad, usando arquitectura de microservicios y contenedores Docker.

La aplicación permite a los usuarios subir y visualizar imágenes mediante una interfaz web, con una infraestructura distribuida en 7 servidores especializados.

---

## Arquitectura del Sistema

- **S1:** Load Balancer (Apache) - Distribuye tráfico entre S2 y S3
- **S2, S3:** Web Servers (Apache + PHP) - Ejecutan extagram.php
- **S4:** Upload Server (Apache + PHP) - Procesa upload.php
- **S5:** Image Server (Apache) - Sirve imágenes desde /uploads/
- **S6:** Static Server (Apache) - Sirve CSS y SVG
- **S7:** Database (MariaDB) - Almacena metadatos

**Ver diagrama completo:** [docs/arquitectura.png](docs/arquitectura.png)

---

## Estructura del Repositorio
```
G1_PROYECTO0.1/
├── atomic_server/          # Configuración del servidor atómico (monolito)
├── Diagrama/               # Diagrama de la arquitectura del sistema
├── docker/                 # Configuración de contenedores Docker
│   ├── Balancer/          # Configuración del balanceador (S1)
│   ├── BBDD/              # Configuración de la base de datos (S7)
│   ├── Nodes/             # Configuración de nodos web (S2, S3, S4)
│   ├── data/              # Volúmenes persistentes
│   ├── html/              # Archivos PHP y estáticos compartidos
│   └── docker-compose.yml # Orquestación de contenedores
├── docs/                   # Documentación técnica
│   ├── annexos/           # Enunciado y anexos
│   └── arquitectura.png   # Diagrama de arquitectura
├── sprints/               # Gestión de sprints
│   ├── sprint1/           # Sprint 1 - MVP
│   ├── sprint2/           # Sprint 2 - Dockerización
│   ├── sprint3/           # Sprint 3 - Pruebas y documentación
│   ├── sprint4/           # Sprint 4 - Seguridad (P0.2)
│   └── planificación de sprints.md
├── Tecnologia/            # Análisis de tecnologías
│   ├── analisis.html      # Análisis técnico en HTML
│   └── readme.md          # Documentación de tecnologías
└── README.md              # Este archivo
```

---

## Documentación Técnica

### Enunciado del Proyecto
- [Enunciado completo](docs/annexos/enunciado.md)

### Análisis Tecnológico
- [Estudio de tecnologías utilizadas](Tecnologia/readme.md)
- [Análisis técnico detallado](Tecnologia/analisis.html)

### Arquitectura
- [Diagrama de arquitectura](docs/arquitectura.png)
- [Diagrama del sistema](Diagrama/)

---

## Sprints

### Planificación General
- [Planificación de sprints](sprints/planificación%20de%20sprints.md)

### Sprint 1 - MVP
**Duración:** 15/12/25 - 19/01/26

Implementación del servidor atómico (Apache + PHP + MySQL en una sola máquina).

- [Acta de Planning](sprints/sprint1/acta_planning.md)
- [Acta de Review](sprints/sprint1/acta_review.md)

### Sprint 2 - Dockerización
**Duración:** 19/01/26 - 27/01/26

Segregación de servicios en contenedores Docker con balanceo de carga.

- [Acta de Planning](sprints/sprint2/acta_planning.md)
- [Acta de Review](sprints/sprint2/acta_review.md)

### Sprint 3 - Pruebas y Documentación
**Duración:** 02/02/26 - 10/02/26

Pruebas de alta disponibilidad, documentación técnica completa y preparación de presentación.

- [Acta de Planning](sprints/sprint3/acta_planning.md)
- [Acta de Review](sprints/sprint3/acta_review.md)

### Sprint 4 - Seguridad (P0.2)
**Duración:** 17/02/26 - 24/02/26

Implementación de firewall, WAF (ModSecurity), hardening de servidores y base de datos.

- [Acta de Planning](sprints/sprint4/acta_planning.md)

---

## Configuración Docker

### Requisitos Previos
- Docker 20.10+
- Docker Compose 2.0+
- Sistema operativo: Ubuntu 24.04 LTS

### Despliegue
```bash
# Clonar el repositorio
git clone https://github.com/AdrianoCalderon-ITB2425/G1_Proyecto0.1.git
cd G1_Proyecto0.1/docker

# Crear directorios de datos
mkdir -p data/uploads data/db

# Levantar todos los contenedores
docker-compose up -d

# Verificar estado
docker-compose ps
```

### Acceso a la Aplicación

- **Web principal:** http://192.168.10.30/
- **Imágenes:** http://storage.extagram.itb/
- **Estáticos:** http://static.extagram.itb/

### Comandos Útiles
```bash
# Ver logs de un contenedor específico
docker-compose logs -f s5-images

# Reiniciar un contenedor
docker-compose restart s5-images

# Parar todos los contenedores
docker-compose down

# Reconstruir contenedores
docker-compose build
docker-compose up -d
```

---

## Tecnologías Utilizadas

### Infraestructura
- **Contenedores:** Docker 24.0, Docker Compose 2.x
- **Virtualización:** Isard VDI

### Servidores Web
- **Servidor HTTP:** Apache 2.4
- **Procesamiento PHP:** PHP 8.1 con mod_php / PHP-FPM

### Base de Datos
- **SGBD:** MariaDB 10.6
- **Persistencia:** Volúmenes Docker

### Redes
- **Red interna:** Docker Bridge Network (172.18.0.0/16)
- **Balanceo:** Apache mod_proxy con algoritmo round-robin

### Herramientas de Desarrollo
- **Control de versiones:** Git + GitHub
- **Gestión de proyecto:** ProofHub
- **Documentación:** Markdown

**Análisis completo:** [Tecnologia/readme.md](Tecnologia/readme.md)

---

## Equipo

- **Carlos Rodríguez Díaz** - Scrum Master
- **Francesc Martínez Ridao** - Product Owner
- **Eduard Pérez Ortuño** - Developer (S5, S6)
- **Adriano Calderón Paolino** - Developer

**Institución:** Institut Tecnològic de Barcelona  
**Curso:** ASIXc - Administración de Sistemas Informáticos en Red  
**Año académico:** 2025-2026

---

## Contacto y Referencias

- **Repositorio GitHub:** [G1_Proyecto0.1](https://github.com/AdrianoCalderon-ITB2425/G1_Proyecto0.1)
- **Documentación compartida:** [Google Docs](https://docs.google.com/document/d/1THLl41WrUCbAfqNdsbIqzLCdOVsk8ueJG0qNdfdMLiY/edit?tab=t.0#heading=h.jzzouqwst4le)

---

**Última actualización:** Febrero 2026  
**Versión:** 1.1 - P0.2 en progreso
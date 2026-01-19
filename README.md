# Proyecto 0.1 - Desplegament d'aplicació Extagram

## Información del Proyecto

**Módulo:** 0379 - Projecte intermodular d'administració de sistemes informàtics en xarxa  
**Actividad:** Pràctica P0.1 - Desplegament d'aplicació extagram  
**Repositorio:** [G1_Proyecto0.1](https://github.com/AdrianoCalderon-ITB2425/G1_Proyecto0.1)  
**Documentación:** [Google Docs](https://docs.google.com/document/d/1THLl41WrUCbAfqNdsbIqzLCdOVsk8ueJG0qNdfdMLiY/edit?tab=t.0#heading=h.jzzouqwst4le)

---

## 📋 Índice

1. [Información del Proyecto](#información-del-proyecto)
2. [Equipo de Trabajo](#equipo-de-trabajo)
3. [Consideraciones Previas](#consideraciones-previas)
4. [Objetivo del Proyecto](#objetivo-del-proyecto)
5. [Arquitectura del Sistema](#arquitectura-del-sistema)
6. [Análisis Técnico](#análisis-técnico)
7. [Planificación de Sprints](#planificación-de-sprints)
8. [Decisiones Tecnológicas](#decisiones-tecnológicas)
9. [Estructura del Repositorio](#estructura-del-repositorio)
10. [Documentación Técnica](#documentación-técnica)
11. [Credenciales y Recursos](#credenciales-y-recursos)
12. [Enlaces Importantes](#enlaces-importantes)
13. [Anexos](#anexos)
---

## Consideraciones Previas

Cada miembro del equipo es responsable de la **totalidad del contenido del proyecto**, independientemente de quién haya realizado cada parte. Todos los miembros deben:

- Conocer todas las partes del proyecto
- Defender su contenido mediante preguntas del profesorado
- Ser responsables de la custodia de la totalidad del proyecto
- Tener siempre acceso al proyecto y trabajar en equipo

### Capacidades Clave del Equipo

Durante la realización del proyecto se valoran y desarrollan las siguientes capacidades:

1. **Comunicación** - Compartir ideas, dar y recibir feedback, mantener informado al equipo
2. **Trabajo en equipo** - Colaborar, respetar opiniones diversas y contribuir al bien común
3. **Resolución de problemas** - Identificar problemas, analizar situaciones y encontrar soluciones
4. **Gestión del tiempo** - Organizar y priorizar tareas para cumplir con los plazos
5. **Liderazgo** - Guiar y motivar a otros, asumir responsabilidades
6. **Adaptabilidad** - Ajustarse a cambios y nuevas situaciones
7. **Pensamiento crítico** - Analizar información de manera objetiva
8. **Empatía** - Comprender y respetar las emociones y perspectivas del equipo

### Herramientas Obligatorias

- **GitHub** - Control de versiones
- **ProofHub** - Gestión de proyectos
- **Markdown** - Documentación

---

## Objetivo del Proyecto

Desplegar una aplicación web llamada **Extagram** que permite subir imágenes y publicarlas con **alta disponibilidad** y **escalabilidad**.

### Características Técnicas

- Aplicación desarrollada en **PHP**
- Base de datos para almacenar información
- Arquitectura de microservicios    
- Red interconectada a través del cloud
- Seguridad en las comunicaciones

---

## Arquitectura del Sistema

El sistema se compone de **7 servidores especializados** para optimizar el rendimiento y la tolerancia a fallos:

![Diagrama de Arquitectura](/DIagrama/diagrama.png)

### Componentes del Sistema

| Servidor | Función | Tecnología |
|----------|---------|------------|
| **S1** | Load Balancer / Proxy Inverso | nginx:alpine |
| **S2** | Web Server - extagram.php | php:fpm |
| **S3** | Web Server - extagram.php | php:fpm |
| **S4** | Upload Server - upload.php | php:fpm |
| **S5** | Image Server | nginx:alpine |
| **S6** | Static Content Server (CSS/SVG) | nginx:alpine |
| **S7** | Database Server | mysql |

### Flujo de Datos
```
Browser → S1 (Load Balancer) → S2/S3 (Web Servers) → S7 (Database)
                             ↓
                             S4 (Upload) → /uploads/
                             ↓
                             S5 (Images)
                             ↓
                             S6 (Static)
```

---

## Análisis Técnico

### Requisitos Críticos

1. **Persistencia de Datos**
   - Volúmenes de almacenamiento externo: `/dbdata/` y `/uploads/`
   - Garantizar que la información no sea volátil

2. **Balanceo de Carga**
   - Configuración de algoritmos de distribución en S1
   - Evitar saturación de los nodos S2 y S3

3. **Segregación de Servicios**
   - Aislamiento de tareas de escritura (S4)
   - Aislamiento de tareas de lectura (S2, S3)
   - Optimización del tiempo de respuesta

### Casos de Uso Principales

#### Caso 1: Navegación y Consulta
```
Usuario → S1 → S2/S3 → Consulta a S7 (Base de Datos)
```

#### Caso 2: Carga de Medios
```
Usuario → S1 → S4 → Almacenamiento en /uploads/
```


---

## Decisiones Tecnológicas

### 1. Plataforma de Virtualización

**Decisión:** Isard VDI

**Justificación:**
- Experiencia previa del equipo
- Interfaz conocida
- Acceso configurado y credenciales activas
- Facilita el trabajo colaborativo

### 2. Servidor Web

**Decisión Sprint 1:** Apache HTTP Server  
**Decisión Sprint 2-3:** NGINX

**Justificación Sprint 1:**
- Mayor familiaridad del equipo
- Configuración más intuitiva
- Integración directa con PHP mediante `libapache2-mod-php`

**Justificación Sprint 2-3:**
- Mejor rendimiento como proxy inverso
- Arquitectura de microservicios
- Mejor gestión de contenido estático

### 3. PHP y FastCGI

**Sprint 1:** `libapache2-mod-php`  
**Sprints 2-3:** PHP-FPM (FastCGI Process Manager)

**Versión:** PHP 8.x

### 4. Base de Datos

**Decisión:** MySQL

**Justificación:**
- Compatibilidad directa con código proporcionado (mysqli)
- Experiencia previa del equipo
- Documentación abundante

---

## Estructura del Repositorio
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
├── Diagrama/
│   └── diagrama.png
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

### Descripción de Directorios

- **app/** - Código fuente de la aplicación Extagram
  - **config/** - Archivos de configuración
  - **public/** - Scripts PHP principales
  - **static/** - Recursos estáticos (CSS, SVG)

- **Diagrama/** - Diagrama de arquitectura del sistema

- **docker/** - Configuración de contenedores Docker
  - Dockerfiles específicos para cada servicio (S1-S7)
  - docker-compose.yml para orquestación

- **docs/** - Documentación técnica del proyecto
  - Arquitectura del sistema
  - Documentación de base de datos
  - Pruebas realizadas
  - Anexos y enunciado

- **scripts/** - Scripts de automatización
  - Backup y restauración de base de datos
  - Scripts de despliegue

- **sprints/** - Documentación de cada sprint
  - Actas de planning y review
  - Capturas de ProofHub

- **Tecnologia/** - Análisis de tecnologías empleadas

---

## Documentación Técnica

### Requisitos de Documentación

1. **GitHub**
   - Repositorio con validación SSH (intercambio de clave pública/privada)
   - Documentación de control de versiones (ADD/PUSH/PULL/CLONE/COMMIT)

2. **Markdown**
   - Estructura de documentación en formato .md
   - Árbol de documentación en el repositorio

3. **Actas de Reuniones**
   - Sprint Planning documentado
   - Sprint Review documentado
   - Capturas de ProofHub en cada acta

### Enlaces a Documentación

- [Arquitectura del Sistema](./docs/arquitectura.md)
- [Base de Datos](./docs/bbbd.md)
- [Pruebas Realizadas](./docs/proves.md)
- [Enunciado Completo](./docs/annexos/enunciat.md)
- [Análisis Tecnológico](./Tecnologia/readme.md)

---

## Credenciales

**Usuario:** `bchecker`  
**Contraseña:** `bchecker121`

---

## Anexos

### Esquema de Base de Datos
```sql
CREATE DATABASE extagram_db;

CREATE USER 'extagram_admin'@'%' IDENTIFIED BY 'pass123';

GRANT ALL PRIVILEGES ON extagram_db.* TO 'extagram_admin'@'%';

FLUSH PRIVILEGES;

CREATE TABLE extagram_db.posts(
    post TEXT, 
    photourl TEXT
);
```

### Código Fuente

El código fuente completo está disponible en:
- [extagram.php](./app/public/extagram.php)
- [upload.php](./app/public/upload.php)
- [style.css](./app/static/style.css)
- [preview.svg](./app/static/preview.svg)

### Scripts de Despliegue

- [deploy.sh](./scripts/deploy.sh) - Script de despliegue automático
- [backup_db.sh](./scripts/backup_db.sh) - Backup de base de datos
- [restore_db.sh](./scripts/restore_db.sh) - Restauración de base de datos

---

## Equipo de Desarrollo

Institut Tecnològic de Barcelona  
Módulo 0379 - Administración de Sistemas Informáticos en Xarxa

---

**Última actualización:** Enero 2026
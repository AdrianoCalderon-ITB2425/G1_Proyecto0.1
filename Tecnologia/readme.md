# Documentación Técnica y Análisis del Proyecto: Extagram

## 1. Stack Tecnológico y Decisiones de Arquitectura

### 1.1. Plataforma de Virtualización
> **Decisión:** Isard VDI

Utilizaremos **Isard VDI** como plataforma de virtualización para el desarrollo del proyecto.

* **Justificación:**
    * Experiencia previa trabajando con Isard.
    * Conocimiento de su interfaz y flujo de trabajo.
    * Acceso configurado y credenciales activas.
    * Permite crear y gestionar máquinas virtuales rápidamente.
    * Facilita el trabajo colaborativo del equipo.

* **Alternativas consideradas:**
    * *VirtualBox:* Requeriría instalación local en cada equipo.
    * *VMware:* Mayor curva de aprendizaje.
    * *Proxmox:* Más complejo para nuestras necesidades actuales.

---

### 1.2. Servidor Web: NGINX vs Apache
> **Decisión:** Apache HTTP Server

Utilizaremos Apache como servidor web principal para el MVP.

* **Justificación:**
    * Mayor familiaridad del equipo con Apache.
    * Configuración previa en prácticas anteriores.
    * Conocimiento de la estructura de archivos de configuración.
    * Documentación y sintaxis más intuitiva.
    * Configuración de PHP directa con `libapache2-mod-php`.

#### Comparativa Técnica

| Característica | Apache | NGINX |
| :--- | :---: | :---: |
| **Familiaridad del equipo** | Alta | Baja |
| **Facilidad de configuración** | Más sencilla | Requiere aprendizaje |
| **Documentación conocida** | Sí | No |
| **Rendimiento estático** | Bueno | Excelente |
| **Configuración PHP** | Directa | Requiere PHP-FPM |

> **Nota:** Para los Sprints 2 y 3 (arquitectura Docker), evaluaremos migrar a **NGINX** para aprovechar sus ventajas como proxy inverso y balanceador de carga.

---

### 1.3. PHP y FastCGI
> **Decisión:** PHP con mod_php (Sprint 1) → PHP-FPM (Sprints 2-3)

* **Sprint 1 (MVP):**
    * Usaremos `libapache2-mod-php` para integración directa con Apache.
    * Configuración más simple para el MVP.
    * Menos componentes que gestionar inicialmente.
* **Sprints 2-3 (Docker):**
    * Migraremos a **PHP-FPM** (FastCGI Process Manager).
    * Requerido para la arquitectura de microservicios.
    * Mejor aislamiento entre servicios.
    * Compatible con NGINX en el balanceador (S1).
* **Versión:** PHP 8.x (repositorios oficiales Debian/Ubuntu).

---

### 1.4. Base de Datos: MySQL vs MariaDB
> **Decisión:** MySQL

Utilizaremos **MySQL** como sistema de gestión de base de datos.

* **Justificación:**
    * El código proporcionado usa `mysqli`.
    * Compatibilidad directa sin modificaciones.
    * Experiencia previa del equipo con MySQL.
    * Documentación abundante.

#### Comparativa

| Característica | MySQL | MariaDB |
| :--- | :--- | :--- |
| **Compatibilidad código** | Directa | Compatible |
| **Experiencia del equipo** | Alta | Media |
| **Disponibilidad** | Repos oficiales | Repos oficiales |
| **Funcionalidades** | Suficiente | Más features |

---

## 2. Análisis del Proyecto

### 2.1. Lectura y Comprensión del Enunciado
Tras analizar los requisitos del proyecto "Extagram", se comprende la necesidad de desplegar una infraestructura web distribuida. El objetivo es garantizar la **alta disponibilidad** y la **integridad de los datos** mediante la separación de servicios y el uso de almacenamiento persistente.

### 2.2. Análisis de la Arquitectura Objetivo (7 Servidores)
Se ha definido una topología de red compuesta por 7 nodos especializados para optimizar el rendimiento y la tolerancia a fallos:

* **S1 (Load Balancer):** Gestiona el tráfico entrante desde el navegador y lo distribuye hacia los servidores de aplicaciones.
* **S2 y S3 (Web Servers):** Instancias gemelas que ejecutan el núcleo de la aplicación (`extagram.php`).
* **S4 (Upload Server):** Servidor dedicado exclusivamente al proceso de subida de archivos (`upload.php`).
* **S5 (Image Server):** Nodo especializado en el procesamiento y entrega de recursos multimedia.
* **S6 (Static Server):** Servidor optimizado para la entrega rápida de contenido estático (CSS, JS, HTML).
* **S7 (Database Server):** Motor central de base de datos que sirve a los servidores de aplicaciones.

### 2.3. Requisitos Técnicos
El análisis técnico determina los siguientes requisitos críticos para la implementación:

1.  **Persistencia:** Es imperativo el uso de volúmenes de almacenamiento externo (`/dbdata/` y `/uploads/`) para asegurar que la información no sea volátil.
2.  **Balanceo:** Configuración de algoritmos de distribución en **S1** para evitar la saturación de los nodos **S2** y **S3**.
3.  **Segregación de Servicios:** Aislamiento de las tareas de escritura (**S4**) y lectura de datos (**S2**, **S3**) para optimizar el tiempo de respuesta.

### 2.4. Casos de Uso Principales
Se establecen los flujos operativos básicos de la plataforma:

* **Navegación y Consulta:**
    User $\rightarrow$ S1 $\rightarrow$ S2/S3 $\rightarrow$ Consulta a S7 (Base de Datos).
* **Carga de Medios:**
    User $\rightarrow$ S1 $\rightarrow$ Redirección a S4 $\rightarrow$ Almacenamiento físico en volumen `/uploads/`.

### 2.5. Diagrama de la Arquitectura
*(Insertar diagrama aquí representando la topología de S1 a S7 y sus conexiones)*

### 2.6. Conclusión
Este análisis técnico final certifica que la arquitectura de **7 servidores** propuesta es la solución más eficiente para escalar el proyecto de forma modular. La separación de roles permite realizar mantenimientos o actualizaciones en servicios específicos (como la base de datos o el servidor de imágenes) sin afectar la disponibilidad global de la plataforma.
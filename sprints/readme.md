## Planificación de Sprints

El proyecto se divide en **3 sprints quinzenales** de 10 horas cada uno:

### Sprint 1: MVP (15/12/25 - 19/01/26)

**Objetivos:**
- Análisis y documentación inicial
- Diseño mínimamente viable (MVP) con servidor web funcional
- Implementación en una sola máquina (NGINX o Apache)
- Instalación y configuración básica de servicios
- Subida de imágenes por carpeta o base de datos

**Entregables:**
- Servidor web funcional
- Base de datos configurada
- Aplicación operativa básica
- Acta de Sprint Planning
- Acta de Sprint Review

**Documentación:** [Sprint 1](./sprints/sprint1/)

### Sprint 2: Dockerización (19/01/26 - 27/01/26)

**Objetivos:**
- Segregación de servicios en Docker
- Comunicación entre contenedores mediante red bridge
- Implementación de proxy inverso y balanceo de carga
- Configurar balanceo entre S2-S3
- Segregar peticiones hacia S4, S5 y S6
- Definición de la topología de red con Packet Tracer

**Entregables:**
- Docker Compose configurado
- Arquitectura de 7 contenedores
- Documentación de red y comunicación
- Acta de Sprint Planning
- Acta de Sprint Review

**Documentación:** [Sprint 2](./sprints/sprint2/)

### Sprint 3: Pruebas y Documentación (02/02/26 - 10/02/26)

**Objetivos:**
- Pruebas de operativa web
- Pruebas de alta disponibilidad
- Pruebas de caída de nodos redundantes
- Documentación final completa
- Mejoras de seguridad y automatización
- Configuración de control de versiones con SSH
- Presentación y defensa del proyecto

**Entregables:**
- Repositorio GitHub con SSH configurado
- Documentación Markdown completa
- Informe de pruebas
- Acta de Sprint Planning
- Acta de Sprint Review
- Presentación final

**Documentación:** [Sprint 3](./sprints/sprint3/)
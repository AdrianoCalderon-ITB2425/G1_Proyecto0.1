# Proyecto 0.1 - Desplegament d'aplicació Extagram

## Información del Proyecto

- **Módulo:** 0379 - Projecte intermodular d'administració de sistemes informàtics en xarxa  
- **Actividad:** Pràctica P0.1 - Desplegament d'aplicació extagram  
- **Repositorio:** [G1_Proyecto0.1](https://github.com/AdrianoCalderon-ITB2425/G1_Proyecto0.1)  
- **Documentación:** [Google Docs](https://docs.google.com/document/d/1THLl41WrUCbAfqNdsbIqzLCdOVsk8ueJG0qNdfdMLiY/edit?tab=t.0#heading=h.jzzouqwst4le)

---

## Objetivo

Desplegar la aplicación web **Extagram** con alta disponibilidad y escalabilidad, usando arquitectura de microservicios y contenedores Docker.

---

## Arquitectura del Sistema

- 7 servidores especializados (balanceador, web, upload, imágenes, estáticos, BBDD)
- Balanceo de carga, persistencia y segregación de servicios
- [Ver diagrama](docs/arquitectura.png)

---

## Estructura del Repositorio
/atomic_server/ # Configuracion del servidor atómico

/Diagrama/ # Imagen del diagrama de funcionamiento

/docker/ # Configuración de contenedores Docker

/docs/ # Documentación técnica y anexos

/sprints/ # Documentación de los sprints

/Tecnologia/ # Análisis de tecnologías usadas

---

## Documentación Técnica

- [Arquitectura del Sistema](docs/arquitectura.png)
- [Enunciado](docs/annexos/enunciado.md)
- [Análisis Tecnológico](Tecnologia/readme.md)

---

## Equipo

Carlos Rodríguez, Cesc Martínez, Jordi Eduard, Adriano Calderón  
Institut Tecnològic de Barcelona

---

**Última actualización:** Febrero 2026

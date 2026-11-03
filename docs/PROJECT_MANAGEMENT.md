# Corebite — Plan de gestión y roadmap

> Documento de planificación. Fechas, horas y prioridades son estimaciones iniciales; deben revisarse en cada Sprint. No se debe marcar una tarea como completada hasta verificar sus criterios de aceptación.

## Datos del proyecto

- Producto: Corebite
- Repositorio: https://github.com/CristianGR-DEV/Secure-social-platform
- Responsable: CristianGR-DEV
- Horizonte planificado: 2026-11-01 a 2027-05-01
- Beta objetivo: 2027-05-01
- Cadencia: Sprints de 4 semanas
- Estimación: horas de trabajo
- Capacidad estimada: 292 horas planificadas, más una reserva recomendada del 15–20%
- Estado inicial conocido: análisis; repositorio inicialmente vacío
- Modelo de trabajo: planificación por Sprint (Scrum) y flujo visual continuo (Kanban)

## Sprints

| Sprint | Fechas | Objetivo | Issues | Horas |
|---|---|---|---|---:|
| Sprint 1 | 2026-11-01 – 2026-11-28 | Análisis, arquitectura y preparación | CORE-01 a CORE-05 | 40 |
| Sprint 2 | 2026-11-29 – 2026-12-26 | Interfaz, autenticación y usuarios | CORE-06 a CORE-10 | 50 |
| Sprint 3 | 2026-12-27 – 2027-01-23 | Mensajería privada y cifrado | CORE-11 a CORE-15 | 57 |
| Sprint 4 | 2027-01-24 – 2027-02-20 | Archivos, llamadas y personalización | CORE-16 a CORE-20 | 51 |
| Sprint 5 | 2027-02-21 – 2027-03-20 | Personalización avanzada, seguridad y pruebas | CORE-21 a CORE-25 | 48 |
| Sprint 6 | 2027-03-21 – 2027-04-17 | Despliegue, rendimiento y documentación | CORE-26 a CORE-29 | 34 |
| Cierre | 2027-04-18 – 2027-05-01 | Validación, release y exposición | CORE-30 | 12 |

## Registro de tareas

| ID | Issue | Sprint / fase | Horas | Prioridad |
|---|---:|---|---:|---|
| CORE-01 | #2 | Sprint 1 | 6 | Urgente |
| CORE-02 | #1 | Sprint 1 | 8 | Alta |
| CORE-03 | #3 | Sprint 1 | 10 | Urgente |
| CORE-04 | #4 | Sprint 1 | 8 | Urgente |
| CORE-05 | #5 | Sprint 1 | 8 | Alta |
| CORE-06 | #6 | Sprint 2 | 10 | Alta |
| CORE-07 | #7 | Sprint 2 | 16 | Urgente |
| CORE-08 | #8 | Sprint 2 | 9 | Alta |
| CORE-09 | #9 | Sprint 2 | 7 | Media |
| CORE-10 | #10 | Sprint 2 | 8 | Alta |
| CORE-11 | #11 | Sprint 3 | 10 | Urgente |
| CORE-12 | #12 | Sprint 3 | 12 | Urgente |
| CORE-13 | #13 | Sprint 3 | 22 | Urgente |
| CORE-14 | #14 | Sprint 3 | 7 | Alta |
| CORE-15 | #15 | Sprint 3 | 6 | Alta |
| CORE-16 | #16 | Sprint 4 | 12 | Alta |
| CORE-17 | #17 | Sprint 4 | 18 | Alta |
| CORE-18 | #18 | Sprint 4 | 8 | Alta |
| CORE-19 | #19 | Sprint 4 | 8 | Alta |
| CORE-20 | #20 | Sprint 4 | 5 | Media |
| CORE-21 | #21 | Sprint 5 | 12 | Alta |
| CORE-22 | #22 | Sprint 5 | 10 | Urgente |
| CORE-23 | #23 | Sprint 5 | 12 | Urgente |
| CORE-24 | #24 | Sprint 5 | 8 | Alta |
| CORE-25 | #25 | Sprint 5 | 6 | Alta |
| CORE-26 | #26 | Sprint 6 | 10 | Urgente |
| CORE-27 | #27 | Sprint 6 | 8 | Urgente |
| CORE-28 | #28 | Sprint 6 | 8 | Alta |
| CORE-29 | #29 | Sprint 6 | 8 | Alta |
| CORE-30 | #30 | Cierre | 12 | Urgente |

## Hitos

| Hito | Fecha | Resultado esperado |
|---|---|---|
| H1 — Arquitectura y análisis aprobados | 2026-11-28 | Requisitos, wireframes, ADR y base técnica |
| H2 — Usuarios operativos | 2026-12-26 | Registro, perfiles, presencia y amistades |
| H3 — Mensajería privada segura | 2027-01-23 | Mensajería E2EE validada en clientes compatibles |
| H4 — Multimedia | 2027-02-20 | Archivos y llamadas de voz 1:1 |
| H5 — Experiencia y pruebas | 2027-03-20 | Temas, layouts, seguridad y pruebas |
| H6 — Producción | 2027-04-17 | Despliegue, backup y documentación |
| H7 — Entrega | 2027-05-01 | Beta validada y presentación |

## Configuración propuesta de GitHub Projects

El conector utilizado para crear las Issues no permite crear ni modificar directamente las iteraciones, campos, vistas o elementos del Project personal. Por ello, estos pasos deben completarse desde la interfaz web del Project:

Project: https://github.com/users/CristianGR-DEV/projects/1

### Campos

- Status: Todo, In Progress, In Review, Done (campo de sistema).
- Priority: Single select — Urgente, Alta, Media, Baja.
- Estimate (h): Number.
- Iteration: Iteration — intervalos de 4 semanas.
- Start Date: Date.
- Target Date: Date.
- Module: Single select — Análisis, Frontend, Backend, Security, Database, Multimedia, DevOps, Testing, Documentation, Performance, Release.

### Vistas

1. `01 — Backlog`: Table; excluir Done; ordenar por prioridad y fecha de inicio.
2. `02 — Sprint Board`: Board; agrupar por Status; filtrar por Iteration activa.
3. `03 — Roadmap`: Roadmap; usar Start Date y Target Date.

### Filtros orientativos

- Backlog: `-status:Done`
- Sprint Board: `iteration:@current`

Comprobar la sintaxis y el comportamiento de los filtros en la interfaz de GitHub al crearlos.

### Cómo vincular las tareas

Añadir al Project los Issues #1–#30 desde el repositorio. Después rellenar los campos Priority, Estimate (h), Iteration, Start Date, Target Date y Module usando el registro anterior y los datos del cuerpo de cada Issue.

## Dependencias

Las dependencias principales están indicadas en cada Issue. Antes de iniciar una tarea, comprobar que sus dependencias estén satisfechas o documentar por qué se puede avanzar parcialmente.

## Definition of Ready

- Objetivo y alcance comprensibles.
- Criterios de aceptación definidos.
- Estimación y prioridad asignadas.
- Dependencias identificadas.
- Resultado demostrable.

## Definition of Done

- Criterios de aceptación verificados.
- Pruebas correspondientes ejecutadas.
- Sin errores críticos conocidos sin registrar.
- Documentación actualizada cuando proceda.
- Cambios versionados en Git.
- Issue actualizado con el estado real.

## Notas de planificación

- Este plan no declara que las tareas ya estén completadas.
- Las fechas son objetivos iniciales y deben revisarse según capacidad real y bloqueos.
- La beta prioriza registro, perfiles, amistades, mensajería privada E2EE, archivos, llamadas de voz 1:1 y personalización.
- Grupos avanzados, canales, llamadas grupales, compartir pantalla, plugins y procesamiento de audio/vídeo se mantienen en el backlog de versiones posteriores, salvo revisión formal del alcance.
- Validar Matrix/Synapse y su integración antes de basar toda la mensajería en esa decisión.
- No publicar secretos, tokens, claves privadas, contraseñas, ni datos sensibles del servidor.

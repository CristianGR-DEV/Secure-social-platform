# Corebite

Corebite es una plataforma web de comunicación cuyo objetivo es ofrecer cuentas, relaciones entre usuarios y mensajería privada con seguridad desde el diseño. La beta prioriza conversaciones privadas, cifrado de extremo a extremo, archivos, llamadas de voz individuales y una interfaz personalizable.

Este README reúne el alcance y la planificación del proyecto. Describe lo que se quiere construir; no afirma que una función esté terminada. El avance real se registra en los Issues y en GitHub Projects.

## Enlaces y seguimiento

- Repositorio: https://github.com/CristianGR-DEV/Secure-social-platform
- Proyecto GitHub: https://github.com/users/CristianGR-DEV/projects/1
- Board: https://github.com/users/CristianGR-DEV/projects/1/views/3
- Backlog: https://github.com/users/CristianGR-DEV/projects/1/views/1
- Roadmap: https://github.com/users/CristianGR-DEV/projects/1/views/2
- Issues CORE-01 a CORE-30: https://github.com/CristianGR-DEV/Secure-social-platform/issues

En GitHub Projects se gestionan el estado, Sprint, módulo, prioridad, fechas y esfuerzo estimado de cada tarea. Las vistas deben reflejar el estado real del trabajo.

## Visión y objetivos

Construir una aplicación web en la que las personas puedan comunicarse de forma privada y confiable desde el navegador. La beta debe demostrar un flujo completo, desde crear una cuenta y encontrar a otra persona hasta intercambiar mensajes protegidos y operar el servicio en un servidor.

Objetivos de la beta:

1. Definir con claridad el alcance, los flujos de usuario, los requisitos de seguridad y la arquitectura.
2. Permitir registro, inicio y cierre de sesión, administración de sesiones y perfiles.
3. Permitir buscar usuarios, gestionar amistades y bloquear contactos.
4. Ofrecer conversaciones privadas en tiempo real, historial y sincronización.
5. Validar el cifrado de extremo a extremo de las conversaciones privadas en clientes compatibles.
6. Permitir compartir archivos con validación de tipo y tamaño y autorización de descarga.
7. Permitir llamadas de voz individuales mediante WebRTC y probar conectividad con relay TURN.
8. Ofrecer interfaz adaptable, accesible, en español e inglés, con temas y distribución personalizable.
9. Entregar una beta desplegable, con copias de seguridad, monitorización, documentación y una presentación demostrable.

### Alcance previsto para la beta

- Registro y sesiones seguras.
- Perfiles, avatares, estados de presencia, búsqueda, amistades y bloqueo.
- Interfaz principal y componentes reutilizables.
- Mensajería privada, historial, sincronización y cifrado E2EE.
- Archivos y llamadas de voz individuales.
- Temas, paletas, ajustes exportables/importables y navegación personalizable.
- Pruebas funcionales y de seguridad, compatibilidad de navegadores, diseño responsive y accesibilidad.
- Staging, despliegue en Ubuntu Server, copias de seguridad, restauración, monitorización, optimización y documentación.

### Funciones posteriores

Los grupos avanzados, canales, llamadas grupales, compartir pantalla, plugins y procesamiento de audio o vídeo se mantienen fuera del alcance inicial de la beta. Se podrán priorizar en una versión posterior después de revisar la capacidad y el feedback.

## Cómo se quiere trabajar

La planificación combina Sprints de cuatro semanas con seguimiento visual continuo en Board, Backlog y Roadmap.

- Cada Issue debe explicar su objetivo, subtareas, dependencias y criterios de aceptación.
- Antes de comenzar, comprobar que la tarea está preparada y que sus dependencias están resueltas o documentadas.
- Las estimaciones y fechas son objetivos iniciales: se revisan con la capacidad y los bloqueos reales.
- Mantener actualizado el estado de GitHub Projects. No marcar una tarea como terminada hasta verificar sus criterios de aceptación.
- Registrar el esfuerzo real cuando se trabaje en la tarea y documentar bloqueos o limitaciones.
- Mantener la documentación al día cuando cambien la arquitectura, el despliegue o el comportamiento del producto.
- Las decisiones importantes deben quedar explicadas en un ADR o en el Issue correspondiente.
- No guardar tokens, contraseñas, claves privadas ni secretos en el repositorio, el frontend o los logs.

### Definition of Ready

Una tarea está preparada cuando:

- El objetivo y el alcance se entienden.
- Tiene criterios de aceptación verificables.
- Tiene prioridad y estimación inicial.
- Sus dependencias y riesgos están identificados.
- El resultado esperado se puede demostrar.

### Definition of Done

Una tarea puede cerrarse cuando:

- Se verificaron sus criterios de aceptación.
- Se ejecutaron las comprobaciones apropiadas para el cambio.
- No quedan errores críticos conocidos sin registrar.
- Se actualizó la documentación cuando corresponde.
- Los cambios están versionados y el Issue refleja el estado real.

## Arquitectura y tecnologías previstas

La selección final de frameworks y algunos detalles de implementación queda sujeta al prototipo técnico y al ADR de CORE-03. No se deben presentar esas decisiones pendientes como si ya estuvieran aprobadas.

| Área | Plan actual |
|---|---|
| Estructura | Monorepo con apps/web, apps/api y packages/shared |
| Lenguaje | TypeScript para la base de frontend y backend |
| Base de datos | PostgreSQL y migraciones iniciales |
| Mensajería | Validar Matrix frente a una implementación propia; el plan de beta propone Synapse y matrix-js-sdk |
| Cifrado | Usar la implementación Matrix E2EE elegida y validada; no crear criptografía propia |
| Voz | WebRTC para llamadas individuales; coturn como relay si las pruebas de red lo requieren |
| Interfaz | Sistema de diseño, tokens CSS, responsive, navegación por teclado e internacionalización es/en |
| Autenticación | Argon2id para contraseñas, sesiones revocables y cookies HttpOnly, Secure y SameSite en producción |
| Archivos | Almacenamiento fuera del directorio público, validación de contenido/tamaño y descargas autorizadas |
| Infraestructura | Docker Compose y beta objetivo en Ubuntu Server; proxy inverso y HTTPS cuando haya dominio y DNS |
| Integración continua | GitHub Actions para instalación y comprobaciones de lint/build |

El framework web, el runtime/API, el ORM y otros detalles que no están decididos deben elegirse y documentarse en CORE-03 antes de depender de ellos. La arquitectura debe definir la relación entre las cuentas Corebite y las identidades Matrix, los límites de responsabilidad de cada servicio, el modelo de amenazas y la recuperación de claves.

## Principios de seguridad y calidad

- No almacenar contraseñas en texto plano. Usar Argon2id y limitar intentos de autenticación.
- No exponer tokens ni secretos en el navegador, el repositorio o los registros.
- Proteger endpoints y sesiones; revocar sesiones cuando corresponda.
- Validar archivos por tamaño, extensión y tipo real. No confiar solo en el MIME declarado por el cliente.
- Guardar archivos fuera de rutas públicas predecibles y comprobar permisos antes de servirlos.
- No afirmar que el servidor no puede leer contenido hasta validar E2EE de extremo a extremo.
- No afirmar E2EE para llamadas de voz sin comprobarlo por separado.
- Probar pérdida de sesión, cambio de dispositivo y recuperación de claves; documentar límites.
- Probar restauraciones de backups y proteger las copias y claves.
- Priorizar accesibilidad, navegación por teclado, mensajes de error claros y compatibilidad en navegadores principales.

## Plan de Sprints

La duración prevista es de cuatro semanas por Sprint. El cierre tiene dos semanas. El plan cubre del 1 de noviembre de 2026 al 1 de mayo de 2027.

| Sprint | Fechas | Objetivo | Issues | Estimación |
|---|---|---|---|---:|
| Sprint 1 | 2026-11-01 a 2026-11-28 | Análisis, arquitectura y preparación | CORE-01 a CORE-05 | 40 h |
| Sprint 2 | 2026-11-29 a 2026-12-26 | Interfaz, autenticación y usuarios | CORE-06 a CORE-10 | 50 h |
| Sprint 3 | 2026-12-27 a 2027-01-23 | Mensajería privada y cifrado | CORE-11 a CORE-15 | 57 h |
| Sprint 4 | 2027-01-24 a 2027-02-20 | Archivos, llamadas y personalización | CORE-16 a CORE-20 | 51 h |
| Sprint 5 | 2027-02-21 a 2027-03-20 | Personalización avanzada, seguridad y pruebas | CORE-21 a CORE-25 | 48 h |
| Sprint 6 | 2027-03-21 a 2027-04-17 | Despliegue, rendimiento y documentación | CORE-26 a CORE-29 | 34 h |
| Cierre | 2027-04-18 a 2027-05-01 | Validación, release y exposición | CORE-30 | 12 h |
| **Total** |  |  | **30 Issues** | **292 h** |

Las 292 horas son una estimación inicial de trabajo. Se recomienda reservar un 15–20% adicional para imprevistos. La beta tiene como fecha objetivo el 1 de mayo de 2027; no es una fecha garantizada.

## Tareas planificadas

Las fechas por tarea y los valores de Priority, Estimate (h), Iteration y Module también se mantienen en los campos del Project. Las fechas pueden cambiar al revisar cada Sprint.

### Sprint 1 — Análisis, arquitectura y preparación · 40 h

- [CORE-01 · Definir visión, objetivos, alcance y criterios de éxito](https://github.com/CristianGR-DEV/Secure-social-platform/issues/2) — 2026-11-03 a 2026-11-05 · 6 h · Urgente · Análisis.
- [CORE-02 · Definir UX, flujos de usuario y wireframes](https://github.com/CristianGR-DEV/Secure-social-platform/issues/1) — 2026-11-03 a 2026-11-12 · 8 h · Alta · Análisis.
- [CORE-03 · Definir arquitectura, modelo de seguridad y prueba técnica de Matrix](https://github.com/CristianGR-DEV/Secure-social-platform/issues/3) — 2026-11-05 a 2026-11-15 · 10 h · Urgente · Arquitectura.
- [CORE-04 · Preparar repositorio, estructura de trabajo, TypeScript y CI inicial](https://github.com/CristianGR-DEV/Secure-social-platform/issues/4) — 2026-11-09 a 2026-11-18 · 8 h · Urgente · DevOps.
- [CORE-05 · Diseñar modelo de datos PostgreSQL y migraciones iniciales](https://github.com/CristianGR-DEV/Secure-social-platform/issues/5) — 2026-11-16 a 2026-11-28 · 8 h · Alta · Database.

### Sprint 2 — Interfaz, autenticación y usuarios · 50 h

- [CORE-06 · Crear sistema de diseño y estructura de la interfaz principal](https://github.com/CristianGR-DEV/Secure-social-platform/issues/6) — 2026-11-29 a 2026-12-09 · 10 h · Alta · Frontend.
- [CORE-07 · Implementar registro, inicio/cierre de sesión y gestión de sesiones](https://github.com/CristianGR-DEV/Secure-social-platform/issues/7) — 2026-11-29 a 2026-12-14 · 16 h · Urgente · Backend.
- [CORE-08 · Implementar perfiles, avatares y estados de presencia](https://github.com/CristianGR-DEV/Secure-social-platform/issues/8) — 2026-12-08 a 2026-12-17 · 9 h · Alta · Frontend.
- [CORE-09 · Configurar internacionalización inicial en español e inglés](https://github.com/CristianGR-DEV/Secure-social-platform/issues/9) — 2026-12-10 a 2026-12-19 · 7 h · Media · Frontend.
- [CORE-10 · Implementar búsqueda de usuarios, amistades y bloqueo](https://github.com/CristianGR-DEV/Secure-social-platform/issues/10) — 2026-12-16 a 2026-12-26 · 8 h · Alta · Backend.

### Sprint 3 — Mensajería privada y cifrado · 57 h

- [CORE-11 · Desplegar Synapse de desarrollo y vincular identidades Corebite](https://github.com/CristianGR-DEV/Secure-social-platform/issues/11) — 2026-12-27 a 2027-01-05 · 10 h · Urgente · Backend.
- [CORE-12 · Integrar Matrix JS SDK y mensajería privada en tiempo real](https://github.com/CristianGR-DEV/Secure-social-platform/issues/12) — 2027-01-02 a 2027-01-11 · 12 h · Urgente · Frontend.
- [CORE-13 · Implementar y validar E2EE en conversaciones privadas](https://github.com/CristianGR-DEV/Secure-social-platform/issues/13) — 2027-01-08 a 2027-01-20 · 22 h · Urgente · Security.
- [CORE-14 · Implementar historial, sincronización y estados de conversación](https://github.com/CristianGR-DEV/Secure-social-platform/issues/14) — 2027-01-11 a 2027-01-18 · 7 h · Alta · Frontend.
- [CORE-15 · Implementar protección adicional de acceso a conversaciones](https://github.com/CristianGR-DEV/Secure-social-platform/issues/15) — 2027-01-17 a 2027-01-23 · 6 h · Alta · Security.

### Sprint 4 — Archivos, llamadas y personalización · 51 h

- [CORE-16 · Implementar almacenamiento y envío seguro de archivos](https://github.com/CristianGR-DEV/Secure-social-platform/issues/16) — 2027-01-24 a 2027-02-03 · 12 h · Alta · Backend.
- [CORE-17 · Implementar llamadas de voz individuales mediante WebRTC](https://github.com/CristianGR-DEV/Secure-social-platform/issues/17) — 2027-01-31 a 2027-02-12 · 18 h · Alta · Multimedia.
- [CORE-18 · Configurar TURN/coturn y validar conectividad WebRTC externa](https://github.com/CristianGR-DEV/Secure-social-platform/issues/18) — 2027-02-10 a 2027-02-16 · 8 h · Alta · DevOps.
- [CORE-19 · Crear motor de temas, paletas y variables visuales](https://github.com/CristianGR-DEV/Secure-social-platform/issues/19) — 2027-02-04 a 2027-02-13 · 8 h · Alta · Frontend.
- [CORE-20 · Implementar exportación e importación versionada de ajustes](https://github.com/CristianGR-DEV/Secure-social-platform/issues/20) — 2027-02-14 a 2027-02-20 · 5 h · Media · Frontend.

### Sprint 5 — Personalización avanzada, seguridad y pruebas · 48 h

- [CORE-21 · Implementar distribución personalizable de paneles y navegación](https://github.com/CristianGR-DEV/Secure-social-platform/issues/21) — 2027-02-21 a 2027-03-03 · 12 h · Alta · Frontend.
- [CORE-22 · Reforzar seguridad, autorización y controles contra abuso](https://github.com/CristianGR-DEV/Secure-social-platform/issues/22) — 2027-03-01 a 2027-03-10 · 10 h · Urgente · Security.
- [CORE-23 · Crear pruebas unitarias, integración y flujos críticos E2E](https://github.com/CristianGR-DEV/Secure-social-platform/issues/23) — 2027-03-04 a 2027-03-14 · 12 h · Urgente · Testing.
- [CORE-24 · Validar navegadores principales, responsive y accesibilidad](https://github.com/CristianGR-DEV/Secure-social-platform/issues/24) — 2027-03-10 a 2027-03-17 · 8 h · Alta · Testing.
- [CORE-25 · Preparar entorno de staging, CI y checklist de release](https://github.com/CristianGR-DEV/Secure-social-platform/issues/25) — 2027-03-14 a 2027-03-20 · 6 h · Alta · DevOps.

### Sprint 6 — Despliegue, rendimiento y documentación · 34 h

- [CORE-26 · Desplegar Corebite beta en Ubuntu Server](https://github.com/CristianGR-DEV/Secure-social-platform/issues/26) — 2027-03-21 a 2027-04-02 · 10 h · Urgente · DevOps.
- [CORE-27 · Configurar copias de seguridad, restauración y monitorización](https://github.com/CristianGR-DEV/Secure-social-platform/issues/27) — 2027-03-29 a 2027-04-06 · 8 h · Urgente · DevOps.
- [CORE-28 · Medir y optimizar consultas, archivos y consumo de recursos](https://github.com/CristianGR-DEV/Secure-social-platform/issues/28) — 2027-04-03 a 2027-04-11 · 8 h · Alta · Performance.
- [CORE-29 · Completar documentación técnica, instalación y manual de usuario](https://github.com/CristianGR-DEV/Secure-social-platform/issues/29) — 2027-04-06 a 2027-04-17 · 8 h · Alta · Documentation.

### Cierre — Entrega académica · 12 h

- [CORE-30 · Validar beta, corregir errores y preparar entrega y exposición](https://github.com/CristianGR-DEV/Secure-social-platform/issues/30) — 2027-04-18 a 2027-05-01 · 12 h · Urgente · Release.

## Hitos

| Hito | Fecha objetivo | Resultado |
|---|---|---|
| H1 · Análisis y arquitectura | 2026-11-28 | Requisitos, wireframes, ADR y base técnica |
| H2 · Usuarios operativos | 2026-12-26 | Registro, perfiles, presencia y amistades |
| H3 · Mensajería privada segura | 2027-01-23 | Mensajería E2EE validada en clientes compatibles |
| H4 · Multimedia | 2027-02-20 | Archivos y llamadas de voz 1:1 |
| H5 · Experiencia y pruebas | 2027-03-20 | Personalización, seguridad y pruebas |
| H6 · Beta desplegada | 2027-04-17 | Despliegue, copias de seguridad y documentación |
| H7 · Entrega | 2027-05-01 | Beta validada y presentación |

## Dependencias y riesgos principales

- CORE-03 debe validar la arquitectura Matrix, el modelo de amenazas y el prototipo antes de cerrar decisiones técnicas.
- CORE-11 depende del diseño de arquitectura, la base de datos y la autenticación.
- CORE-12 depende de la búsqueda/contactos y de Synapse.
- CORE-13 depende de Synapse y la integración del SDK.
- CORE-16 depende de la mensajería y del modelo de seguridad.
- CORE-17 depende de la señalización/mensajería; CORE-18 depende de las pruebas de WebRTC.
- CORE-26 depende de Synapse, coturn y staging.
- CORE-27 depende del despliegue.
- CORE-30 depende de las pruebas, seguridad, despliegue y documentación.
- Las fechas pueden cambiar por capacidad del servidor, DNS/dominio, disponibilidad de SMTP, limitaciones de navegadores y recuperación de claves.

## Actualización de este README

Este archivo es la referencia resumida y viva del proyecto. Al avanzar, actualizar el plan cuando cambien alcance, fechas, estimaciones, stack o criterios de éxito. Los detalles de implementación, subtareas y criterios verificables permanecen en los Issues enlazados; GitHub Projects refleja su estado diario.
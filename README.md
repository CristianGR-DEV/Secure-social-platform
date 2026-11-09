# Corebite

Corebite es una plataforma web de comunicación para comunidades cerradas. La beta prioriza chats privados, voz individual y una experiencia sencilla, ligera y protegida mediante seguridad de cuentas, conexiones y datos almacenados. E2EE no forma parte de la beta inicial.

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
5. Proteger cuentas, conexiones y datos almacenados. E2EE queda fuera de la beta y podrá reconsiderarse después.
6. Permitir compartir archivos con validación de tipo y tamaño y autorización de descarga.
7. Entregar llamadas 1:1 mediante WebRTC. Voz en beta; vídeo 1:1 posterior a la beta. Probar relay TURN si la conectividad lo requiere.
8. Ofrecer interfaz adaptable, accesible, en español e inglés, con temas y distribución personalizable.
9. Entregar una beta desplegable, con copias de seguridad, monitorización, documentación y una presentación demostrable.

### Alcance previsto para la beta

- Registro y sesiones seguras.
- Perfiles, avatares, estados de presencia, búsqueda, amistades y bloqueo.
- Interfaz principal y componentes reutilizables.
- Mensajería privada, historial y sincronización sin E2EE en la beta. La persona que administre el servidor podría leer el contenido.
- Archivos y llamadas 1:1; voz está aprobada en beta y el vídeo 1:1 queda fuera de la beta.
- Temas, paletas, ajustes exportables/importables y navegación personalizable; web/PWA en beta y escritorio Linux posterior.
- Pruebas funcionales y de seguridad, compatibilidad de navegadores, diseño responsive y accesibilidad.
- Staging, despliegue en Ubuntu Server, copias de seguridad, restauración, monitorización, optimización y documentación.

### Funciones posteriores

La creación de servidores privados/comunidades, grupos, canales, roles y permisos queda fuera de la primera beta. También quedan para después el cliente de escritorio Linux, las llamadas grupales, compartir pantalla, plugins y procesamiento avanzado de audio/vídeo. La beta prioriza web/PWA, chats privados y voz 1:1; el vídeo 1:1 queda para después de la beta.

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

Tecnologías permitidas para el proyecto: HTML, CSS, JavaScript y PHP. El frontend se desarrollará con HTML/CSS/JavaScript y el backend con PHP. No se incorporarán otros lenguajes de programación. La elección de frameworks PHP, bibliotecas y herramientas compatibles queda pendiente de CORE-03 y debe respetar esta restricción.

| Área | Plan actual |
|---|---|
| Estructura | Por definir en CORE-03 y CORE-04 según la arquitectura PHP |
| Lenguajes | HTML, CSS y JavaScript en el frontend; PHP en el backend |
| Base de datos | PostgreSQL y migraciones iniciales |
| Mensajería | Servicio propio Corebite, priorizando simplicidad y bajo consumo; medir entrega, historial, sincronización y recursos en Ubuntu |
| Cifrado | Sin E2EE en la beta. Aplicar HTTPS, autorización, protección de datos almacenados y registros mínimos; reconsiderar E2EE tras la beta |
| Voz/vídeo | WebRTC para voz 1:1 en beta; vídeo 1:1 posterior a la beta; coturn si las pruebas de red lo requieren |
| Interfaz | Diseño propio inspirado en Discord; web/PWA adaptable en beta; escritorio Linux después; ligereza como objetivo de rendimiento |
| Autenticación | Argon2id para contraseñas, sesiones revocables y cookies HttpOnly, Secure y SameSite en producción |
| Archivos | Almacenamiento fuera del directorio público, validación de contenido/tamaño y descargas autorizadas |
| Infraestructura | Probar primero en el Ubuntu disponible y medir recursos; Docker Compose previsto; comparar alojamiento gestionado después |
| Integración continua | GitHub Actions para instalación y comprobaciones de lint/build |

El framework PHP, la organización del backend, el acceso a PostgreSQL y las herramientas de frontend/PWA deben elegirse y documentarse antes de depender de ellos. Toda elección debe ser compatible con HTML, CSS, JavaScript y PHP. CORE-03 define una arquitectura propia ligera y mide recursos en Ubuntu. La administración del servidor puede leer los mensajes porque la beta no tendrá E2EE; documentar ese límite claramente.

## Principios de seguridad y calidad

- No almacenar contraseñas en texto plano. Usar Argon2id y limitar intentos de autenticación.
- No exponer tokens ni secretos en el navegador, el repositorio o los registros.
- Proteger endpoints y sesiones; revocar sesiones cuando corresponda.
- Validar archivos por tamaño, extensión y tipo real. No confiar solo en el MIME declarado por el cliente.
- Guardar archivos fuera de rutas públicas predecibles y comprobar permisos antes de servirlos.
- La beta no tendrá E2EE: explicar a usuarios que quien administre el servidor puede leer los mensajes. Aplicar HTTPS, hash Argon2id para contraseñas, autorización por conversación, limitación de intentos, cifrado/protección de datos almacenados y copias seguras. Estas medidas no equivalen a E2EE.
- No afirmar E2EE para llamadas de voz o vídeo sin comprobarlo por separado.
- Probar pérdida de sesión, cambio de dispositivo y recuperación de claves; documentar límites.
- Probar restauraciones de backups y proteger las copias y claves.
- Priorizar accesibilidad, navegación por teclado, mensajes de error claros y compatibilidad en navegadores principales.

## Plan de Sprints

La duración prevista es de cuatro semanas por Sprint. El cierre tiene dos semanas. El plan cubre del 1 de noviembre de 2026 al 1 de mayo de 2027.

| Sprint | Fechas | Objetivo | Issues | Estimación |
|---|---|---|---|---:|
| Sprint 1 | 2026-11-01 a 2026-11-28 | Análisis, arquitectura y preparación | CORE-01 a CORE-05 | 40 h |
| Sprint 2 | 2026-11-29 a 2026-12-26 | Interfaz, autenticación y usuarios | CORE-06 a CORE-10 | 50 h |
| Sprint 3 | 2026-12-27 a 2027-01-23 | Mensajería privada y sincronización | CORE-11, CORE-12, CORE-14 y CORE-15 | 35 h provisionales |
| Sprint 4 | 2027-01-24 a 2027-02-20 | Archivos, llamadas y personalización | CORE-16 a CORE-20 | 51 h |
| Sprint 5 | 2027-02-21 a 2027-03-20 | Personalización avanzada, seguridad y pruebas | CORE-21 a CORE-25 | 48 h |
| Sprint 6 | 2027-03-21 a 2027-04-17 | Despliegue, rendimiento y documentación | CORE-26 a CORE-29 | 34 h |
| Cierre | 2027-04-18 a 2027-05-01 | Validación, release y exposición | CORE-30 | 12 h |
| **Total beta** |  |  | **29 Issues planificados + 1 idea posterior** | **270 h provisionales** |

Las 270 horas son una suma provisional tras sacar CORE-13 de la beta. Las estimaciones de CORE-03, CORE-11 y CORE-12 deben revisarse para la solución propia; también hay que confirmar que la PWA queda cubierta. Voz está prevista; el vídeo 1:1 queda para después de la beta y el cliente de escritorio Linux se pospone. Se recomienda reservar un 15–20% para imprevistos. La fecha objetivo de beta es el 1 de mayo de 2027, no una garantía.

## Tareas planificadas

Las fechas por tarea y los valores de Priority, Estimate (h), Iteration y Module también se mantienen en los campos del Project. Las fechas pueden cambiar al revisar cada Sprint.

### Sprint 1 — Análisis, arquitectura y preparación · 40 h

- [CORE-01 · Definir visión, objetivos, alcance y criterios de éxito](https://github.com/CristianGR-DEV/Secure-social-platform/issues/2) — 2026-11-03 a 2026-11-05 · 6 h · Urgente · Análisis.
- [CORE-02 · Definir UX, flujos de usuario y wireframes](https://github.com/CristianGR-DEV/Secure-social-platform/issues/1) — 2026-11-03 a 2026-11-12 · 8 h · Alta · Análisis.
- [CORE-03 · Diseñar y probar arquitectura ligera de mensajería propia](https://github.com/CristianGR-DEV/Secure-social-platform/issues/3) — 2026-11-05 a 2026-11-15 · 10 h provisionales · Urgente · Arquitectura.
- [CORE-04 · Preparar repositorio, estructura de trabajo, HTML/CSS/JavaScript, PHP y CI inicial](https://github.com/CristianGR-DEV/Secure-social-platform/issues/4) — 2026-11-09 a 2026-11-18 · 8 h · Urgente · DevOps.
- [CORE-05 · Diseñar modelo de datos PostgreSQL y migraciones iniciales](https://github.com/CristianGR-DEV/Secure-social-platform/issues/5) — 2026-11-16 a 2026-11-28 · 8 h · Alta · Database.

### Sprint 2 — Interfaz, autenticación y usuarios · 50 h

- [CORE-06 · Crear sistema de diseño y estructura de la interfaz principal](https://github.com/CristianGR-DEV/Secure-social-platform/issues/6) — 2026-11-29 a 2026-12-09 · 10 h · Alta · Frontend.
- [CORE-07 · Implementar registro, inicio/cierre de sesión y gestión de sesiones](https://github.com/CristianGR-DEV/Secure-social-platform/issues/7) — 2026-11-29 a 2026-12-14 · 16 h · Urgente · Backend.
- [CORE-08 · Implementar perfiles, avatares y estados de presencia](https://github.com/CristianGR-DEV/Secure-social-platform/issues/8) — 2026-12-08 a 2026-12-17 · 9 h · Alta · Frontend.
- [CORE-09 · Configurar internacionalización inicial en español e inglés](https://github.com/CristianGR-DEV/Secure-social-platform/issues/9) — 2026-12-10 a 2026-12-19 · 7 h · Media · Frontend.
- [CORE-10 · Implementar búsqueda de usuarios, amistades y bloqueo](https://github.com/CristianGR-DEV/Secure-social-platform/issues/10) — 2026-12-16 a 2026-12-26 · 8 h · Alta · Backend.

### Sprint 3 — Mensajería privada y sincronización · 35 h provisionales

- [CORE-11 · Preparar el servicio propio de mensajería Corebite](https://github.com/CristianGR-DEV/Secure-social-platform/issues/11) — 2026-12-27 a 2027-01-05 · 10 h provisionales · Urgente · Backend.
- [CORE-12 · Implementar mensajería privada Corebite en tiempo real](https://github.com/CristianGR-DEV/Secure-social-platform/issues/12) — 2027-01-02 a 2027-01-11 · 12 h provisionales · Urgente · Frontend.

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

## Ideas posteriores a la beta

- [CORE-13 · Evaluar E2EE para una versión posterior](https://github.com/CristianGR-DEV/Secure-social-platform/issues/13) — estado Ideas; no tiene Sprint, fechas ni estimación.
- [CORE-31 · Diseñar comunidades privadas y permisos personalizables](https://github.com/CristianGR-DEV/Secure-social-platform/issues/31) — fase posterior a beta, estado Ideas y sin Sprint/estimación. Creación por cualquier cuenta; entrada por enlace autorizado de un solo uso que caduca en 24 horas, acceso inmediato y rol base automático; roles personalizables y múltiples por miembro, con rol base que permite ver canales accesibles, escribir, subir archivos y conectarse a voz; denegar prevalece. Los permisos se aplican en general con excepciones por canal. Los roles usan niveles numéricos definidos por el propietario: cuanto menor el número, mayor la autoridad; se permiten roles empatados y un administrador no puede gestionar roles de su mismo nivel ni superiores, ni el rol del propietario. Un miembro puede salir cuando quiera; la comunidad desaparece de su vista y pierde acceso. Sus mensajes y archivos permanecen visibles para los demás y, si vuelve mediante una invitación nueva, también recupera la visibilidad de ese contenido; su pertenencia comienza de nuevo y no recupera los permisos anteriores. Solo propietario y administradores autorizados pueden expulsar o suspender. Los permisos administrativos son independientes y asignables a roles: crear/editar/eliminar canales; crear/compartir invitaciones; eliminar mensajes con invitaciones para invalidarlas; expulsar/suspender miembros; crear/editar/eliminar roles; asignar/quitar roles respetando la jerarquía; cambiar ajustes generales como nombre e imagen; La moderación incluye borrar mensajes de otras personas para todos, borrar adjuntos conservando el mensaje cuando sea posible, fijar/desfijar mensajes, bloquear temporalmente un canal y revisar/resolver reportes. Cualquier miembro que pueda ver el contenido puede reportarlo, seleccionando una categoría obligatoria y añadiendo un comentario opcional. El contenido reportado permanece visible para todos hasta que una persona moderadora actúe. Quien envía el reporte no recibe notificación ni información sobre su resolución. El reporte es anónimo incluso para el equipo moderador, que no ve la identidad de quien reporta. Para limitar abusos, Corebite vincula temporalmente la cuenta y el reporte; además mantiene un contador de reportes por miembro y comunidad. Los administradores pueden ver el total de cada miembro, pero no saber qué reportes envió. Corebite usará temporalmente la identidad para aplicar un límite máximo por periodo y después borrará el vínculo entre usuario y reporte; el límite es de 3 reportes por miembro y comunidad; el contador se reinicia diariamente a medianoche en la zona horaria elegida por el propietario en los ajustes; inicialmente usa la zona de quien creó la comunidad. Si el propietario cambia la zona, el cambio es inmediato y el contador diario se reinicia. Si el reporte trata sobre un moderador, ese moderador no puede verlo: lo revisa otro moderador o, si no hay otro, el propietario. Si el reporte trata sobre el propietario, lo pueden revisar el propietario o los administradores. La decisión de moderación es definitiva y no puede apelarse. Si se aplica una medida, la persona autora del contenido recibe un aviso con la regla incumplida y la medida aplicada; nunca se revela quién reportó. El propietario debe transferir la propiedad a un administrador antes de salir. La transferencia es inmediata al elegir al nuevo propietario; se le notifica después. El antiguo propietario permanece como miembro con el rol base y sin permisos de administración. El nuevo propietario podrá volver a asignarle cualquier rol que tenga autoridad para gestionar.
- Vídeo 1:1 y cliente de escritorio Linux.

## Hitos

| Hito | Fecha objetivo | Resultado |
|---|---|---|
| H1 · Análisis y arquitectura | 2026-11-28 | Requisitos, wireframes, ADR y base técnica |
| H2 · Usuarios operativos | 2026-12-26 | Registro, perfiles, presencia y amistades |
| H3 · Mensajería privada | 2027-01-23 | Mensajería privada operativa y sincronización fiable |
| H4 · Multimedia | 2027-02-20 | Archivos, voz 1:1 en beta y vídeo 1:1 posterior |
| H5 · Experiencia y pruebas | 2027-03-20 | Personalización, seguridad y pruebas |
| H6 · Beta desplegada | 2027-04-17 | Despliegue, copias de seguridad y documentación |
| H7 · Entrega | 2027-05-01 | Beta validada y presentación |

## Dependencias y riesgos principales

- CORE-03 debe validar la arquitectura propia, el modelo de amenazas y el prototipo antes de cerrar decisiones técnicas.
- CORE-11 depende del diseño de arquitectura, la base de datos y la autenticación.
- CORE-12 depende de la búsqueda/contactos y del servicio propio preparado en CORE-11.
- CORE-13 queda fuera de la beta en Ideas, sin Sprint, fechas ni estimación; reconsiderar después de la beta.
- CORE-16 depende de la mensajería y del modelo de seguridad.
- CORE-17 depende de la señalización/mensajería; CORE-18 depende de las pruebas de WebRTC.
- CORE-26 depende del servicio propio, coturn y staging.
- CORE-27 depende del despliegue.
- CORE-30 depende de las pruebas, seguridad, despliegue y documentación.
- Las fechas pueden cambiar por capacidad del servidor, DNS/dominio, disponibilidad de SMTP, limitaciones de navegadores y recuperación de claves.

## Actualización de este README

Este archivo es la referencia resumida y viva del proyecto. Al avanzar, actualizar el plan cuando cambien alcance, fechas, estimaciones, stack o criterios de éxito. Los detalles de implementación, subtareas y criterios verificables permanecen en los Issues enlazados; GitHub Projects refleja su estado diario.

## Preferencias confirmadas por el propietario (actualizadas al 2026-11-09)

Estas respuestas reemplazan cualquier supuesto inicial que no coincida.

- **Usuarios y propósito:** comunidad cerrada; el uso principal es hablar en privado con amistades y controlar los propios datos/servidor.
- **UX y estilo:** en la primera etapa, acercarse lo máximo posible a los flujos y estética de Discord, manteniendo marca Corebite. Más adelante se desarrollará un estilo propio. Interfaz minimalista, oscura por defecto, con modo claro y temas personalizables. Se busca la ligereza y fiabilidad de TeamSpeak clásico.
- **Cuentas y contactos:** usuario y contraseña; en beta comprobar que nombre de usuario y correo no estén ya registrados, sin verificar ni enviar correos. La verificación por correo se evaluará después de la beta. Mientras tanto, se podrá cambiar la contraseña desde una sesión activa; si se pierde el acceso, no habrá recuperación en beta. Búsqueda por nombre de usuario y, en comunidades futuras, búsqueda de miembros. Aceptar/rechazar solicitudes, bloquear, eliminar amistades, ocultar perfil de búsquedas y reportar cuentas o mensajes. Cuando se bloquea, ambas personas conservan lectura del historial, el chat muestra un aviso de bloqueo y ninguna puede escribir. La persona bloqueada no ve el nombre ni la foto de perfil de quien la bloqueó y recibe un avatar genérico «user». La amistad queda suspendida durante el bloqueo; al desbloquear, se restauran perfil e identidad y se puede volver a escribir en la conversación existente.
- **Solicitudes de amistad:** cualquier cuenta puede enviar solicitudes. No caducan y quien las envía no puede cancelarlas; quedan pendientes hasta que la persona destinataria acepte o rechace. Límite inicial: 5 nuevas solicitudes al día y 7 días antes de volver a solicitar a la misma persona.
- **Mensajes e historial:** mensajería en tiempo real, historial y sincronización. Solo se muestra «Enviado» cuando el servidor acepta el mensaje; no hay confirmación de entrega ni de lectura. Se permite editar/borrar para todas las personas durante los primeros 5 minutos; después el mensaje permanece visible para ambas y no se puede ocultar solo para una persona. Si se bloquea el chat, ambas personas pueden leerlo pero ninguna puede enviar mensajes; aparece un aviso de bloqueo. Al desbloquear, si la amistad estaba aceptada, se restaura la conversación existente y se puede escribir de nuevo. No hay caducidad automática.
- **Borrado de conversación:** quitarla de la bandeja la oculta para esa persona. El servidor elimina el historial cuando ambas personas la hayan quitado de sus propias bandejas.
- **Sesiones y seguridad:** varios dispositivos con la misma cuenta/sesión y sesiones revocables. No se envían correos de verificación, recuperación ni aviso de dispositivo nuevo en la beta. La contraseña se puede cambiar desde una sesión activa; si se pierde el acceso, no habrá recuperación hasta diseñar la verificación posterior. Sin E2EE en beta. Se guardará solo lo necesario; el historial se protegerá en almacenamiento y habrá registros técnicos limitados. Esto no equivale a E2EE.
- **E2EE posterior:** explorar E2EE para mensajes privados después de la beta, con recuperación de historial mediante clave o copia protegida. Los archivos quedan fuera de esa decisión por ahora.
- **Archivos:** se pueden enviar todos los tipos de archivo hasta 50 MB; todos se descargan como adjuntos con su nombre original. Excepciones con vista previa: imágenes y vídeos; TXT hasta 1 MB (TXT mayor se descarga completo). Los vídeos solo se reproducen cuando la persona pulsa. Se puede borrar el archivo para ambas personas durante 5 minutos; después no. Al borrarlo, la línea se conserva con el aviso «Archivo eliminado». Mostrar el nombre original con caracteres peligrosos saneados, sin usarlo como ruta del servidor. Los adjuntos se eliminan cuando ambas personas quitan la conversación. El servidor nunca ejecuta ni abre los archivos.
- **Llamadas:** voz 1:1 en beta; vídeo 1:1 después.
- **Notificaciones:** nuevas conversaciones/mensajes, solicitudes de amistad y llamadas entrantes; usar avisos dentro de Corebite y del navegador según preferencias de cada persona. Las vistas previas del navegador muestran solo el nombre y que llegó un mensaje, nunca su contenido. Sonido activable y silenciable.
- **Interfaz y accesibilidad:** ordenador, tablet y móvil mediante navegador. Navegación por teclado, compatibilidad con lectores de pantalla y texto ampliable.
- **Personalización:** tema claro/oscuro, paletas, distribución de paneles, tamaño de texto e importar/exportar ajustes.
- **Infraestructura:** servidor Ubuntu e IP pública dinámica disponibles; el usuario puede configurar router y firewall. No hay dominio registrado. Comparar coste total y opciones de alojamiento; SMTP queda fuera de beta.
- **Tecnología de mensajería:** servicio propio Corebite para beta, sin Matrix/Synapse; se quiere una explicación comparativa de ventajas y límites de Matrix.
- **Tecnologías permitidas:** HTML, CSS, JavaScript y PHP. El frontend será HTML/CSS/JavaScript y el backend PHP; no se añadirán otros lenguajes. Framework PHP y herramientas compatibles quedan pendientes de decisión.
- **Comunidades futuras:** servidores privados, canales, roles y permisos posteriores, con un modelo inspirado en Discord. La jerarquía usa niveles numéricos definidos por el propietario: los números bajos representan más autoridad y se permiten niveles compartidos. Las invitaciones se publican como enlaces en mensajes; borrar el mensaje invalida el enlace, incluso si se compartió en otra comunidad. El creador puede borrar su mensaje y los administradores pueden eliminar mensajes con invitaciones de su comunidad. Tras transferir la propiedad, el anterior propietario permanece como miembro con rol base, pero el nuevo propietario puede volver a asignarle roles que tenga autoridad para gestionar.
- **Linux:** integración nativa como objetivo; cliente de escritorio Linux posterior a la beta.
- **Código y repositorio:** Corebite no se publicará como producto de código abierto. El repositorio se mantiene público por la decisión previa.
- **Fecha y éxito de beta:** mantener 2027-05-01. Beta terminada significa funciones principales, criterios verificados, despliegue externo, backups/restauración probados, documentación y presentación. Si hay que recortar, priorizar UX.
- **Plan estimado:** 270 h provisionales. Comparar alojamiento y confirmar dominio, DNS dinámico y acceso externo antes del despliegue. SMTP se decidirá después de la beta.

### Comparativa inicial de alojamiento (referencia: 2026-11-03)

| Opción | Coste/servicio de referencia | Ventajas | Límites a comprobar |
|---|---|---|---|
| Ubuntu propio disponible | Sin alquiler adicional de servidor; calcular electricidad, conexión, mantenimiento y backup | Reutiliza hardware, control completo y útil para medir rendimiento real | Cortes eléctricos o de red, acceso entrante, IP fija/dinámica, router/ISP, seguridad y restauración |
| VPS europeo Hetzner CPX22 | 2 vCPU, 4 GB RAM, 80 GB NVMe; 19,49 €/mes antes de IVA y coste adicional de IPv4 | Servidor público estable y escalable; menos dependencia de la red doméstica | Es un VPS que seguimos administrando; añadir copias, IPv4, IVA y almacenamiento si se requiere |
| VPS DigitalOcean Basic | 2 vCPU, 4 GB RAM, 80 GB SSD; 24 USD/mes, con 4.000 GiB de transferencia | Configuración sencilla, precio mensual claro y región elegible | También es autogestionado; revisar impuestos, región, backups y cargos adicionales |
| Plataforma gestionada Render | Precio por servicio, base de datos, almacenamiento y transferencia; web services con TLS/dominio/WebSockets | Reduce tareas del sistema operativo y ofrece servicios gestionados | Sumar aplicación + PostgreSQL + disco/tráfico; probar WebRTC/TURN y límites antes de adoptarla |

**Propuesta provisional:** desarrollar y medir primero en Ubuntu propio. Para una beta accesible externamente, comparar su coste total y fiabilidad con un VPS europeo o una plataforma gestionada. No elegir proveedor hasta medir estabilidad de la conexión, consumo eléctrico y necesidades reales de CPU/RAM/almacenamiento. Los precios son referencias publicadas y pueden cambiar; confirmar en el checkout.

**Dominio, IP y acceso externo pendientes:**
- No hay dominio registrado; se probará primero con DNS dinámico sobre la IP pública cambiante, y se registrará un dominio antes de abrir la beta al público. El usuario puede configurar router/firewall. Cloudflare puede gestionar DNS, pero no registra dominios.
- Para las primeras pruebas desde Internet en el Ubuntu propio, configurar DNS dinámico para que el registro apunte a la IP cambiante; abrir solo los puertos necesarios y probar desde una red externa. Registrar el dominio antes del lanzamiento público.
- Revisar con el ISP si existe CGNAT aunque la configuración local del router sea posible. Configurar port forwarding de 80/443 al proxy HTTPS; la voz WebRTC puede requerir TURN/UDP.
- En la beta no se enviarán correos ni se verificará el correo. Solo se comprueba que nombre de usuario y correo sean únicos. Tras la beta se decidirán proveedor SMTP, verificación, recuperación y alertas; entonces configurar SPF, DKIM y DMARC.
- La voz WebRTC puede requerir un relay TURN; validarlo con el Issue CORE-18 y medir tráfico/costes.
- Fórmula para estimar electricidad del servidor: vatios medios medidos × horas de uso × tarifa eléctrica; no usar el consumo máximo de la fuente como gasto medio.

Fuentes oficiales de la referencia: [Hetzner Cloud y tarifas](https://docs.hetzner.com/general/infrastructure-and-availability/price-adjustment/), [DigitalOcean Droplets](https://www.digitalocean.com/pricing/droplets), [Render Pricing](https://render.com/pricing), [Brevo](https://help.brevo.com/hc/en-us/articles/208589409-About-Brevo-s-pricing-plans) y [gestión DNS de Cloudflare](https://developers.cloudflare.com/dns/manage-dns-records/).

Los reportes de mensajes incluyen la categoría, un comentario y una copia del mensaje reportado para que administración revise el caso. Si el nombre de usuario o correo ya existe, el registro muestra un mensaje genérico que no revela cuál dato está duplicado.

### Seguridad sin E2EE

HTTPS protege la conexión; Argon2id, autorización por conversación, límites de uso, protección del almacenamiento y copias seguras reducen riesgos. No impiden que quien administre el servidor lea mensajes sin E2EE. Mostrar este límite. La protección criptográfica del historial almacenado se describe por separado de E2EE.

### Ideas posteriores a la beta

- [CORE-13 · Evaluar E2EE para mensajes privados](https://github.com/CristianGR-DEV/Secure-social-platform/issues/13) — estudiar recuperación/dispositivos; E2EE de archivos queda fuera de la decisión actual.
- Servidores privados, roles, permisos y canales; al salir, la membresía y permisos se reinician si se invita de nuevo. El contenido anterior permanece para los miembros y vuelve a ser visible para quien reingresa, aunque se reinicien sus permisos.
- Vídeo 1:1 y cliente de escritorio Linux.

### Material local

La carpeta de trabajo contiene páginas HTML para inicio, sesión, cuenta y términos, hojas CSS e imágenes. No encontré JavaScript, backend ni manifiesto de dependencias. Rediseñar y limpiar antes de subirlo al repositorio; la página inicial tiene textos de ejemplo y algunas rutas incompletas.

# Guía de estilos de Corebite

**Documento vivo.** Actualizar esta guía y `Media/CSS/design-system.css` cada vez que cambien los estilos, se añada un componente visual o se introduzca una nueva pantalla. La hoja de diseño compartida se carga al final de los CSS específicos para mantener una apariencia común sin borrar las necesidades de cada vista.

## Identidad visual

Corebite combina fondos azul carbón, superficies índigo, acentos lavanda y azul, bordes finos punteados y degradados suaves. En las tarjetas de pasos de la landing, el hover eleva el contenido mientras mantiene fijos y alineados los números, sus subrayados y las líneas punteadas. El landing y los formularios llevan un campo ASCII animado como fondo: ondas de caracteres lavanda/índigo legibles pero discretas, ancladas al documento para que se desplacen con la página y activas solo mientras la pestaña está visible. Debe ignorarse con lectores de pantalla y congelarse con movimiento reducido. Las pequeñas ilustraciones ASCII decorativas usan el mismo ritmo lento. Los títulos principales de la aplicación se recomponen desde símbolos ASCII al entrar en pantalla, de forma visible y una sola vez, con la tipografía habitual y el texto original accesible. La interfaz debe sentirse cercana, clara y ligera. Los efectos sirven para orientar la atención; el contenido y la lectura siempre tienen prioridad.

## Tokens compartidos

Los valores de referencia viven como variables `--cb-*` en `Media/CSS/design-system.css`.

| Token | Valor | Uso |
|---|---|---|
| `--cb-bg` | `#10141c` | Fondo principal |
| `--cb-bg-raised` | `#171a23` | Fondo secundario |
| `--cb-surface` | `#1b1a28` | Superficie de panel |
| `--cb-surface-raised` | `#252437` | Superficie elevada o seleccionada |
| `--cb-text` | `#f7f4fc` | Texto principal |
| `--cb-text-secondary` | `#c4c0cf` | Texto secundario |
| `--cb-text-muted` | `#9693a2` | Ayudas, metadatos y estados discretos |
| `--cb-accent` | `#7966ac` | Lavanda principal |
| `--cb-accent-blue` | `#586bd1` | Azul del degradado de acción |
| `--cb-accent-soft` | `#ac9bdb` | Foco, iconos y estados activos |
| `--cb-accent-bright` | `#c3b1ed` | Acento con contraste alto |
| `--cb-line` | `rgba(181,171,220,.22)` | Bordes punteados |
| `--cb-choice-hover-bg` | `rgba(172,155,219,.08)` | Fondo de tarjetas de opción al pasar el cursor |
| `--cb-choice-selected-bg` | `rgba(172,155,219,.14)` | Fondo de la tarjeta de opción seleccionada |
| `--cb-radius-sm/md/lg` | `8 / 12 / 16 px` | Campos, tarjetas y paneles |
| `--cb-font` | fuentes del sistema UI | Tipografía rápida y disponible localmente |

Las variables heredadas `--color-primario`, `--color-terciario`, `--fondo-cristal` y `--borde-punteado` apuntan a los tokens compartidos para que las páginas antiguas mantengan la misma paleta.

## Componentes

- **Navegación:** barra oscura translúcida, fija en la parte superior en páginas públicas; marca a la izquierda y acciones de cuenta a la derecha.
- **Acciones principales:** degradado lavanda-azul, borde claro, radio de 8 px y elevación suave al pasar el cursor. Los botones deshabilitados pierden brillo y movimiento.
- **Acciones secundarias:** texto claro o superficie elevada; mantener diferencia visual respecto a la acción principal.
- **Campos:** superficie oscura, borde fino punteado y foco lavanda con halo visible. Etiquetas claras encima del campo. El selector de fecha y los menús de mes/año son propios para conservar el tema oscuro y lavanda en todos los navegadores; el calendario se muestra sobre la capa superior para evitar solapamientos. Los campos de contraseña llevan un control de ojo accesible de 38 px dentro del lado derecho.
- **Tarjetas y diálogos:** degradado índigo oscuro, borde punteado lavanda y sombra amplia de baja opacidad.
- **Footer:** fondo carbón con línea superior punteada, texto secundario y enlaces lavanda; columnas centradas y responsive.
- **Iconos:** SVG monocromos de `Media/SVG/`, teñidos con `currentColor` cuando sea posible. Mantener tamaños consistentes y etiquetas accesibles.
- **Ajustes de chat:** cabecera de perfil con degradado tenue; preferencias agrupadas con títulos y divisores punteados; usar tarjetas con muestras visuales para estilo, tamaño y tipografía, y reservar el control de color para la entrada de color nativa.

## Pantallas y temas

| Vista | Apariencia |
|---|---|
| Landing | Fondo carbón con brillos lavanda/azules; héroe amplio, vista previa del chat y tarjetas con bordes punteados |
| Inicio de sesión y registro | Mismo header/footer que el landing; formulario como panel elevado, controles y CTA compartidos; título con aparición ASCII |
| Cuenta y personalización | Diálogo de ajustes con cabecera de perfil, navegación lateral y cierre de sesión; el perfil permite revelar datos privados, editar nombre/foto/presentación/actividad y ofrece una ficha pública para los miembros; paletas en tarjetas seleccionables, tarjeta personalizada con controles de color y esquemas gráficos para las posiciones izquierda/derecha de canales y servidores, y tamaños normal/reducido para la tarjeta de chats privados; selector de presencia sobre el avatar y X roja de cierre centrada y visible en la esquina superior derecha |
| Términos | Lectura en una sola página, tarjeta elevada con bordes redondeados y espacio visible entre header, contenido y footer; título con aparición ASCII |
| Error 404/500 | Pantalla limpia sin tarjeta ni navegación, código en tipografía de títulos con disolución ASCII, escáner ASCII y un único botón al landing |
| Chat | Distribución propia del producto; tema inicial `discord` usa la paleta de marca. Se conservan los temas alternativos `midnight` y `light`, y los ajustes de apariencia por conversación; los ajustes individuales usan tarjetas visuales sin desplegables, X roja de cierre y adaptación a móvil; títulos principales con aparición ASCII |

## Responsive y accesibilidad

- Mantener el ancho mínimo de 320 px y evitar desplazamiento horizontal.
- Usar diseños fluidos (`minmax`, `clamp`, porcentajes) y apilar columnas cuando el contenido deje de caber.
- Los breakpoints existentes se agrupan en torno a 900, 760/768, 640/600, 520, 420 y 360 px; al crear uno nuevo, explicar qué distribución resuelve.
- El foco de teclado debe permanecer claramente visible; los controles han de tener etiquetas y áreas de toque cómodas.
- Respetar `prefers-reduced-motion`; nunca depender de animación para comunicar estado.
- Mantener contraste suficiente entre texto y superficie, especialmente en texto secundario y estados deshabilitados.

## Cómo actualizar el sistema

1. Añadir o ajustar un token `--cb-*` en `design-system.css` en lugar de repetir un color o radio nuevo.
2. Aplicar el token al componente y conservar las reglas de distribución propias de la pantalla.
3. Si aparece una nueva pantalla, enlazar el CSS compartido al final de su `<head>`.
4. Actualizar esta guía y el registro de cambios antes de dar por terminada la modificación visual.

## Registro de cambios

- **2026-11-13:** primera guía compartida y capa visual común. Unifica paleta, tipografía, botones, campos, superficies, estados de foco y footer; aplica la paleta de marca al tema inicial del chat sin quitar los temas alternativos.
- **2026-11-13:** mejora el selector de fecha, añade controles para mostrar u ocultar contraseñas y ajusta la separación y el acabado de la tarjeta de términos.
- **2026-11-13:** sustituye el popup de fecha nativo por un calendario propio con menús de mes/año de estilo Corebite, selección de fechas y navegación en la capa superior; amplía el espacio vertical de la tarjeta de términos.
- **2026-11-13:** separa el hover de las tarjetas de pasos: el contenido conserva su elevación y los números, subrayados y bordes punteados quedan fijos; añade el subrayado que faltaba bajo el tercer número.
- **2026-11-13:** añade ilustraciones ASCII de nodos al landing y a los formularios de acceso con animación de flotación y brillo, sin movimiento cuando el dispositivo lo solicita.
- **2026-11-13:** añade un fondo ASCII animado de caracteres ondulantes a la landing, acceso/registro y cuenta; se pausa en pestañas ocultas y respeta el ajuste de movimiento reducido.
- **2026-11-13:** añade un efecto de disolución ASCII de una sola reproducción a los títulos grandes del landing, activado al entrar en pantalla y accesible sin animación cuando se reduce el movimiento.
- **2026-11-13:** sustituye la tarjeta anterior por una pantalla 404/500 limpia a pantalla completa; muestra el código en ASCII con disolución, un escáner animado y un botón de regreso. El enrutador sirve 404 y los errores PHP no controlados muestran 500 sin detalles internos.
- **2026-11-13:** afina la página de error con cifras rectangulares de bloques que se recomponen como partículas ASCII, el escáner `█······` de la referencia, una trama de fondo más visible y el botón con el acabado del landing.
- **2026-11-13:** centra la flecha gruesa del botón «Volver al inicio» y ajusta el tamaño de los números para acercarlos a la tipografía geométrica de referencia.
- **2026-11-13:** cambia los números ASCII del error por la tipografía habitual de los títulos, con el efecto de disolución ASCII al aparecer.
- **2026-11-13:** extiende la aparición ASCII de títulos a acceso/registro, cuenta, términos y los encabezados principales del chat, manteniendo el texto accesible y el ajuste de movimiento reducido.
- **2026-11-13:** cambia la paleta del panel de personalización por tarjetas de temas y añade una tarjeta personalizada con selectores de fondo, paneles, acento y texto; guarda la selección y centra la X del diálogo, dejando espacio para la marca.
- **2026-11-13:** sustituye los selectores desplegables de posición por tarjetas con diagramas izquierda/derecha para el menú de canales y la barra de servidores, conservando la preferencia guardada.
- **2026-11-13:** adapta el diálogo de ajustes al diseño de la página de cuenta con cabecera de perfil, navegación lateral y un botón de cierre de sesión protegido por CSRF.
- **2026-11-13:** elimina la franja superior interna del contenido de ajustes, fija una X visible en la esquina superior derecha y lleva el desplazamiento vertical al borde derecho del panel.
- **2026-11-13:** al ocultar la lista de miembros también se oculta su panel de cuadrícula; elimina el alto mínimo del espacio de chat para que no se recorte en pantallas bajas. Se conserva la adaptación de columnas para escritorio, tablet y móvil.
- **2026-11-13:** separa las opciones de colores, distribución y tamaño con títulos y líneas punteadas; deja dos tamaños para la tarjeta privada (normal por defecto y reducido con perfil/controles en una fila), guarda la selección y ajusta el texto de las tarjetas para que no desborde.
- **2026-11-13:** añade la edición persistente del perfil público, revelado explícito de datos personales y fichas públicas accesibles desde la lista de miembros y los chats privados, indicando qué campos son visibles para otros.
- **2026-11-13:** rediseña los ajustes individuales de chat con cabecera inspirada en ajustes de cuenta, secciones punteadas y tarjetas visuales para estilo, tamaño y tipografía; conserva el selector nativo de color y las preferencias por conversación.
- **2026-11-13:** hace visible y roja la X de cierre en la esquina superior derecha de los ajustes de cuenta y de cada chat.
- **2026-11-13:** eleva la X de los ajustes por encima de la cabecera fija para que no quede tapada y pueda recibir clics.
- **2026-11-13:** aumenta el contraste y la densidad de caracteres del fondo ASCII en landing y acceso/registro; el canvas cubre la altura completa y se desplaza con el documento en lugar de permanecer fijo al viewport.
- **2026-11-13:** corrige la capa y la proporción circular de los indicadores de presencia; añade al avatar propio un menú para elegir entre en línea, ausente, no molestar e invisible, guarda la preferencia en la cuenta y la muestra a otros miembros.
- **2026-11-13:** unifica el diseño de las barras de desplazamiento con carril oscuro y deslizador fino en tonos de acento; corrige las fotos de avatar para que llenen el círculo sin deformarse, oculten la inicial cuando hay foto y no muestren un fondo morado detrás.

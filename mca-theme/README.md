# Master Center Américas — tema de WordPress

Tema a medida construido sobre el diseño `MCA-WEB.pdf`. La portada es la landing
completa; cada sección vive en su propio archivo y su propia hoja de estilos.

## Instalación

1. Copia la carpeta `mca-theme/` a `wp-content/themes/`.
2. **Apariencia → Temas → Activar**.
3. **Ajustes → Lectura**: deja «Tu página de inicio muestra» como prefieras.
   `front-page.php` se usa igual en ambos casos. Si eliges «Una página
   estática», asigna también la «Página de entradas» para que el archivo del
   blog use `index.php`.
4. **Apariencia → Menús**: crea un menú y asígnalo a *Navegación principal*.
   Sin menú asignado el header muestra los cuatro enlaces del diseño.
5. **Apariencia → Personalizar → MCA — Contacto**: teléfono, correo, WhatsApp,
   destino del botón «Agenda tu reunión», redes sociales y el destino del
   formulario.

## Qué es editable sin tocar código

Todo el contenido de la portada se edita desde el escritorio. Nada de texto,
imágenes ni iconos vive ya en las plantillas.

### Textos e imágenes sueltos

**Apariencia → Personalizar → MCA — Contenido**, una sección por bloque:

| Sección | Qué contiene |
|---|---|
| Cabecera | texto del botón «Agenda tu reunión» |
| Portada (hero) | titular, párrafo, textos y destinos de los dos botones, imagen principal y fondo decorativo |
| Cifras | fotografía de fondo |
| Quiénes somos | antetítulo, título y los dos párrafos |
| Soluciones | título de la sección |
| Clientes | título de la sección |
| Por qué trabajar con nosotros | título del panel y fotografía lateral |
| Metodología | título de la sección y la palabra antes del número («Paso») |
| Blog | título de la sección y texto del enlace de cada tarjeta |
| Llamada a la acción final | título, párrafo y textos de los botones |
| Formulario de reunión | antetítulo, título, texto introductorio y botones de enviar y cancelar |
| Contacto | teléfono, correo, WhatsApp, destino del CTA, LinkedIn, Facebook, título y texto del pie, destino del formulario |

**WhatsApp se controla desde dos campos de la sección Contacto** y alimenta los
**tres** botones: el de la portada, el del CTA y el flotante de la esquina.

| Campo | Qué lleva |
|---|---|
| Enlace de WhatsApp | solo el número: `https://wa.me/51XXXXXXXXX` |
| Mensaje con el que llega el chat | el texto que se escribe solo en el chat de quien pulsa |

No pongas `?text=` dentro del enlace: el campo de mensaje lo sustituye. Si vacías
el mensaje, el chat abre en blanco; si vacías el enlace, los tres botones
desaparecen.

Los nombres y los ejemplos de los campos del formulario (Nombres completos,
Correo, Celular…) siguen en el código: definen qué datos se recogen, no son
copy de marketing.

El único texto visible que no es editable es «Saltar al contenido», el enlace
de accesibilidad que sólo aparece al navegar con teclado.

### Listas repetibles

Cada colección es un tipo de contenido propio **en el menú lateral del
escritorio**, no en el Personalizador: el Customizer de WordPress no sabe editar
listas. Para que eso no se lea como «faltan campos», las cinco secciones
afectadas del Personalizador llevan un botón que abre la pantalla correcta en
una pestaña nueva.

El orden se controla con el campo «Orden» de la caja **Atributos**.

| Menú | Qué es | Campos propios |
|---|---|---|
| MCA · Servicios | las tarjetas de Soluciones | icono, descripción |
| MCA · Cifras | las tarjetas de estadísticas | número que se anima, icono, descripción |
| MCA · Metodología | los pasos del ciclo | icono — el «Paso N» se numera solo |
| MCA · Por qué nosotros | las columnas del panel oscuro | descripción |
| MCA · Clientes | los logos | imagen destacada, altura óptica, agrupar en fila |
| MCA · Clientes → Sectores | los paneles que agrupan logos | estilo de la etiqueta, orden del panel |

El icono se elige de una lista cerrada de 15; añadir uno nuevo requiere el PNG
en `assets/img/icons`, su regla en `assets/css/icons.css` y una entrada en
`mca_icons()` de `inc/content.php`.

**Altura óptica de un logo**: cada marca pesa distinto a igual altura, así que
lleva su propio tope en píxeles. Punto de partida: `62 / raíz(ancho/alto)`.
Los once logos que trae el tema quedaron hasta 8 px fuera de esa curva, o sea
que es un punto de partida, no la respuesta: ajusta a ojo.

### Lo demás

| Dato | Dónde |
|---|---|
| Logo | Personalizar → Identidad del sitio |
| Nombre del sitio (sale en el pie) | Ajustes → Generales |
| Menú | Apariencia → Menús |
| Artículos del blog | Entradas — la portada toma las 3 más recientes |

### Contenido inicial

El texto que traía la maqueta se escribe en la base de datos una sola vez, la
primera vez que se entra al escritorio con el tema activo (`inc/seed.php`).
A partir de ahí el sembrador no vuelve a tocar nada: si borras una tarjeta,
sigue borrada.

## El formulario todavía no envía

`form_endpoint` está vacío a propósito. Mientras lo esté, el formulario avisa
al usuario que no está conectado **en lugar de simular un envío exitoso**.
Apunta ese campo a una ruta REST, a `admin-ajax.php` o a un plugin de
formularios y empezará a enviar de verdad.

## Estructura

```
front-page.php          compone las 9 secciones en orden
header.php / footer.php cabecera, pie, sprite de iconos y modal
template-parts/
  section-*.php         una por sección del diseño
  post-card.php         tarjeta de artículo (portada y archivo)
  modal-reunion.php     formulario de solicitud de reunión
  icon-sprite.php       los 2 SVG que el set de marca no trae
inc/customizer.php      campos de contacto
assets/css/             una hoja por sección, encoladas en cadena
assets/js/main.js       header fijo, menú móvil, carruseles, contador, modal
```

Las hojas se encolan con dependencias explícitas, no por orden de registro: la
cascada importa (los componentes definen valores por defecto que las secciones
sobrescriben).

## Pendiente antes de publicar

- Cargar los datos reales de contacto y las URLs de redes.
- Conectar el formulario.
- Publicar artículos reales (la portada muestra los 3 más recientes; si no hay
  ninguno, la sección de blog no se renderiza).

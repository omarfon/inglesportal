<?php
/**
 * Configuración básica de WordPress.
 *
 * El script de creación utiliza este fichero para la creación del fichero wp-config.php durante el
 * proceso de instalación. Usted no necesita usarlo en su sitio web, simplemente puede guardar este fichero
 * como "wp-config.php" y completar los valores necesarios.
 *
 * Este fichero contiene las siguientes configuraciones:
 *
 * * Ajustes de MySQL
 * * Claves secretas
 * * Prefijo de las tablas de la Base de Datos
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solcite esta información a su proveedor de alojamiento web. ** //
/** El nombre de la base de datos de WordPress */
define('DB_NAME', 'inglesportal');

/** Nombre de usuario de la base de datos de MySQL */
define('DB_USER', 'root');

/** Contraseña del usuario de la base de datos de MySQL */
define('DB_PASSWORD', 'root');

/** Nombre del servidor de MySQL (generalmente es localhost) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para usar en la creación de las tablas de la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** El tipo de cotejamiento de la base de datos. Si tiene dudas, no lo modifique. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autenticación y salts.
 *
 * ¡Defina cada clave secreta con una frase aleatoria distinta!
 * Usted puede generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress.org}
 * Usted puede cambiar estos valores en cualquier momento para invalidar todas las cookies existentes. Esto obligará a todos los usuarios a iniciar sesión nuevamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'K|lx>E7yv`PA-aA`mY`HT4qZ5=Wr!aYrCCI^X8ZrolOEgYntj7X*Tfay|?^~F;x5');
define('SECURE_AUTH_KEY',  'x.c7=uEmiUf3v)n(*,to6?7f}):6/bEO77ujf(n:WI0]|cLM#NP2qJn*H5 g#V}B');
define('LOGGED_IN_KEY',    'ni2%T:bVdKfZ_fm;9}z*n`<oe{5khL6_.#[-U(!9BLP-Gl&(~&>JZc3xUB`%LFnw');
define('NONCE_KEY',        'NwkQHqV[d*Eu$#e&|7jg_C;P8tie{)k|IVFeP5wT+hku;Yab?BtbQn)<VD/7xe[/');
define('AUTH_SALT',        'PzD?D3=&=>ada_HKad.I,x$XU!8Vq>X/9KXIQ{,$Ptf[SbB^I^MrF{^~aw]nC$ 3');
define('SECURE_AUTH_SALT', ':`zf1so7=}m<&Sux91_5f1eo5#OVPR]p?cAOL`P<lCw]~iQRDV3MyOj-EJy6PC<%');
define('LOGGED_IN_SALT',   '~Rq,[.<A5B`]amiOyzZP8_]|c/VKN.NLrXQabzPu:8rBL2#L6l!@9v#_Z$d0~e?j');
define('NONCE_SALT',       'b3F, oYvWMS:$;&pnsLjzOs1qu:CB:9^g*Fhwb+;4{~5at=z9KLnZug^%3zo<d.z');

/**#@-*/

/**
 * Prefijo de las tablas de la base de datos de WordPress.
 *
 * Usted puede tener múltiples instalaciones en una sóla base de datos si a cada una le da 
 * un único prefijo. ¡Por favor, emplee sólo números, letras y guiones bajos!
 */
$table_prefix  = 'wp_';

/**
 * Para los desarrolladores: modo de depuración de WordPress.
 *
 * Cambie esto a true para habilitar la visualización de noticias durante el desarrollo.
 * Se recomienda encarecidamente que los desarrolladores de plugins y temas utilicen WP_DEBUG
 * en sus entornos de desarrollo.
 *
 * Para obtener información acerca de otras constantes que se pueden utilizar para la depuración, 
 * visite el Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deje de editar! Disfrute de su sitio. */

/** Ruta absoluta al directorio de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Establece las vars de WordPress y los ficheros incluidos. */
require_once(ABSPATH . 'wp-settings.php');

<?php
/**
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //

/** The name of the database for WordPress */
define( 'DB_NAME', $_ENV['WP_DB_NAME'] ."_es");

/** MySQL database username */
define( 'DB_USER', $_ENV['WP_DB_USER']);

/** MySQL database password */
define( 'DB_PASSWORD', $_ENV['WP_DB_PASSWORD']);

/** MySQL hostname */
define( 'DB_HOST', $_ENV['WP_DB_HOST']);

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', 'utf8mb4_unicode_ci' );

define( 'AS3CF_SETTINGS', serialize( array(
    'provider' => 'aws',
    'access-key-id' => $_ENV['WP_S3_ACCESS_KEY'],
    'secret-access-key' => $_ENV['WP_S3_SECRET_KEY'],
	'bucket' => $_ENV['WP_S3_BUCKET']
) ) );

define( 'FORCE_SSL', true );
define( 'FORCE_SSL_ADMIN',true );
$_SERVER['HTTPS']='on';

/** Ajustes adventistas.org */
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'SITE', 'videos' );

/* Multisite */
define('WP_ALLOW_MULTISITE', true );
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'videos.adventistas.org');
define('PATH_CURRENT_SITE', '/es/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', ')PW>QatPqjoAe+Aue?3,bscv%jsUE-P[2fre$y1z0YDs&xkZV0B@r@!#6F4E-R>w'); // Cambia esto por tu frase aleatoria.
define('SECURE_AUTH_KEY', '+1]3A)i.xrL$m$O|X8buePBCC]j-PCYa}+VQ$FU.p+jc?5GfBitSV2m|x,xKa2D-'); // Cambia esto por tu frase aleatoria.
define('LOGGED_IN_KEY', 'CiheKvV<]S |?G#t>s|s34]tt`IHXW;&js{eXAQ%+fj--N[Mt_71`L9lyDQH9bwN'); // Cambia esto por tu frase aleatoria.
define('NONCE_KEY', 'S<5rsR^c.w#:&dX19@WfF8p<|-|ZTjr@FB+1]r0C/;?h2~5Pv{~|aSK4S`i:3#N.'); // Cambia esto por tu frase aleatoria.
define('AUTH_SALT', 'yQ`nyKhJWM}(BIm71:+M|61T}z9$YLo1%EfwY }aUY`Bx#iyDpTw`Mw*m.j`i)<a'); // Cambia esto por tu frase aleatoria.
define('SECURE_AUTH_SALT', 'WEq,_KFsz8TGi~mHK*9l36a+Rx<2b^_1+;<+7H-/m0%erxe(-,SguR)j|AzGM|8L'); // Cambia esto por tu frase aleatoria.
define('LOGGED_IN_SALT', 'E18a(i!5-i4P6|R1t@{LG)Btp#2ZD$vr+E 3{!:InZ|+f#9tHvtzqH~PMyx_%<m>'); // Cambia esto por tu frase aleatoria.
define('NONCE_SALT', 'UOj|RA_413#G|v.Y/X_;UUe:yar?cGu9$p8n0K[h7w8>tDtz~BOom@/A+Fon8dvn'); // Cambia esto por tu frase aleatoria.

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';

/**
 * Idioma de WordPress.
 *
 * Cambia lo siguiente para tener WordPress en tu idioma. El correspondiente archivo MO
 * del lenguaje elegido debe encontrarse en wp-content/languages.
 * Por ejemplo, instala ca_ES.mo copiándolo a wp-content/languages y define WPLANG como 'ca_ES'
 * para traducir WordPress al catalán.
 */
define('WPLANG', 'es_ES');


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

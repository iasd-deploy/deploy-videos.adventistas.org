<?php
/**
 * As configurações básicas do WordPress.
 *
 * Esse arquivo contém as seguintes configurações: configurações de MySQL, Prefixo de Tabelas,
 * Chaves secretas, Idioma do WordPress, e ABSPATH. Você pode encontrar mais informações
 * visitando {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. Você pode obter as configurações de MySQL de seu servidor de hospedagem.
 *
 * Esse arquivo é usado pelo script ed criação wp-config.php durante a
 * instalação. Você não precisa usar o site, você pode apenas salvar esse arquivo
 * como "wp-config.php" e preencher os valores.
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //

/** The name of the database for WordPress */
define( 'DB_NAME', $_ENV['WP_DB_NAME'] ."_pt");

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
define('PATH_CURRENT_SITE', '/pt/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer cookies existentes. Isto irá forçar todos os usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'kB`!jc:vj.<88Hu{<-?I%HfT-`CYxh,Va1To/-;eL|.Gcm/?a+uxsU@L/s1XK5.r');
define('SECURE_AUTH_KEY',  'HwID<Y5CNLfi0,o;+ua*F]w`,yuY<gW>u^%-T7]-nCmns3/KtFB6]|/HQ(8Fhbg-');
define('LOGGED_IN_KEY',    '2-}QU&*UJ!*_$tnO>>?^U3R*P[b-.s-rZkS|Pc3_w8L.;D/HxP {{(N7l+,<)pZ@');
define('NONCE_KEY',        'IR*&g@Z]Y5G&I$%O$XrZQ#mV?>r~L#PENXU/mwCf i`<Kmj(^e>NSIBm1z|tE#WS');
define('AUTH_SALT',        '|hAZ@vh,j,fs=k_+o3tn)>=!82u<sZ.4?eJpri~;RdWyg/+~g|/H|n3^+=8Xa&/S');
define('SECURE_AUTH_SALT', 'O-edA:#L{/P[EUc6?J8O88>Ihk@7]BjLmh|XYUrX?4tAdaLQ0i3-~Cw|92xt9I/a');
define('LOGGED_IN_SALT',   '*#F8*Ahw/BSsxx>?q(47~|0Wyh+Lo%9~+.8*X+FefcJO}pr-,jH-Iubb`n-z(0hi');
define('NONCE_SALT',       's$-&eAjZS-~9Gg,>e,4 0|O`w4~iU^vfUj`D>?a=u(L*9?=7$Tt0ei2!0&8^GzAq');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente ao
 * idioma escolhido deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define('WPLANG', 'pt_BR');

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * altere isto para true para ativar a exibição de avisos durante o desenvolvimento.
 * é altamente recomendável que os desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 */
define('WP_DEBUG', false);

//define( 'FORCE_SSL_LOGIN', true );
//define( 'FORCE_SSL_ADMIN', true );


/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis do WordPress e arquivos inclusos. */
require_once(ABSPATH . 'wp-settings.php');

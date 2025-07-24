<?php
//Begin Really Simple Security key
define('RSSSL_KEY', 'XqjSxaDDT4bV6TJZ7k3WNI6o96QeZWv8Jge4JXhZSTo5z4x58G8wkIxfzD416ja0');
//END Really Simple Security key

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings

/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

define('FS_METHOD', 'direct');

define('WP_AUTO_UPDATE_CORE', 'minor');// This setting is required to make sure that WordPress updates can be properly managed in WordPress Toolkit. Remove this line if this WordPress website is not managed by WordPress Toolkit anymore.
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'valencaq_wpns' );
/** MySQL database username */
define( 'DB_USER', 'valencaq_wpnu' );
/** MySQL database password */
define( 'DB_PASSWORD', 'a]4hWuhz.ITL' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('DISABLE_WP_CRON', true);
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'E{D`xTv8 ed%[t0E((@M7=AQ^W,<N:~FXm}i$XExS5K8oA9[6SG-8fK1QNx`H-:$' );
define( 'SECURE_AUTH_KEY',  '_e]lF>~ukgmUkBQve`)zYGLeS?3;0S*XZ64#b^ Smz>ofX XM6T?jC2~j0OO`kMo' );
define( 'LOGGED_IN_KEY',    ',1#o>`!21(_a4X(BbRPlnB[kFtF?a[?Z-@ZT%hp>}f4?#n=!.0`fxn;X$Ao7BA1X' );
define( 'NONCE_KEY',        ';wmo1cLfP;La}|iRX2e@uSCVv6-%sJ[P9g#|K<p<`8UF-F P{21+tq8NgCU.VbkE' );
define( 'AUTH_SALT',        'qI}.us}V7X7sW)/(j.bkae:*w&q*u^&9IjIq2n:d$++F4Qj0[Ds*yVOS6r&p)0o9' );
define( 'SECURE_AUTH_SALT', '/t3:4[o/~~0a]DY1rC}pVe{u?>1f|?0` T+>Ir=q.Z8L5jfC$+gqO<[uoLo`rR#r' );
define( 'LOGGED_IN_SALT',   'zxV]<s~eW}B<ID5n2~1m>|3T!h6qY/B;g]3jr``yTuRvVPf.;m2[gIkr?=ETRLI{' );
define( 'NONCE_SALT',       '0q&:K<AxqE*{E1tGf~<bH$Lk_t-W?wsFtg}Y6RZ+ov36 3|W;IkL&K |Id^pm6><' );
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'vq_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

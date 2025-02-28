<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '9Ru2Z$E2@GreY/A=#1)?Ap(%M%:~Zr647uVrb}4`(h_7?M^x#q?qbbj=ug f`ch4' );
define( 'SECURE_AUTH_KEY',   'WiCJvCR)kn7KYJGI;O-/<@nQxz[rXIo7L]Lg>wkj??`cl$4tv,<gz?~:xts]] HH' );
define( 'LOGGED_IN_KEY',     'z0C5t!#CKJ)(BU2zFFHa)_p:Qq+<NXd#:KE2.7Zm*Y+%fX$Oo{ZON{&7;F~J)UC-' );
define( 'NONCE_KEY',         '2NhW&r^,9wrQ[-ZUOW&n]kw10R-N$&]L]^u~6~=>`+Lp<-~rlg^S!]:W$G@Y~@3/' );
define( 'AUTH_SALT',         'c{E+MqjjeoVG>f 7XNL.!HJ7Dh9fX2o<dF_=]R7+y=S?;~~?r(q>5EMN<>F_BRS}' );
define( 'SECURE_AUTH_SALT',  'k-4Hzep1,:Fzwj;eNIlgMmu)t4etM2*Y8%0o_}Y@UH#2Vd(j{M,Q6?E(VvJEo[e[' );
define( 'LOGGED_IN_SALT',    ';o!^*=Pus_rYdF`={{<8hJP8o9n#.?Ej#2KyQ$!).e^@A)WUT5)6s0;4Pr@=9Ho|' );
define( 'NONCE_SALT',        '5hNJAxgAAV{y9;mXIC=>?A_~OFdD4%J:<;=AW<Q|zA3UFhgUxt~$e4sd[t0cTX72' );
define( 'WP_CACHE_KEY_SALT', 'C|0oAEmaRZL,4x7^LD&PL-_.h7^96`RHa)8yXoot__t<oe:TJ,mJ)S|5{Qv6n)/e' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */
define ('FS_METHOD', 'direct');
define ('WP_MEMORY_LIMIT', '256M');

/* test! */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

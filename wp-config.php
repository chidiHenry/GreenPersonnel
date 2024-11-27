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
define( 'AUTH_KEY',          'X8v-gD@PCYD<)eXw5I8RJ[o[eEG[zaT5X{bN|dId,6?IkqQjRH`5E TGyWa$zHC^' );
define( 'SECURE_AUTH_KEY',   '#*nVMo/?ak)MESFQh$5.Q; GjCLL:+&ekb<&b^&(~<._}gj%,|./i?o[oQ/}IfP|' );
define( 'LOGGED_IN_KEY',     'e{sx4V+5ou@Kths<Nfk>jRArB2N(duc[Kf;^)a?WsGwXQus{TSi+[hBE,irCt>fY' );
define( 'NONCE_KEY',         '`)EI~n>cw<dJFF?<*0>yW6p#BruGj*1=A;! m>_j:lR;mb%pRO-Y~Dg,?2[-y8;g' );
define( 'AUTH_SALT',         'D%)GBwJW_:uAF4=u<Sy5x+t8S7{2$Y/;Tze?LlKktHs2P>F9|&$]5 z[@F!>;w;)' );
define( 'SECURE_AUTH_SALT',  'l=+!Q6Spm5U?F,Cp=7_F@f0Uf-fsac`H82&]5krW0JZS}zcQ_.Vcz%V#~s@>G+%X' );
define( 'LOGGED_IN_SALT',    'p8B3>OB=-!Mpo:<QO:,{~Mn*tI!W9. X~E|.2R>u#N@HN1a6)6:htemMG>A3%=[3' );
define( 'NONCE_SALT',        'JV]JOD+$rv.4-M3PIs*i/b*At+4sUDa91o|u8bMtP)nQ@3 RyH%@@EI%ai-&pVT;' );
define( 'WP_CACHE_KEY_SALT', 'LQ$k9 *og%DV [8k)#.;u3Nq(xx,tjm<^(UX<ft95_>z.wBN}-;hknH#do4/l[]^' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

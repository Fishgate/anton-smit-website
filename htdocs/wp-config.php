<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
if ( file_exists( dirname( __FILE__ ) . '/wp-config-local.php' ) ) {
	include( dirname( __FILE__ ) . '/wp-config-local.php' );
} else {
	define('DB_NAME', '');
	define('DB_USER', '');
	define('DB_PASSWORD', '');
	define('DB_HOST', '');
	
	/**
	 * For developers: WordPress debugging mode.
	 *
	 * Change this to true to enable the display of notices during development.
	 * It is strongly recommended that plugin and theme developers use WP_DEBUG
	 * in their development environments.
	 */
	define('WP_DEBUG', false);

}

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         's7z5Ld=IUk8#*>U(-E-O{P8SIY*Z(+1#^+Cp,jiymg;l#,6Y4qf~5eWRC_AB|yM1');
define('SECURE_AUTH_KEY',  ']<_|$Vm&XI+*Y-E27]0A-|s0>]MW?|>)ZG$(+qQ0DKKe}Kv+q>=[*G,4!TbLHZg-');
define('LOGGED_IN_KEY',    '_dflw&lpIq9U@QhzR7<09`fPyeub+S4+-qO/Xi?SSt08A@2p&$F6cht3IA7rnjqu');
define('NONCE_KEY',        '~3+6=C6LoOT!+iP5-`+X;2-z);Jf6b-BE9<@S[>Y >K4s#|r9Uxd2mo~DEx4]o//');
define('AUTH_SALT',        '.j|KFKHMN%ggcK19e[|RGTF/|2YTF1-91+7UZ(qX6ys?&XA|VzI+m$F&rUEK`@Ii');
define('SECURE_AUTH_SALT', '6/5VH*rPU%`SVzx?C[Jm<R{jj<Phq+t^@U5^+|<;6j1FQ|[xGxQE!7x 6Qhf*,%l');
define('LOGGED_IN_SALT',   'ha|(BH!ihGD+7W-b|.+(FwkKdb+++SK(o7+L+EAGDg`yhcx<q?s<i2-G9&nw+@aE');
define('NONCE_SALT',       'M1jHVV[],[]o,Ac!B#VQ-E9y7Rr+u$DX]LysP|j-)XgVK2=F62nJq<0`t>,,Z|Gh');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

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
/** The name of the database for WordPress */
define('DB_NAME', 'coldplay_db');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '3YpnTzl=;5ld17B/4Id7$XJ`LY4AUx/N2TjGjc:>+e2L8br FgP!lc[d;17E88i{');
define('SECURE_AUTH_KEY',  '{uLSR&Z;RfRq=kh[c?Vj+=`I<ZM60V*%,(R59kq*5u+WFWSj^^{6T+QHw<.[ZT}L');
define('LOGGED_IN_KEY',    ';<;P7`yU]MEC0UQp{-+[h0*sRUjM5ZNt?{*vZuAVeR7}O.Nv{.d:xD^G?ah([z/i');
define('NONCE_KEY',        'vvjB$-Y4as#pN&l40j[M*]VX<#$4!xLElQ[&XNPX#_8I|~<A*}(cl2LB{JAT6TRv');
define('AUTH_SALT',        'e:?:{0&bwv/ FFN|w.Y+ v=Cw]@ pllshu9>%:[s*WpG=U,x$5$;e6i[v77EuN1>');
define('SECURE_AUTH_SALT', '9F$CQ=(HZJ0fr6OW3v&,:D]$uu+%,~N6zlC=A<gVW&>EoD{k+:k&Pkw(hvVA` _7');
define('LOGGED_IN_SALT',   'b JfOcpn|;0c9T,7ID z3~L+&YGU8rzi,Fd^7B;k;Gp<J3o7:7Yct/}TLo*xq3Xq');
define('NONCE_SALT',       '3f>3$@0HlSHf~WXY3!,2GemMPyl#+=6Q(lE#^!TLl#%X@&*5dqe(*/=L9y_xhnT=');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

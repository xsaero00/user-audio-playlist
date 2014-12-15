<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           User_Audio_Playlist
 *
 * @wordpress-plugin
 * Plugin Name:       User Audio Playlist
 * Plugin URI:        http://mikestarov.com/wp-plugins/user-audio-playlist
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress dashboard.
 * Version:           1.0.0
 * Author:            Mike Starov
 * Author URI:        http://mikestarov.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       user-audio-playlist
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//define some needed constants
define( 'UAP_SLUG', 'user_audio_playlist' );
define( 'UAP_PLAYLIST_SLUG', 'my-first-playlist');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-user-audio-playlist-activator.php
 */
function activate_user_audio_playlist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-user-audio-playlist-activator.php';
	User_Audio_Playlist_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-user-audio-playlist-deactivator.php
 */
function deactivate_user_audio_playlist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-user-audio-playlist-deactivator.php';
	User_Audio_Playlist_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_user_audio_playlist' );
register_deactivation_hook( __FILE__, 'deactivate_user_audio_playlist' );

/**
 * Autoloader callback.
 *
 * Converts a class name to a file path and requires it if it exists.
 *
 * @since 1.0.0
 *
 * @param string $class Class name.
 */
function autoload_playlist_class( $class ) {

	$file  = dirname( __FILE__ );
	$file .= ( false === strpos( $class, 'Admin' ) ) ? '/includes/' : '/admin/includes/';
	$file .= 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

	if ( file_exists( $file ) ) {
		require_once( $file );
	}
}
spl_autoload_register( 'autoload_playlist_class' );



/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-user-audio-playlist.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_user_audio_playlist() {

	$plugin = new User_Audio_Playlist();
	$plugin->run();

}
run_user_audio_playlist();


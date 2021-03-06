<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/admin
 * @author     Mike Starov <mike.starov@gmail.com>
 */
class User_Audio_Playlist_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $user_audio_playlist    The ID of this plugin.
	 */
	private $user_audio_playlist;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $user_audio_playlist       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $user_audio_playlist, $version ) {

		$this->user_audio_playlist = $user_audio_playlist;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in User_Audio_Playlist_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_Audio_Playlist_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->user_audio_playlist, plugin_dir_url( __FILE__ ) . 'css/user-audio-playlist-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in User_Audio_Playlist_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_Audio_Playlist_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->user_audio_playlist, plugin_dir_url( __FILE__ ) . 'js/user-audio-playlist-admin.js', array( 'jquery' ), $this->version, false );

	}

}

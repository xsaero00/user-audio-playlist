<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/includes
 * @author     Mike Starov <mike.starov@gmail.com>
 */
class User_Audio_Playlist {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      User_Audio_Playlist_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $user_audio_playlist    The string used to uniquely identify this plugin.
	 */
	protected $user_audio_playlist;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->user_audio_playlist = 'user_audio_playlist';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - User_Audio_Playlist_Loader. Orchestrates the hooks of the plugin.
	 * - User_Audio_Playlist_i18n. Defines internationalization functionality.
	 * - User_Audio_Playlist_Admin. Defines all hooks for the dashboard.
	 * - User_Audio_Playlist_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-user-audio-playlist-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-user-audio-playlist-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-user-audio-playlist-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-user-audio-playlist-public.php';

		$this->loader = new User_Audio_Playlist_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the User_Audio_Playlist_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new User_Audio_Playlist_i18n();
		$plugin_i18n->set_domain( $this->get_user_audio_playlist() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new User_Audio_Playlist_Admin( $this->get_user_audio_playlist(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new User_Audio_Playlist_Public( $this->get_user_audio_playlist(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// modify audio short code at the time of insertion in post
		$this->loader->add_filter( 'media_send_to_editor', $plugin_public, 'add_id_attribute_to_audio_shortcode', 10, 3);
		// add audio short code post preprocessing
		$this->loader->add_filter( 'wp_audio_shortcode_override', $plugin_public, 'record_audio_shortcode_attributes', 10, 4);
		// add audio short code post processing
		$this->loader->add_filter( 'wp_audio_shortcode', $plugin_public, 'render_add_to_playlist_link', 10, 5);

		// register user playlist widget
		$this->loader->add_action( 'widgets_init', $plugin_public, 'register_user_audio_playlist_widget' );

		// add ajax action callback
		//TODO: Use common action names variables/constants
		$this->loader->add_action( 'wp_ajax_add_to_playlist', $plugin_public, 'ajax_add_to_playlist_callback');
		$this->loader->add_action( 'wp_ajax_nopriv_add_to_playlist', $plugin_public, 'ajax_add_to_playlist_callback');
		$this->loader->add_action( 'wp_ajax_remove_from_playlist', $plugin_public, 'ajax_remove_from_playlist_callback');
		$this->loader->add_action( 'wp_ajax_nopriv_remove_from_playlist', $plugin_public, 'ajax_remove_from_playlist_callback');
		$this->loader->add_action( 'wp_ajax_retrieve_playlist', $plugin_public, 'ajax_retrieve_playlist_callback');
		$this->loader->add_action( 'wp_ajax_nopriv_retrieve_playlist', $plugin_public, 'ajax_retrieve_playlist_callback');

		// add calls to start and destroy session
		$this->loader->add_action( 'init', $plugin_public, 'start_playlist_session');
		$this->loader->add_action( 'wp_logout', $plugin_public, 'destroy_playlist_session');


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_user_audio_playlist() {
		return $this->user_audio_playlist;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    User_Audio_Playlist_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

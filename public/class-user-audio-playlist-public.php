<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/public
 * @author     Mike Starov <mike.starov@gmail.com>
 */
class User_Audio_Playlist_Public {

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
	 * @var      string    $user_audio_playlist       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $user_audio_playlist, $version ) {

		$this->user_audio_playlist = $user_audio_playlist;
		$this->version = $version;
		$this->link_class = 'add-to-playlist';
		$this->link_text = __("Add to playlist +");
		$this->default_playlist_title = __("My First Playlist");
		$this->add_action = 'add_to_playlist'; // add to playlist action

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in User_Audio_Playlist_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_Audio_Playlist_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->user_audio_playlist, plugin_dir_url( __FILE__ ) . 'css/user-audio-playlist-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in User_Audio_Playlist_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The User_Audio_Playlist_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->user_audio_playlist, plugin_dir_url( __FILE__ ) . 'js/user-audio-playlist-public.js', array( 'jquery' ), $this->version, false );
		// make some variables avaialble to JavaScript
		wp_localize_script($this->user_audio_playlist, $this->user_audio_playlist, array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'link_selector' => '.'.$this->link_class)); 

	}

	/**	
	*/
	public function render_add_to_playlist_link( $html, $atts, $audio, $post_id, $library )
	{
		$default_types = wp_get_audio_extensions();
		$defaults_atts = array(
			'src'      => '',
			'pltext'=>$this->link_text,
			'plclass'=>$this->link_class,
			'pltitle'=>$this->default_playlist_title,
			'action'=>$this->add_action, 			
		);
		foreach ( $default_types as $type ) {
			$defaults_atts[$type] = '';
		}
		$atts = shortcode_atts( $defaults_atts, $atts, 'audio' );


		$data = $data_atts = [];
		foreach($atts as $k => $v)
			if(!empty($v))
				$data[$k] = $v;
		$data['postid'] = $post_id;		
		foreach ($data as $k => $v) {
			$data_atts[] = 'data-'.$k . '="' . esc_attr( $v ) . '"';
		}

		$html_atts = array( 'id='.sprintf( 'pl-add-audio-%d', $post_id),
							'class='.esc_attr($atts['plclass']));

		$link_html = sprintf( '<a href="#" %s %s>%s</a>', join( ' ', $html_atts ), join( ' ', $data_atts ), $atts['pltext'] );

		return $html."<!-- Add to playlist -->".$link_html;
	}

	/**
	* Register user playlist widget
	*/
	public function register_user_audio_playlist_widget()
	{
		require_once plugin_dir_path( __FILE__  ) . 'user-audio-playlist-widget.php';
		register_widget( 'User_Audio_Playlist_Widget' );
	}

	/**
	* Add to playlist AJAX callback 
	*/
	public function ajax_add_to_playlist_callback()
	{
		if(empty($_POST))
			wp_send_json_error();
		
		// playlist title and slug
		$playlist_title = ( isset($_POST['pltitle'])?$_POST['pltitle']:$this->default_playlist_title );
		$playlist_slug = sanitize_title( $playlist_title);
		
		$manager = new Playlist_Manager($this->user_audio_playlist, $playlist_slug, $playlist_title);
		
		

		// get the item to add to playlist
		$playlist_item = Null;
        $default_types = wp_get_audio_extensions();
        foreach ($default_types as $type) {
        	if(isset($_POST[$type]) && !empty($_POST[$type]))
        	{
        		$playlist_item = $_POST[$type];
        		break;
        	}
        }
		
		if(!$playlist_item)
		wp_send_json_error( array( 'message'=>__("Could not add to playlist: No valid audio file!"), $this->user_audio_playlist=>$manager->as_array()));

		if(!$manager->add($playlist_item))
			wp_send_json_error( array('message' =>__("Could not add to playlist: This file is already added to this playlist!"),$this->user_audio_playlist=>$manager->as_array()));

		
		wp_send_json_success(array($this->user_audio_playlist=>$manager->as_array()));
	}


	/**
	*	Start session, after this call the PHP $_SESSION super global is available
	*/
	public function start_playlist_session()
	{
		if(!session_id())session_start();
	}

	/**
	* Destroy the session, this removes any data saved in the session
	*/
	public function destroy_playlist_session()
	{
		session_destroy ();
	}
}

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
		$this->add_link_class = 'add-to-playlist';
		$this->remove_link_class = 'remove-from-playlist';
		$this->download_link_class = 'download-from-playlist';
		$this->link_text = __("Add to playlist +");
		$this->default_playlist_title = __("My First Playlist");
		$this->add_action = 'add_to_playlist'; // add to playlist action
		$this->remove_action = 'remove_from_playlist'; // remove from playlist action
		$this->attr_storage = array(); // temporary holding place for shortcode attributes

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

		wp_enqueue_style( $this->user_audio_playlist, plugin_dir_url( __FILE__ ) . 'css/user-audio-playlist-public.css', array('mediaelement'), $this->version, 'all' );

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
		
		// IE hack
		?>
				<!--[if lt IE 9]><script>document.createElement('audio');</script><![endif]-->
		<?php
		wp_enqueue_script( $this->user_audio_playlist, plugin_dir_url( __FILE__ ) . 'js/user-audio-playlist-public.js', array( 'jquery',  'wp-util', 'backbone', 'mediaelement'  ), $this->version, false );
		// make some variables avaialble to JavaScript
		wp_localize_script( $this->user_audio_playlist, $this->user_audio_playlist, array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 
																						  'add_link_selector' => '.'.$this->add_link_class,
																						  'remove_link_selector' => '.'.$this->remove_link_class,
																						  'download_link_selector' => '.'.$this->download_link_class,
																						  'playlist_widget_selector' => '.'.UAP_SLUG)); 

	}
	
	/**
	 * Filter the HTML markup for a media item sent to the editor.
	 *
	 * @since 2.5.0
	 *
	 * @see wp_get_attachment_metadata()
	 *
	 * @param string $html       HTML markup for a media item sent to the editor.
	 * @param int    $aid         Attachemnt id
	 * @param array  $attachment Array of attachment metadata.
	 */
	public function add_id_attribute_to_audio_shortcode($html, $aid, $attachment )
	{
		// if this is an audio shortcode, add id attribute
		$pos = strpos($html, '[audio');
		if ($pos === 0 && $aid)
			$html = "[audio id=\"$aid\"" . substr($html, 6);
		
		return $html;
	}
	
	/**
	 * Filter the default audio shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating the default audio template.
	 *
	 * @since 3.6.0
	 *
	 * @param string $html      Empty variable to be replaced with shortcode markup.
	 * @param array  $attr      Attributes of the shortcode. @see wp_audio_shortcode()
	 * @param string $content   Shortcode content.
	 * @param int    $instances Unique numeric ID of this audio shortcode instance.
	 */
	public function record_audio_shortcode_attributes($html, $attr, $content, $instances)
	{
		array_push($this->attr_storage, $attr);
		return '';
	}

	/**	
	*/
	public function render_add_to_playlist_link( $html, $atts, $audio, $post_id, $library )
	{
		$default_types = wp_get_audio_extensions();
		$defaults_atts = array(
			'id' => '',
			'src'      => '',
			'pltext'=>$this->link_text,
			'plclass'=>$this->add_link_class,
			'pltitle'=>$this->default_playlist_title,
			'action'=>$this->add_action, 			
		);
		foreach ( $default_types as $type ) {
			$defaults_atts[$type] = '';
		}
		$original_atts = array_pop($this->attr_storage);
		$atts = shortcode_atts( $defaults_atts, array_merge($original_atts, $atts), 'audio' );


		$data = array_filter($atts, function ($v){return !empty($v);});
		$data['postid'] = $post_id;		
		$params['id'] = sprintf( 'pl-add-audio-%d', $post_id);
		$params['class'] = esc_attr($atts['plclass']);

// 		$link_html =  $this->render_add_link($atts['pltext'], $params, $data);
// 		sprintf( '<a href="#" %s %s>%s</a>', join( ' ', $html_atts ), join( ' ', $data_atts ), $atts['pltext'] );

		return $html."<!-- Add to playlist -->".$this->render_add_link($atts['pltext'], $params, $data);
	}
	
	private function render_add_link($link_text, $attrs=Null, $data=Null)
	{
		// define generic attributes
		$attrs = wp_parse_args( $attrs, array( 'class' => $this->add_link_class ) );
		// define data attributes
		$data = wp_parse_args( $data, array() );
		$data_attr = $html_atts = array();
		foreach ($attrs as $k => $v) {
			$html_atts[] = $k . '="' . esc_attr( $v ) . '"';
		}
		foreach ($data as $k => $v) {
			$data_atts[] = 'data-'.$k . '="' . esc_attr( $v ) . '"';
		}
		
		return sprintf( '<a href="#" %s %s>%s</a>', join( ' ', $html_atts), join(' ', $data_atts), $link_text );
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
	 * Add shortcode for adding music
	 */
	public function register_add_to_playlist_shortcode()
	{
		add_shortcode( 'uap', array($this, 'add_to_playlist_shortcode') );
	}
	
	/**
	 * Add to playlist shortcode. A way to add manual link to add something into playlist
	 * @param unknown $attrs
	 * @param unknown $link_text
	 * @return string
	 */
	public function add_to_playlist_shortcode( $attrs, $link_text )
	{
		// TODO: Finish it
		$attrs = shortcode_atts( array('ids'=>''), $attrs);
		if(!$attrs['ids'])
			return "Please define ids.";
		if(!$link_text)
			$link_text = $this->$this->link_text;
		
		return "<!-- Add to playlist -->".$this->render_add_link($link_text, $params, $data);
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
		
		$manager = new Playlist_Manager($playlist_slug, $playlist_title);
		
		// get the item to add to playlist
		$default_types = wp_get_audio_extensions();
		$defaults_atts = array(
				'id' => '',
				'postid'      => '',
		);
		foreach ( $default_types as $type ) {
			$defaults_atts[$type] = '';
		}

		$playlist_item = shortcode_atts( $defaults_atts, $_POST);
		
		if(!$playlist_item)
			wp_send_json_error( array( 'message'=>__("Could not add to playlist: No valid audio file!"), $this->user_audio_playlist=>$manager->as_array()));

		if(!$manager->add($playlist_item))
			wp_send_json_error( array('message' =>__("Could not add to playlist: This file is already added to this playlist!"), $this->user_audio_playlist=>$manager->as_array()));

		
		wp_send_json_success(array($this->user_audio_playlist=>$manager->as_array()));
	}
	
	/**
	 * Remove from playlist AJAX callback
	 */
	public function ajax_remove_from_playlist_callback()
	{
		if(empty($_POST))
			wp_send_json_error();
		
		// playlist title and slug
		$playlist_title = ( isset($_POST['pltitle'])?$_POST['pltitle']:$this->default_playlist_title );
		$playlist_slug = sanitize_title( $playlist_title);
		
		$manager = new Playlist_Manager($playlist_slug);
		
		$playlist_item = NULL;
		
		if(isset($_POST['plitemkey']))
			$playlist_item = $_POST['plitemkey'];
		
		else if(isset($_POST['plitem']))
			$playlist_item = $_POST['plitem'];
		
		if(!$playlist_item)
			wp_send_json_error( array( 'message'=>__("Could remove from playlist: No item!"), $this->user_audio_playlist=>$manager->as_array()));
		
		
		
		$manager->remove($playlist_item);
		
		
		wp_send_json_success(array($this->user_audio_playlist=>$manager->as_array()));
	}
	
	public function ajax_retrieve_playlist_callback()
	{
		// playlist title and slug
		$playlist_title = ( isset($_POST['pltitle'])?$_POST['pltitle']:$this->default_playlist_title );
		$playlist_slug = sanitize_title( $playlist_title);
	
		$manager = new Playlist_Manager($playlist_slug);
		
		$parray = $manager->as_array();
		$ids = array_values(array_map(function ($i) {return $i['id'];}, $parray['items']));
		$data = uap_playlist_data(array(
				'ids' => $ids
		) ) ;
	
		wp_send_json($data);
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

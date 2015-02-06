<?php 

/**
 * User_Audio_Playlist_Widget widget.
 *
 * Add special widget that displays user audio playlist
 *
 * @package    User_Audio_Playlist
 * @subpackage User_Audio_Playlist/public
 * @author     Mike Starov <mike.starov@gmail.com>
 */

class User_Audio_Playlist_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'user_audio_playlist_widget', // Base ID
			__( 'User Audio Playlist' ), // Name
			array( 'description' => __( 'This widget allows users to display their custom playlist as well as add and remove audio files from it.' ), ) // Args
		);
	}

	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$this->widget_simple($args, $instance);
	}
	
	/**
	 * Simple widget display
	 * 
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget_simple( $args, $instance ) {
	
     	        echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		$playlist = new Playlist_Manager(UAP_PLAYLIST_SLUG);
		$parray = $playlist -> as_array();
		
		echo "<div class='".UAP_SLUG."' id='playlist-".$parray['slug']."'>";
		echo "<h4 class='user_audio_playlist-title'>".$parray['title']."</h4>";
		if(empty($parray['items']))
			echo "<p>".__('Playlist is empty.')."</p>";
		else
		{
			echo "<ul>";
			//TODO: Use common values for class and data action in remove link
			//TODO: Sanitize attributes, etc
			foreach ($parray['items'] as $key => $item) {
				echo <<<END
					<audio class="wp-audio-shortcode" id="" preload="none"
				        style="width: 100%; visibility: hidden;" controls="controls">
				        <source type="audio/mpeg" src="$item?_=1"/>
				        <a href="$item">$item</a>
				    </audio>
				    &nbsp;
				    <a href="#" class="remove-from-playlist" data-plitemkey="$key" data-plitem="$item" data-action="remove_from_playlist" data-pltitle="{$parray['title']}">remove</a>
				    <hr/>
END;
			}
			echo "</ul>";
		}
		echo "<div/>";
		
		echo $args['after_widget'];
	}
	
	/**
	 * Cue playlist widget display
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget_cue( $args, $instance ) {
	
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
	
		$playlist = new Playlist_Manager(UAP_PLAYLIST_SLUG);
		$parray = $playlist -> as_array();
	
		echo "<div class='".UAP_SLUG."' id='playlist-".$parray['slug']."'>";
		echo "<h4 class='user_audio_playlist-title'>".$parray['title']."</h4>";
		if(empty($parray['items']))
			echo "<p>".__('Playlist is empty.')."</p>";
		else
		{
			// Show cue playlist
			$classes   = array( 'cue-playlist' );
			$classes[] = ( isset( $args['show_playlist'] ) && false == $args['show_playlist'] ) ? 'is-playlist-hidden' : '';
			$classes   = implode( ' ', array_filter( $classes ) );
			
			echo '<div class="cue-playlist-container">';
			
			//do_action( 'cue_before_playlist', $post, $tracks, $args );
			
			$tracks = $parray['items'];
			
			include( plugin_dir_path( dirname( __FILE__ ) ).'/templates/playlist.php' );
			
			//do_action( 'cue_after_playlist', $post, $tracks, $args );
			
			echo '</div>';
			
// 			echo "<ul>";
// 			//TODO: Use common values for class and data action in remove link
// 			//TODO: Sanitize attributes, etc
// 			foreach ($parray['items'] as $key => $item) {
// 				echo <<<END
// 					<audio class="wp-audio-shortcode" id="" preload="none"
// 				        style="width: 100%; visibility: hidden;" controls="controls">
// 				        <source type="audio/mpeg" src="$item?_=1"/>
// 				        <a href="$item">$item</a>
// 				    </audio>
// 				    &nbsp;
// 				    <a href="#" class="remove-from-playlist" data-plitemkey="$key" data-plitem="$item" data-action="remove_from_playlist" data-pltitle="{$parray['title']}">remove</a>
// 				    <hr/>
// END;
// 			}
// 			echo "</ul>";
		}
		echo "<div/>";
	
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
     	        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'User Audio Playlist' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class User_Audio_Playlist_Widget
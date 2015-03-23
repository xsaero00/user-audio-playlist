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
		$this->widget_worpress_playlist($args, $instance);
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
				// assume 'mp3'
				echo <<<END
					<audio class="wp-audio-shortcode" id="" preload="none"
				        style="width: 100%; visibility: hidden;" controls="controls">
				        <source type="audio/mpeg" src="{$item['mp3']}?_=1"/>
				        <a href="{$item['mp3']}">{$item['mp3']}</a>
				    </audio>
				    &nbsp;
				    <a href="#" class="remove-from-playlist" data-plitemkey="$key" data-action="remove_from_playlist" data-pltitle="{$parray['title']}">remove</a>
				    <hr/>
END;
			}
			echo "</ul>";
		}
		echo "<div/>";
		
		echo $args['after_widget'];
	}
	
	/**
	 * Wordpress playlist widget display
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget_worpress_playlist( $args, $instance ) {
	
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
			$ids = array_values(array_map(function ($i) {return $i['id'];}, $parray['items']));
			// Show wordpress playlist
			echo uap_playlist_shortcode( array(
				'ids' => $ids
			) );
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


/**
 * Output the templates used by playlists.
 *
 * @since 3.9.0
 */
function uap_underscore_playlist_templates() {
	?>
<script type="text/html" id="tmpl-uap-playlist-current-item">
	<# if ( data.image ) { #>
	<img src="{{ data.thumb.src }}"/>
	<# } #>
	<div class="uap-playlist-caption">
		<span class="uap-playlist-item-meta uap-playlist-item-title">&#8220;{{ data.title }}&#8221;</span>
		<# if ( data.meta.album ) { #><span class="uap-playlist-item-meta uap-playlist-item-album">{{ data.meta.album }}</span><# } #>
		<# if ( data.meta.artist ) { #><span class="uap-playlist-item-meta uap-playlist-item-artist">{{ data.meta.artist }}</span><# } #>
	</div>
</script>
<script type="text/html" id="tmpl-uap-playlist-item">
	<div class="uap-playlist-item">
		<a class="uap-playlist-caption" href="{{ data.src }}">
			{{ data.index ? ( data.index + '. ' ) : '' }}
			<# if ( data.caption ) { #>
				{{ data.caption }}
			<# } else { #>
				<span class="uap-playlist-item-title">&#8220;{{{ data.title }}}&#8221;</span>
				<# if ( data.artists && data.meta.artist ) { #>
				<span class="uap-playlist-item-artist"> &mdash; {{ data.meta.artist }}</span>
				<# } #>
			<# } #>
		</a>
		<# if ( data.meta.length_formatted ) { #>
		<div class="uap-playlist-item-length">{{ data.meta.length_formatted }}</div>
		<# } #>
	</div>
</script>
<?php
}


/**
 * The user audio playlist "shortcode".
 *
 * This implements the functionality of the playlist shortcode for displaying
 * a collection of WordPress audio or video files in a post.
 *
 * @since 3.9.0
 *
 * @param array $attr {
 *     Array of default playlist attributes.
 *
 *     @type string  $type         Type of playlist to display. Accepts 'audio' or 'video'. Default 'audio'.
 *     @type string  $order        Designates ascending or descending order of items in the playlist.
 *                                 Accepts 'ASC', 'DESC'. Default 'ASC'.
 *     @type string  $orderby      Any column, or columns, to sort the playlist. If $ids are
 *                                 passed, this defaults to the order of the $ids array ('post__in').
 *                                 Otherwise default is 'menu_order ID'.
 *     @type int     $id           If an explicit $ids array is not present, this parameter
 *                                 will determine which attachments are used for the playlist.
 *                                 Default is the current post ID.
 *     @type array   $ids          Create a playlist out of these explicit attachment IDs. If empty,
 *                                 a playlist will be created from all $type attachments of $id.
 *                                 Default empty.
 *     @type array   $exclude      List of specific attachment IDs to exclude from the playlist. Default empty.
 *     @type string  $style        Playlist style to use. Accepts 'light' or 'dark'. Default 'light'.
 *     @type bool    $tracklist    Whether to show or hide the playlist. Default true.
 *     @type bool    $tracknumbers Whether to show or hide the numbers next to entries in the playlist. Default true.
 *     @type bool    $images       Show or hide the video or audio thumbnail (Featured Image/post
 *                                 thumbnail). Default true.
 *     @type bool    $artists      Whether to show or hide artist name in the playlist. Default true.
 * }
 *
 * @return string Playlist output. Empty string if the passed type is unsupported.
 */
function uap_playlist_shortcode( $attr ) {
	global $content_width;
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	

	$atts = shortcode_atts( array(
			'type'		=> 'audio',
			'order'		=> 'ASC',
			'orderby'	=> 'menu_order ID',
			'id'		=> $post ? $post->ID : 0,
			'include'	=> '',
			'exclude'   => '',
			'style'		=> 'light',
			'tracklist' => true,
			'tracknumbers' => true,
			'images'	=> true,
			'artists'	=> true
	), $attr, 'playlist' );

	$id = intval( $atts['id'] );

	if ( $atts['type'] !== 'audio' ) {
		$atts['type'] = 'video';
	}

	$args = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => $atts['type'],
			'order' => $atts['order'],
			'orderby' => $atts['orderby']
	);

	if ( ! empty( $atts['include'] ) ) {
		$args['include'] = $atts['include'];
		$_attachments = get_posts( $args );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$args['post_parent'] = $id;
		$args['exclude'] = $atts['exclude'];
		$attachments = get_children( $args );
	} else {
		$args['post_parent'] = $id;
		$attachments = get_children( $args );
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id ) . "\n";
		}
		return $output;
	}

	$outer = 22; // default padding and border of wrapper

	$default_width = 640;
	$default_height = 360;

	$theme_width = empty( $content_width ) ? $default_width : ( $content_width - $outer );
	$theme_height = empty( $content_width ) ? $default_height : round( ( $default_height * $theme_width ) / $default_width );

	$data = array(
			'type' => $atts['type'],
			// don't pass strings to JSON, will be truthy in JS
			'tracklist' => wp_validate_boolean( $atts['tracklist'] ),
			'tracknumbers' => wp_validate_boolean( $atts['tracknumbers'] ),
			'images' => wp_validate_boolean( $atts['images'] ),
			'artists' => wp_validate_boolean( $atts['artists'] ),
	);

	$tracks = array();
	foreach ( $attachments as $attachment ) {
		$url = wp_get_attachment_url( $attachment->ID );
		$ftype = wp_check_filetype( $url, wp_get_mime_types() );
		$track = array(
				'src' => $url,
				'type' => $ftype['type'],
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content
		);

		$track['meta'] = array();
		$meta = wp_get_attachment_metadata( $attachment->ID );
		if ( ! empty( $meta ) ) {

			foreach ( wp_get_attachment_id3_keys( $attachment ) as $key => $label ) {
				if ( ! empty( $meta[ $key ] ) ) {
					$track['meta'][ $key ] = $meta[ $key ];
				}
			}

			if ( 'video' === $atts['type'] ) {
				if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
					$width = $meta['width'];
					$height = $meta['height'];
					$theme_height = round( ( $height * $theme_width ) / $width );
				} else {
					$width = $default_width;
					$height = $default_height;
				}

				$track['dimensions'] = array(
						'original' => compact( 'width', 'height' ),
						'resized' => array(
								'width' => $theme_width,
								'height' => $theme_height
						)
				);
			}
		}

		if ( $atts['images'] ) {
			$thumb_id = get_post_thumbnail_id( $attachment->ID );
			if ( ! empty( $thumb_id ) ) {
				list( $src, $width, $height ) = wp_get_attachment_image_src( $thumb_id, 'full' );
				$track['image'] = compact( 'src', 'width', 'height' );
				list( $src, $width, $height ) = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$track['thumb'] = compact( 'src', 'width', 'height' );
			} else {
				$src = wp_mime_type_icon( $attachment->ID );
				$width = 48;
				$height = 64;
				$track['image'] = compact( 'src', 'width', 'height' );
				$track['thumb'] = compact( 'src', 'width', 'height' );
			}
		}

		$tracks[] = $track;
	}
	$data['tracks'] = $tracks;

	$safe_type = esc_attr( $atts['type'] );
	$safe_style = esc_attr( $atts['style'] );

	ob_start();

	if ( 1 === $instance ) {
		/**
		 * Print and enqueue playlist scripts, styles, and JavaScript templates.
		 *
		 * @since 3.9.0
		 *
		 * @param string $type  Type of playlist. Possible values are 'audio' or 'video'.
		 * @param string $style The 'theme' for the playlist. Core provides 'light' and 'dark'.
		 */
		do_action( 'uap_playlist_scripts', $atts['type'], $atts['style'] );
	} ?>
<div class="uap-playlist uap-<?php echo $safe_type ?>-playlist uap-playlist-<?php echo $safe_style ?>">
	<?php if ( 'audio' === $atts['type'] ): ?>
	<div class="uap-playlist-current-item"></div>
	<?php endif ?>
	<<?php echo $safe_type ?> controls="controls" preload="none" width="<?php
		echo (int) $theme_width;
	?>"<?php if ( 'video' === $safe_type ):
		echo ' height="', (int) $theme_height, '"';
	else:
		echo ' style="visibility: hidden"';
	endif; ?>></<?php echo $safe_type ?>>
	<div class="uap-playlist-next"></div>
	<div class="uap-playlist-prev"></div>
	<noscript>
	<ol><?php
	foreach ( $attachments as $att_id => $attachment ) {
		printf( '<li>%s</li>', wp_get_attachment_link( $att_id ) );
	}
	?></ol>
	</noscript>
	<script type="application/json" class="uap-playlist-script"><?php echo wp_json_encode( $data ) ?></script>
</div>
	<?php
	return ob_get_clean();
}

/**
 * Output and enqueue default scripts and styles for playlists.
 *
 * @since 3.9.0
 *
 * @param string $type Type of playlist. Accepts 'audio' or 'video'.
 */
function uap_playlist_scripts( $type ) {
	?>
<!--[if lt IE 9]><script>document.createElement('<?php echo esc_js( $type ) ?>');</script><![endif]-->
<?php
	add_action( 'wp_footer', 'uap_underscore_playlist_templates', 0 );
	add_action( 'admin_footer', 'uap_underscore_playlist_templates', 0 );
}
add_action( 'uap_playlist_scripts', 'uap_playlist_scripts' );
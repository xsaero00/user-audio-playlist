(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	$(function() {
		/* Add remove links to playlist */
		$(user_audio_playlist.playlist_widget_selector).find('.wp-playlist-item-length').append('<a href="#" class="remove-from-playlist" data-plitemkey="" data-action="remove_from_playlist" data-pltitle=""> x</a>');
		
		/* Action to add items to playlist(s) */
		$(user_audio_playlist.add_link_selector).on('click', function(){
			$.post(user_audio_playlist.ajax_url, $.extend({},$(this).data(),{}), function(data){
				console.log('Add callback');
				console.log(data);
			}, 'json')
		});
		
		/* Action to remove items from playlist(s) */
		$(user_audio_playlist.remove_link_selector).on('click', function(){
			$.post(user_audio_playlist.ajax_url, $.extend({},$(this).data(),{}), function(data){
				console.log('Remove callback');
				console.log(data);
			}, 'json')
		})
		
	});
	
	

})( jQuery );

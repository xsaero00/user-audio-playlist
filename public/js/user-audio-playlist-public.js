/*globals window, document, jQuery, _, Backbone, _wpmejsSettings */

(function ($, _, Backbone) {
	"use strict";
	
	var UAPTrack = Backbone.Model.extend({
		  defaults: {
			  caption: null,
			  description: null,
			  id: null,
			  image: { 	height: null,
				  		src: null,
				  		width: null },
			  meta: {	album: null,
				  		artist: null,
				  		length_formatted: null,
				  		year: null },
			  src: null,
			  thumb: {	height: null,
				  		src: null,
				  		width: null	},
			  title: null,
			  type: null
		  }
		});
	
	var UAPTrackList = Backbone.Collection.extend({
		  url: user_audio_playlist.ajax_url+'?action=retrieve_playlist',
		  model: UAPTrack,
		  parse: function(data) {
		    return data.tracks;
		  }
		});

	var UAPPlaylistView = Backbone.View.extend({
		initialize : function (options) {
			this.index = 0;
			this.settings = {};
			this.data = options.metadata || $.parseJSON( this.$('script.uap-playlist-script').html() );
			this.playerNode = this.$( this.data.type );

			this.tracks = new UAPTrackList( this.data.tracks );
			this.current = this.tracks.first();

			if ( 'audio' === this.data.type ) {
				this.currentTemplate = wp.template( 'uap-playlist-current-item' );
				this.currentNode = this.$( '.uap-playlist-current-item' );
			}

			this.renderCurrent();

			if ( this.data.tracklist ) {
				this.itemTemplate = wp.template( 'uap-playlist-item' );
				this.playingClass = 'uap-playlist-playing';
				this.renderTracks();
			}

			this.playerNode.attr( 'src', this.current.get( 'src' ) );

			// what is that for?
			_.bindAll( this, 'bindPlayer', 'bindResetPlayer', 'setPlayer', 'ended', 'clickTrack' );

			if ( ! _.isUndefined( window._wpmejsSettings ) ) {
				this.settings = _wpmejsSettings;
			}
			this.settings.success = this.bindPlayer;
			this.setPlayer();
			
			this.tracks.on('reset', this.redraw, this);
		},

		bindPlayer : function (mejs) {
			this.mejs = mejs;
			this.mejs.addEventListener( 'ended', this.ended );
		},

		bindResetPlayer : function (mejs) {
			this.bindPlayer( mejs );
			this.playCurrentSrc();
		},

		setPlayer: function (force) {
			if ( this.player ) {
				this.player.pause();
				this.player.remove();
				this.playerNode = this.$( this.data.type );
			}

			if (force) {
				this.playerNode.attr( 'src', this.current.get( 'src' ) );
				this.settings.success = this.bindResetPlayer;
			}

			/**
			 * This is also our bridge to the outside world
			 */
			this.player = new MediaElementPlayer( this.playerNode.get(0), this.settings );
		},

		playCurrentSrc : function () {
			this.renderCurrent();
			this.mejs.setSrc( this.playerNode.attr( 'src' ) );
			this.mejs.load();
			this.mejs.play();
		},

		renderCurrent : function () {
			var dimensions, defaultImage = 'wp-includes/images/media/video.png';
			if ( 'video' === this.data.type ) {
				if ( this.data.images && this.current.get( 'image' ) && -1 === this.current.get( 'image' ).src.indexOf( defaultImage ) ) {
					this.playerNode.attr( 'poster', this.current.get( 'image' ).src );
				}
				dimensions = this.current.get( 'dimensions' ).resized;
				this.playerNode.attr( dimensions );
			} else {
				if ( ! this.data.images ) {
					this.current.set( 'image', false );
				}
				this.currentNode.html( this.currentTemplate( this.current.toJSON() ) );
			}
		},

		renderTracks : function () {
			var self = this, i = 1, tracklist = $( '<div class="uap-playlist-tracks"></div>' );
			this.tracks.each(function (model) {
				if ( ! self.data.images ) {
					model.set( 'image', false );
				}
				model.set( 'artists', self.data.artists );
				model.set( 'index', self.data.tracknumbers ? i : false );
				tracklist.append( self.itemTemplate( model.toJSON() ) );
				i += 1;
			});
			this.$el.append( tracklist );

			this.$( '.uap-playlist-item' ).eq(this.index).addClass( this.playingClass );
		},

		events : {
			'click .uap-playlist-item' : 'clickTrack',
			'click .uap-playlist-next' : 'next',
			'click .uap-playlist-prev' : 'prev'
		},

		clickTrack : function (e) {
			e.preventDefault();

			this.index = this.$( '.uap-playlist-item' ).index( e.currentTarget );
			this.setCurrent();
		},

		ended : function () {
			if ( this.index + 1 < this.tracks.length ) {
				this.next();
			} else {
				this.index = 0;
				this.setCurrent();
			}
		},

		next : function () {
			this.index = this.index + 1 >= this.tracks.length ? 0 : this.index + 1;
			this.setCurrent();
		},

		prev : function () {
			this.index = this.index - 1 < 0 ? this.tracks.length - 1 : this.index - 1;
			this.setCurrent();
		},

		loadCurrent : function () {
			var last = this.playerNode.attr( 'src' ) && this.playerNode.attr( 'src' ).split('.').pop(),
				current = this.current.get( 'src' ).split('.').pop();

			this.mejs && this.mejs.pause();

			if ( last !== current ) {
				this.setPlayer( true );
			} else {
				this.playerNode.attr( 'src', this.current.get( 'src' ) );
				this.playCurrentSrc();
			}
		},

		setCurrent : function () {
			this.current = this.tracks.at( this.index );

			if ( this.data.tracklist ) {
				this.$( '.uap-playlist-item' )
					.removeClass( this.playingClass )
					.eq( this.index )
						.addClass( this.playingClass );
			}

			this.loadCurrent();
		},
		
		reload : function () {
			this.tracks.fetch({reset: true});
		},
		
		redraw : function() {
			this.player.pause();
			this.$el.find('.uap-playlist-tracks').remove();
			this.renderTracks();
			this.index = 0;
			this.setCurrent();
		}
	});

	window.UAPPlaylistView = UAPPlaylistView;
	window.UAPTrackList = UAPTrackList;

}(jQuery, _, Backbone));


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
		
		
		$('.uap-playlist').each( function() {
			$(this).data('playlist', new window.UAPPlaylistView({ el: this }));
		} );
		
		
		
		/* Action to add items to playlist(s) */
		$(user_audio_playlist.add_link_selector).on('click', function(event){
			event.stopPropagation()
			$.post(user_audio_playlist.ajax_url, $.extend({},$(this).data(),{}), function(data){
				console.log('Add callback');
				console.log(data);
				$('.uap-playlist').each( function() {
					$(this).data('playlist').reload();
				} );
			}, 'json')
		});
		
		/* Action to remove items from playlist(s) */
		$(user_audio_playlist.remove_link_selector).on('click', function(event){
			event.stopPropagation()
			$.post(user_audio_playlist.ajax_url, $.extend({},$(this).data(),{}), function(data){
				console.log('Remove callback');
				console.log(data);
				$('.uap-playlist').each( function() {
					$(this).data('playlist').reload();
				} );
			}, 'json')
		})
		
		/* Action to remove items from playlist(s) */
		$(user_audio_playlist.download_link_selector).on('click', function(event){
			event.stopPropagation()
			$.post(user_audio_playlist.ajax_url, $.extend({},$(this).data(),{}), function(data){
				console.log('Download callback');
				console.log(data);
			}, 'json')
		})
		
	});
	
	

})( jQuery );

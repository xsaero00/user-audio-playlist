<?php

/**
 *
 * Class resposible for managing a playlist
 *
 */
class Playlist_Manager
{


	public function __construct($playlist_slug, $playlist_title='')
	{
		$this->playlist_slug=$playlist_slug;
		$this->playlist_title=$playlist_title;
		$this->items=array();
		$this->load();
	}

	public function __destruct() 
	{
		$this->save();
	}

    /**
    * Add an item to playlist
    */
	public function add($item)
	{
		if($this->exists($item))
			return false;
		$this->items[$this->item_key($item)]=$item;
	}

	/**
	* Check if item exists in playlist
	*/
	public function exists($item)
	{
		return isset($this->items[$this->item_key($item)]);
	}

	/**
	* Remove an item from the playlist
	*/
	public function remove($item)
	{
		unset($this->items[$this->item_key($item)]);
	}

	/**
	* Remove all items from playlist
	*/
	public function remove_all()
	{
		$this->items = array();
	}

	/**
	* Move an item to selected position in playlist
	*/
	public function move_to_position($item, $position)
	{
		// make sure requested position is valid
		if ($position < 0) return;
		if ($position >= count($this->items)) return;
		// make sure item in the playlist already
		if (!$this->exists($item)) return;

		$item_key = $this->item_key($item);		
		$items = $this->items; 
		$this->remove_all();
		$index = 0;

		foreach ($items as $key => $value) {
			if($index == $position)
			{
				$this->add($item);
				$index++;
			}
			if($key != $item_key)
			{
				$this->add($value);
				$index++;
			}
		}
		if($index == $position)
			$this->add($item);

	}

	private function item_key($item)
	{
		return md5($item);
	}

	/**
	*	Load data from storage
	*/
	private function load()
	{
		// make sure there is something to load before starting to
		if (!isset($_SESSION[UAP_SLUG]))
			return;
		
		if (isset($_SESSION[UAP_SLUG][$this->playlist_slug]) && is_array($_SESSION[UAP_SLUG][$this->playlist_slug]))
			$data = $_SESSION[UAP_SLUG][$this->playlist_slug];
		// load items
		if(isset($data['items']) && is_array($data['items']))
			$this->items=$data['items'];
		// load title
		if(!$this->playlist_title && isset($data['title']))
			$this->playlist_title = $data['title'];

	}

	/**
	* Save data to storage
	*/
	private function save()
	{
		$_SESSION[UAP_SLUG][$this->playlist_slug] = array('title'=>$this->playlist_title, 'items'=>$this->items);
	}
	
	/**
	 * Return playlist as PHP array
	 * @return array representation
	 */
	public function as_array()
	{
		return array('slug'=>$this->playlist_slug, 'title'=>$this->playlist_title, 'items'=>$this->items );
	}
	
	/**
	 * Return playlist as rendered HTML
	 */
	public function as_html()
	{
		echo "<div class='".UAP_SLUG."' id='playlist-".$this->playlist_slug."'>";
		echo "<h4 class='user_audio_playlist-title'>".$this->playlist_title."</h4>";
		if(empty($this->items))
			echo "<p>".__('Playlist is empty.')."</p>";
		else
		{
			echo "<ul>";
			foreach ($this->items as $item) {
				echo <<<END
					<audio class="wp-audio-shortcode" id="" preload="none"
				        style="width: 100%; visibility: hidden;" controls="controls">
				        <source type="audio/mpeg" src="$item?_=1"/>
				        <a href="$item">$item</a>
				    </audio>
				    <hr/>
END;
			}
			echo "</ul>";
		}
		echo "<div/>";
	}

}


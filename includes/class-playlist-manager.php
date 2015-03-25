<?php

/**
 *
 * Class resposible for managing a playlist
 *
 */
class Playlist_Manager
{
	
 	private $identifier;

	public function __construct($playlist_slug, $playlist_title='', $identifier='id')
	{
		$this->playlist_slug=$playlist_slug;
		$this->playlist_title=$playlist_title;
		$this->identifier=$identifier;
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
	public function remove($item_or_key)
	{
		unset($this->items[$item_or_key]);
		unset($this->items[$this->item_key($item_or_key)]);
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
 		if (key_exists($this->identifier, $item))
 			return $item[$this->identifier];
		return md5(serialize($item));
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
		
	}

}


<?php

class AlbumsModel
{
    private $db;

    public function __construct()
    {
        $this->db = Flight::db();
    }

    /**
     * Method to get albums from url.
     * @id int Optional. Is the album id.
     * @params mixed Array of params that can be passed for order and limit.
     * @return array
     */
    public function getAlbums($id = null, $params)
    {
		
		//Lets do a check to see if we have data. If not, then load albums.
		$sql = "SELECT count(*) AS total FROM albums";
        $result = $this->db->query($sql);
        $total = $result->fetch(PDO::FETCH_ASSOC);
        
        if($total['total'] == 0) {
			$this->loadAlbums();
		}
        
        $where = '';
        if($id !== null) {
			$where = 'WHERE album_id = '. $this->db->quote($id);
		}
        
        if(isset($params['order_col'])) {
			switch($params['order_dir']) {
				case 'asc':
				case 'ascending':
				    $direction = 'asc';
				    break;
				case 'desc':
				case 'descending':
				default:
				    $direction = 'desc';
			}
			
			$order = 'ORDER BY '. $this->db->quote($params['order_col']) . ' '. $direction;
		} else {
			$order = 'ORDER BY rank';
		}
		
		if(isset($params['limit'])) {
			$limit = 'LIMIT '. intval($params['limit']);
		} else {
			$limit = '';
		}
		$sql = "SELECT * FROM albums {$where} {$order} {$limit}";
        
        $result = $this->db->query($sql);
        
        $results = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			//Lets get album art.
			$row['art'] = $this->getAlbumArt($row['album_id']);
			
			//lets get category info as well
			$row['category'] = $this->getAlbumCategory($row['category_id']);
			
            $results[] = $row;
        }

        return $results;
    }
    
    /**
     * Method to refresh albums from url.
     * 
     */
    public function loadAlbums()
    {
		$json = file_get_contents('https://itunes.apple.com/us/rss/topalbums/limit=100/json');
        
        $albums = json_decode($json, true);
        $category_sql = "INSERT IGNORE INTO categories (name, category_id, link) VALUES (:name, :category_id, :link)";
		$album_sql = "INSERT IGNORE INTO albums (album_id, name, artist, artist_link, category_id, release_date, rank) VALUES (:album_id, :name, :artist, :artist_link, :category_id, :release_date, :rank)";
		$images_sql = "INSERT IGNORE INTO album_art (album_id, album_image, image_size) VALUES (:album_id, :album_image, :image_size)";

		//Lets delete whatever is in these tables before adding to them.
		$this->purgeTables();
        
        foreach($albums['feed']['entry'] as $rank=>$album) {
			
			//Insterts categories
			$stmt= $this->db->prepare($category_sql);
			
			$data = [
				'name' => $album['category']['attributes']['label'],
				'category_id' => $album['category']['attributes']['im:id'],
				'link' => $album['category']['attributes']['scheme'],
			];
			
			$stmt->execute($data);
			
			//Insterts album info
			$stmt= $this->db->prepare($album_sql);
			
			$data = [
				'album_id' => $album['id']['attributes']['im:id'],
				'name' => $album['title']['label'],
				'artist' => $album['im:artist']['label'],
				'artist_link' => (isset($album['im:artist']['attributes'])) ? $album['im:artist']['attributes']['href'] : '',
				'category_id' => $album['category']['attributes']['im:id'],
				'release_date' => $album['im:releaseDate']['label'],
				'rank' => $rank,
			];

			$stmt->execute($data);
			
			//inserts album images
			$stmt= $this->db->prepare($images_sql);
			
			foreach($album['im:image'] as $image){
				$data = [
					'album_id' => $album['id']['attributes']['im:id'],
					'album_image' => $image['label'],
					'image_size' => $image['attributes']['height'],
				];
				
				$stmt->execute($data);
			}
		}
	}
	
	/**
     * Method to get delete album by album id
     * @id int Required. Is the album id.
     */
	public function deleteAlbum($id)
	{
		$sql = "DELETE FROM albums WHERE album_id = :album_id";
		$stmt= $this->db->prepare($sql);
			
		$data = [
			'album_id' => $id,
		];
		
		$stmt->execute($data);
		
		//Lets also delete from album art
		$sql = "DELETE FROM album_art WHERE album_id = :album_id";
		$stmt= $this->db->prepare($sql);
			
		$data = [
			'album_id' => $id,
		];
		
		$stmt->execute($data);
	}
	
	/**
	 * Method to purge our tables of data. Used when loading in album data for the first time or refreshing. 
	 * 
	 */
	private function purgeTables()
	{	
		$this->db->query("DELETE FROM categories");
		$this->db->query("DELETE FROM album_art");
		$this->db->query("TRUNCATE TABLE albums");
		
	}
	
	
    /**
     * Method to get album art.
     * @album_id int Album id of album to get art.
     * @return array
     */
    private function getAlbumArt($album_id)
    {
		$art_sql = "SELECT album_image, image_size FROM album_art WHERE album_id = :album_id";
        $stmt= $this->db->prepare($art_sql);
			
		$data = [
			'album_id' => $album_id,
		];
			
		$stmt->execute($data);
		
		
		$results = [];
		while ($art = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $art;
		}
		
		return $results;
	}
	
	    /**
     * Method to get category info for album.
     * @category_id int.
     * @return array
     */
    private function getAlbumCategory($category_id)
    {
		//Albums can only have one category. 
		$cat_sql = "SELECT * FROM categories WHERE category_id = :category_id LIMIT 1";
        $stmt= $this->db->prepare($cat_sql);
			
		$data = [
			'category_id' => $category_id,
		];
			
		$stmt->execute($data);
		
		$results = [];
		while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$results[] = $category;
		}
		
		return $results;
	}
}

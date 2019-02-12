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
			$art_sql = "SELECT album_image, image_size FROM album_art WHERE album_id = " . $row['album_id'];
        
			$art_result = $this->db->query($art_sql);
			while ($art = $art_result->fetch(PDO::FETCH_ASSOC)) {
				unset($art['album_id']);
				$row['art'][] = $art;
			}
			
			//lets get category info as well
			$cat_sql = "SELECT * FROM categories WHERE category_id = " . $row['category_id'] .' LIMIT 1';
			$cat_result = $this->db->query($cat_sql);
			$cat = $cat_result->fetch(PDO::FETCH_ASSOC);
			$row['category'] = $cat;
			
            $results[] = $row;
        }
        
        //if no results, lets load albums. 
        if(empty($results)) {
			$this->loadAlbums();
			$this->getAlbums($id, $params);
			exit;
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
		$this->db->query("TRUNCATE TABLE albums");
		$this->db->query("TRUNCATE TABLE categories");
		$this->db->query("TRUNCATE TABLE album_art");

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
			
			//insterts album images
			
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
}

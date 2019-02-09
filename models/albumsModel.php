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
     * @return array
     */
    public function getAlbums()
    {
        
        $json = file_get_contents('https://itunes.apple.com/us/rss/topalbums/limit=10/json');
        
        $albums = json_decode($json, true);
        
        return $albums['feed']['entry'];
        
        $result = $this->db->query("SELECT * FROM {$this->table}");
        $results = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }

        return $results;
    }
    
    public function loadAlbums($count = 100)
    {
		$json = file_get_contents('https://itunes.apple.com/us/rss/topalbums/limit=10/json');
        
        $albums = json_decode($json, true);

        foreach($albums['feed']['entry'] as $album) {
			
			$sql = "INSERT IGNORE INTO categories (name, category_id, link) VALUES (:name, :category_id, :link)";
			$stmt= $this->db->prepare($sql);
			
			$data = [
				'name' => $album['category']['attributes']['label'],
				'category_id' => $album['category']['attributes']['im:id'],
				'link' => $album['category']['attributes']['scheme'],
			];
			
			$stmt->execute($data);
			
			$sql = "INSERT INTO albums (name, artist_id, artist_link, category_id, release_date) VALUES (:name, :artist_id, :artist_link, :category_id, :release_date)";
			$stmt= $this->db->prepare($sql);
			
			$data = [
				'name' => $album['title']['label'],
				'artist' => $album['im:artist']['label'],
				'artist_link' => (isset($album['im:artist']['attributes'])) ? $album['im:artist']['attributes']['href'] : '',
				'category_id' => $album['category']['attributes']['im:id'],
				'release_date' => $album['im:releaseDate']['label'],
			];
			
			$stmt->execute($data);
			$id = $this->db->lastInsertId();
		}
		
	}
}

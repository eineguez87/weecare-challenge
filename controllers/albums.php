<?php
include 'models/albumsModel.php';


class Albums
{

    public function __construct(){
        $this->albums = new albumsModel();
    }

    /**
     *
     * @return array
     */
    public function getAlbums($id, $params)
    {
        return $this->albums->getAlbums($id, $params);
        
    }
    
    public function loadAlbums() {
		$this->albums->loadAlbums();
	}

    public function deleteAlbum($id) {
		$this->albums->deleteAlbum($id);
	}

}

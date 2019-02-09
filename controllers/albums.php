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
    public function getAlbums()
    {
        return $this->albums->getAlbums();
        
    }
    
    public function loadAlbums() {
		$this->albums->loadAlbums();
	}

    /**
     *
     * @param $data
     * @return mixed
     */
    public function addComment($data)
    {
        return $this->commento->addComment($data);
    }

    /**
     * Helper function that appends child comments to its parent.
     * @param array $elements
     * @param int $parent_id
     * @return array
     */
    private function getChildComments(array $elements, $parent_id = 0, $level = 0) {
        $parent = array();
        $level++;
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parent_id) {
                $element['level'] = $level;

                $children = $this->getChildComments($elements, $element['id'], $level);
                if ($children) {
                    $element['children'] = $children;
                }
                $parent[] = $element;
            }
        }

        return $parent;
    }


}

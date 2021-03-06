<?php
require 'flight/Flight.php';
include 'controllers/albums.php';

//DB INFO
Flight::register('db', 'PDO', array('mysql:host=localhost;port=3306;dbname=weecare_challenge', 'root', ''), function($db) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
});

Flight::register('albums', 'albums');
Flight::register('albumsModel', 'albumsodel');

Flight::route('GET /albums(/@id)', function($id){
	
	$request = Flight::request();
	$params = [];
	parse_str(parse_url($request->url, PHP_URL_QUERY), $params);

    $albums = Flight::albums()->getAlbums($id, $params);
    
    Flight::json($albums, 200);

}, true);

Flight::route('DELETE /albums/@id', function($id){
	
    Flight::albums()->deleteAlbum($id);
	Flight::response(200);

});

Flight::route('POST /albums/refresh', function(){
	
    Flight::albums()->loadAlbums();
    

});


Flight::start();

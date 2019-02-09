<?php
require 'flight/Flight.php';
include 'controllers/albums.php';

//DB INFO
Flight::register('db', 'PDO', array('mysql:host=localhost;port=3306;dbname=weecare_challenge', 'root', ''), function($db) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
});

Flight::register('albums', 'albums');
Flight::register('albumsModel', 'albumsodel');

Flight::route('/', function(){
	phpinfo();
    echo 'hello world!';
});

Flight::route('GET /albums', function(){
	
    $albums = Flight::albums()->getAlbums();
    
    Flight::json($albums, 200);

});

Flight::route('POST /albums/load', function(){
	
    Flight::albums()->loadAlbums();
    
    

});


Flight::start();

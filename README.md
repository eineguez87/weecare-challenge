# weecare-challenge
----


#API Documentation
---- 
</albums/load/@id>

**METHOD**

`GET`

**URL Params**

**OPTIONAL:**
`id`
`limit=[integer]`
`order_col=[name|artist|rank|release_date]`
`order_dir=[ASC|DESC]`

**Success Response:**
Code: 200 <br />
Content: `[{    "album_id": "1350091548",
				"name": "Golden Hour - Kacey Musgraves",
				"artist": "Kacey Musgraves",
				"artist_link": "https://itunes.apple.com/us/artist/kacey-musgraves/466044182?uo=2",
				"category_id": "6",
				"release_date": "2018-03-30 00:00:00",
				"rank": "0",
				"inserted": "2019-02-12 13:03:56" 
			}, ...]`

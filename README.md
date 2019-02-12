# weecare-challenge
----


#API Documentation
---- 
</albums/load/@id>

**METHOD**

`GET`

**URL Params**

**OPTIONAL:**<br>
`id`<br>
`limit=[integer]` <br>
`order_col=[name|artist|rank|release_date]`<br>
`order_dir=[ASC|DESC]`<br>

**Success Response:**
Code: 200 <br />
Content: `[{    "album_id": "1350091548",
				"name": "Golden Hour - Kacey Musgraves",
				"artist": "Kacey Musgraves",<br>
				"artist_link": "https://itunes.apple.com/us/artist/kacey-musgraves/466044182?uo=2",
				"category_id": "6",
				"release_date": "2018-03-30 00:00:00",
				"rank": "0",
				"inserted": "2019-02-12 13:03:56" ,
				"art":[
					{
					"album_image": "https://is2-ssl.mzstatic.com/image/thumb/Music118/v4/7f/21/2a/7f212aa6-68ea-3925-a391-bbe04a2bc673/UMG_cvrart_00602567385714_01_RGB72_3000x3000_18UMGIM03879.jpg/55x55bb-85.png",
					"image_size": "55"
					},
					{
					"album_image": "https://is3-ssl.mzstatic.com/image/thumb/Music118/v4/7f/21/2a/7f212aa6-68ea-3925-a391-bbe04a2bc673/UMG_cvrart_00602567385714_01_RGB72_3000x3000_18UMGIM03879.jpg/60x60bb-85.png",
					"image_size": "60"
					},
					{
					"album_image": "https://is3-ssl.mzstatic.com/image/thumb/Music118/v4/7f/21/2a/7f212aa6-68ea-3925-a391-bbe04a2bc673/UMG_cvrart_00602567385714_01_RGB72_3000x3000_18UMGIM03879.jpg/170x170bb-85.png",
					"image_size": "170"
					}
					],
				"category":{
					"category_id": "6",
					"name": "Country",
					"link": "https://itunes.apple.com/us/genre/music-country/id6?uo=2"
					}
			}, ...]`

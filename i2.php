<?php

require './vendor/autoload.php';

$data = [];

$file = file('./sccpre_urls_short.csv', FILE_IGNORE_NEW_LINES);

$urls_and_keys = [];
foreach ($file as $key => $value) {
	$tmp = explode(',', $value);
	$urls_and_keys[$key]['url']	= $tmp[0];
	$urls_and_keys[$key]['key'] = $tmp[1];
}

foreach ($urls_and_keys as $key => $value) {

	$url = $value['url'];

	$html = file_get_contents($url);

	preg_match_all('#rel="nofollow" href="https://www.sccpre.cat/is/.+?/" >(.+?)</a>#s', $html, $ok);

	$data['tags'] = $ok[1];

	// scraping tag description

	preg_match_all('#<title>(.+?)  Free PNG Images & Clipart#', $html, $ok);

	$data['description'] = $ok[1][0];

	// оставляем только ту часть, которая до тере
	// preg_match("/(.+?)(-|$)/", $data['description'], $ok);
	// $data['description'] = trim($ok[1]);

	// cuts string in half giving less to string1 on split word (so string2 is bigger in the end)
	$middle = strrpos(substr($data['description'] , 0, floor(strlen($data['description'] ) / 2)), ' ') + 1;
	$string1 = substr($data['description'] , 0, $middle);  // "The Quick : Brown Fox "
	$string2 = substr($data['description'] , $middle);  // "Jumped Over The Lazy / Dog"
	$string2 = @str_replace('-', '', ucwords(strtolower($string2)));

	$data['description'] = $string2.' - '.ucwords(strtolower($value['key']));

	// scraping image url

	preg_match('#/show/(.+?)_#', $url, $ok);

	$url = 'https://www.sccpre.cat/maxp/'.$ok[1].'/';

	$html = file_get_contents($url);

	preg_match('#<meta property="og:image" content="(.+?)"#s', $html, $ok);

	$data['image_url'] = $ok[1];

	$client = new \GuzzleHttp\Client([
	    'headers' => [ 
			'Content-Type' => 'application/json',
			'Accept' => 'application/json'
		]
	]);

	$base64_image = base64_encode(file_get_contents($data['image_url']));

	$request = $client->post('http://localhost:8000/api/post/add',
		['body' => '{"access_token": "245344@#$%#$!@QWERFes423q45t2q3512q34twgfegh4w53542324!@#$@wefrrewt",
		    "user_id": "1",
		    "views_count": "25",
		    "downloads_count": "187",
		    "likes_count": "154",
		    "category_name": "'.$data['tags'][0].'",
		    "image_name": "'.$data['description'].'",
		    "title": "'.$data['description'].'",
		    "tags": [
		        "'.implode('","', $data['tags']).'"
		    ],
		    "description": "'.$data['description'].'",
		    "base64_image": "'.$base64_image.'"}']); 

	$response = $request->getBody(); 

	print_r($response);

}



/*"comments":[
	{
		"username": "cat",
		"comment": "Hello! Is it possible to get a commercial license for this PNG? Thank you!",
		"comment_date": "2019-05-10"
	},
	{
		"username": "mice1",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-22"    		
	},
	{
		"username": "mice2",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-22"    		
	},
	{
		"username": "mice3",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-05-30 12:00:10"    		
	},
	{
		"username": "mice4",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-10"    		
	},
	{
		"username": "mice5",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-3"    		
	},
	{
		"username": "mice6",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-5"    		
	},
	{
		"username": "mice7",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-9"    		
	},
	{
		"username": "mice8",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-12"    		
	},
	{
		"username": "mice9",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-01-22"    		
	},
	{
		"username": "mice10",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-04-22"    		
	},
	{
		"username": "mice11",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-03-22"    		
	},
	{
		"username": "mice12",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-03-10"    		
	},
	{
		"username": "mice13",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-03-10"    		
	},
	{
		"username": "mice14",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-04-01"    		
	},    
	{
		"username": "mice15",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-04-12"    		
	},
	{
		"username": "mice16",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-04-17"    		
	},
	{
		"username": "mice17",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-02-4"    		
	},
	{
		"username": "mice18",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-01-05"    		
	},
	{
		"username": "mice19",
		"comment": "This is a pretty PNG!!",
		"comment_date": "2019-01-15"    		
	}  	
],*/
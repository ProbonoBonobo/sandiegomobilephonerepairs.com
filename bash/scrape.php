<?php

// Initialize a hashmap to store the XPATH queries we'll use later
$KEYRING = array();
$KEYRING['review'] = "//div[@class='review review--with-sidebar']";
$KEYRING['name'] = "//div[@class='review review--with-sidebar']//a[@class='user-display-name']";
$KEYRING['avatar'] = "//img[contains(@src,'60s.jpg') and contains(@src, '/photo/') or contains(@src,'default_avatars')]/@src";
$KEYRING['city'] = "//li[contains(@class,'user-location')]/*";
$KEYRING['rating'] = "//div[@class='review review--with-sidebar']//meta[@itemprop = 'ratingValue']/@content";
$KEYRING['content'] = "//div[@class='review review--with-sidebar']//p[@itemprop='description']";
$KEYRING['date'] = "//div[@class='review review--with-sidebar']//meta[@itemprop='datePublished']/@content";

// fancy css sprite implementation of a little picture of '5 stars' 
$STARIMG = "<img alt='5.0 star rating' class='offscreen' height='303' src='//s3-media4.fl.yelpcdn.com/assets/srv0/yelp_styleguide/c2252a4cd43e/assets/img/stars/stars_map.png' width='84'>";

// go get the raw HTML of the yelp page
$scraped = file_get_contents("http://www.yelp.com/biz/mobile-iphone-repair-ipad-screen-repair-san-diego-24");
//$scraped = file_get_contents("./scraped.html");
$DOM = new DOMDocument();

// we'll need this to preserve the formatting of the reviews




// Defining the basic cURL function
function curl($url) {
    $ch = curl_init();  // Initialising cURL
    curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
    $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
    curl_close($ch);    // Closing cURL
    print sprintf("%s", $data);
    return $data;   // Returning the data from the function    
}




libxml_use_internal_errors(TRUE); //disable libxml errors

if(!empty($scraped)){ //if any html is actually returned

	$DOM->loadHTML($scraped);
	$scraped_xpath = new DOMXPath($DOM);  
	$scraped_row = $scraped_xpath->query("//div[@class='review review--with-sidebar']");
}


$review_list = array();

// We need to serialize the values of our data fields in a way that preserves order, so that the object's
// index maps uniquely to its identity
$review_obj = $scraped_xpath->query($GLOBALS['KEYRING']['content']);
$names = $scraped_xpath->query($GLOBALS['KEYRING']['name']);
$ratings = $scraped_xpath->query($GLOBALS['KEYRING']['rating']);
$contents = $scraped_xpath->query($GLOBALS['KEYRING']['content']);
$cities = $scraped_xpath->query($GLOBALS['KEYRING']['city']);
$avatars = $scraped_xpath->query($GLOBALS['KEYRING']['avatar']);
$dates = $scraped_xpath->query($GLOBALS['KEYRING']['date']);


if($review_obj->length > 0){  
	$ctr = 0;

	echo "[\n";

    // since index(obj) <=> identity(obj), the value of obj[key] is simply the current index of Arr[key]
    foreach($review_obj as $pat){

        //so just refocus data selectors on the current element of our data arrays 
        $name = $names->item($ctr)->nodeValue;
        $rating = $ratings->item($ctr)->nodeValue;
        $city = $cities->item($ctr)->nodeValue;
        $content = $contents->item($ctr)->nodeValue;
        $date = $dates->item($ctr)->nodeValue;
        $avatar = $avatars->item($ctr)->nodeValue;

        //sanitize the input
        $content = filter_var($content, FILTER_SANITIZE_STRING);

        //rewrite the avatar URL to the higher resolution version
//		echo "\n\n\nNow inspecting ", substr($avatar, -7),"\n\n\n";
//		echo "strcmp returns: ", strcmp(substr($avatar, -7), "60s.jpg");

		if(strcmp(substr($avatar, -7), "60s.jpg") !== 0) {
			$avatar = "img/anon.jpg";
		} else {
			$avatar = "http://". substr($avatar, 2, -7). "ms.jpg";
		}



        //and push
        $review_list[] = array('date' => $date,
        					   'name' => $name, 
        					   'city' => $city, 
        					   'avatar' => $avatar,
        					   'rating' => $rating,
        					   'stars' => $GLOBALS['STARIMG'], 
        					   'content' => $content);

        
        //but actually what we really want is JSON, which we can simply echo to stdout 
        echo "      {\n\"id\" : \"", $ctr, "\",\n";
        echo "      \"date\" : \"", $date, "\",\n";
		echo "      \"name\" : \"", $name, "\",\n";
		echo "      \"avatar\" : \"", $avatar, "\",\n";
		echo "      \"starsprite\": \"", $GLOBALS['STARIMG'], "\",\n";
		echo "      \"city\" : \"", $city, "\",\n";
		echo "      \"rating\" : \"", $rating, "\",\n";
		echo "      \"content\" : \"", $content, "\",\n";
		echo "      \"formattedContent\" : \"", $content, "\"\n";
		if ($ctr == 19) {
			// drop comma from last element of JSON table
			// todo: write a valid predicate test for this condition instead of assuming a fixed array of 20 elements
			echo "   }\n";
		} else {
			echo "   },\n"; 
		}

        $ctr += 1;
    }
//    print_r($review_list);
    echo "]";
}



?>

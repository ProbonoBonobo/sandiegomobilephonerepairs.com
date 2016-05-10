<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 4/28/16
 * Time: 1:14 AM
 *
 *  Have: a JSON object that contains approximately 20 reviews.
 *
 *  Want: A filtered JSON object that contains 3 reviews, each of which satisfies the following constraints:
 *         1. Length: short enough to fit within the bounding box (fewer than 550 chars or so)
 *         2. Rating: it's a 5 star review
 *         3. Sanity: the review object passes some heuristic sanity checks defined below (e.g., no blank fields)
 *         3. Recency: indexically speaking, each review is fresher than the one after it
 */


$f = file_get_contents('./new/scraped.json', FILE_USE_INCLUDE_PATH);
$maxLength = 550;
$filtered = [];
$json = json_decode($f,true);
$messageSignature = "tuG:6%Q8E9K8kZw+-.mgX793K=XUrrxzA4g-";



function alertMe($errorCode, $context)
{
    $hr = "\n=====================================================\n";
    if ($errorCode == 1) {
        $msg = "\nAn error has occurred! Here is the context for the error: " . $hr . $context . $hr;
        $msg = $msg . "Message signature: " . $messageSignature;
        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg, 70);
        // send email
        mail("kzeidler@gmail.com", "Fwuh oh! An error has occurred.", $msg);
    }
}


function shortEnough($review) {
    return (strlen($review) < 550);
}

function isFiveStars($rating) {
    return (strcmp($rating, "5.0") !== 1);
}

function reviewPassesInspection($output) {
    $review = $output['formattedContent'];
    $rating = $output['rating'];

    if (shortEnough($review) && isFiveStars($rating)) {
        return true;
    }
    return false;
}

function inputPassesSanityCheck($obj) {
    return true;
}

function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strnatcmp($b[$key], $a[$key]);
    };
}



if (inputPassesSanityCheck($json)) {
    $ctr = 0;
    foreach ($json as $obj) {
        foreach ($json[$ctr] as $field => $value) {


            $date = $json[$ctr]['date'];

            if (reviewPassesInspection($json[$ctr])) {


                array_push($filtered, array('date' => $json[$ctr]['date'],
                    'review' => $json[$ctr]['formattedContent'],
                    'name' => $json[$ctr]['name'],
                    'avatar' => $json[$ctr]['avatar'],
                    'city' => $json[$ctr]['city']));
            }
            $ctr += 1;

        }
    }
}
    else {
            alertMe(1,$json);
    }

$ctr = 0;

usort($filtered, build_sorter('date'));

echo json_encode(array_slice($filtered,0,3));
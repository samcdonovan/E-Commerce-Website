<?php

/* AJAX FILE */

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$database = $mongoClient->ecommerce; // main database

// search string retrieved from search input field
$searchStr = filter_input(INPUT_POST, "search", FILTER_SANITIZE_STRING);

$findCriteria = [ 
    '$text' => [ '$search' => $searchStr ]
];

/* find all products that contain that search string
e.g. if the user searches "nike", it will find products that contain nike in the brand field */
$searchProducts = $database->products->find($findCriteria);

if ($searchProducts != "") { // check that search string is not empty
    $jsonStr = '[';
    foreach ($searchProducts as $prod) {
        $jsonStr .= json_encode($prod);
        $jsonStr .= ",";
    }
    
    $jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);
    
    $jsonStr .= ']';

    echo $jsonStr; // output string containing all matching products
    return;
    
} else {
    echo "invalid";
    return;
}
?>
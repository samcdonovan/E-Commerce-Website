<?php

/* AJAX FILE */

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);

// get sorting type from AJAX
$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

$productsDB = $mongoClient->ecommerce->products; // products collection

$sortProducts = []; // initialise array which will contain sort fields

// sort changes depending on inputted type
if ($type == "ascending") {
    $sortProducts = ['sort' => ['price' => 1]];
}
else if ($type == "descending"){
    $sortProducts = ['sort' => ['price' => -1]];
}

// return all products but sorted
$sortedProducts = $productsDB->find([], $sortProducts);

// loop through products and output to a json string
$jsonStr = '[';
foreach ($sortedProducts as $prod) {
    $jsonStr .= json_encode($prod);
    $jsonStr .= ",";
}

$jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);

$jsonStr .= ']';

echo $jsonStr; // echo to AJAX function
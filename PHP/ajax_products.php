<?php

/* AJAX FILE */

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$database = $mongoClient->ecommerce;

$products = $database->products->find(); // find all products in product collection

// convert each product into json format for parsing in JS
$jsonStr = '[';
foreach ($products as $prod) {
    $jsonStr .= json_encode($prod);
    $jsonStr .= ",";
}
$jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);
$jsonStr .= ']';

echo $jsonStr; // echo the result back to JS

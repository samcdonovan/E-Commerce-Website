<?php

/* AJAX FILE */

session_start(); // start PHP session

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$database = $mongoClient->ecommerce; 
$basketDB = $database->basket; // basket collection
$customerDB = $database->customer; // customer collection

// retrieve email and password sent to the server
$email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

$_SESSION["loggedInUser"] = " "; // initialises loggedInUser session variable

$findCriteria = ["email" => $email];

/* find all of the customers that match this criteria.
  no two customers can have the same email so this will only return one customer */
$resultArray = $customerDB->find($findCriteria)->toArray();

// check that there is exactly one customer
if (count($resultArray) == 0) {
    echo 'incorrect email';
    return;
} else if (count($resultArray) > 1) {
    echo 'Database error: Multiple customers have same email address.';
    return;
}

// get customer and check password
$customer = $resultArray[0];
if ($customer['password'] != $password) {
    echo 'incorrect password';
    return;
}

/* if the customer has a basket open while they're not logged in we want to set the customer 
id of that basket to the current customers id. this effectively assigns that basket to that customer. */

$customerArray = $customerDB->find($findCriteria);

foreach ($customerArray as $cust) {
    // set $customerId to the id of the logged in customer    
    $customerId = $cust['customerId'];
}

$updateBasket = [
    '$set' => [
        "customerId" => $customerId,
    ]
];

// find basket with empty customerId field
$basketSearch = [
    'customerId' => "",
];

// update the basket to contain their id
$basket = $basketDB->updateOne($basketSearch, $updateBasket);

// set 'loggedInUser' to the users email address
$_SESSION['loggedInUser'] = $email;

echo "success"; // inform web page that login is successful

?>
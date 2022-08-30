<?php

/* AJAX FILE */

require __DIR__ . '/vendor/autoload.php';

$mongoClient = (new MongoDB\Client);

$database = $mongoClient->ecommerce; // main database

$customerDB = $database->customer; // customer collection

// retrieve all fields from JS post request
$firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
$surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

// flag received from JS to verify that the post is valid
$postCheck = filter_input(INPUT_POST, 'postCheck', FILTER_SANITIZE_STRING);

$customer = [ // customer array consisting of relevant fields
    "firstName" => $firstName,
    "surname" => $surname,
    "address" => $address,
    "email" => $email,
    "phone" => $phone,
    "password" => $password
];

if ($postCheck == "true") { // check that the post from JS is valid
    $findCriteria = [
        'email' => $email
    ];
    
    if (count($customerDB->find($findCriteria)->toArray()) == 0) { // check that there isn't already a customer with that email
       // echo "lol";
        $addCustomer = $customerDB->insertOne($customer); // insert customer into customer collection
    } else {
        echo "Invalid email"; // if a customer exists with that email return invalid email
        return;
    }

    $getCustomerId = [
        '$set' => ["customerId" => $customerDB->findOne($findCriteria)['_id']]
    ];

    if ($addCustomer->getInsertedCount() == 1) { // check that customer was inserted correctly

        // find the customer that was just inserted and set "customerId" to their _id
        $addCustomerId = $customerDB->updateOne($findCriteria, $getCustomerId);

        echo "success";
    } else {
        echo 'Error adding customer.';
    }
}
?>
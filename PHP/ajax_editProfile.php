<?php

/* AJAX FILE */

session_start();
require __DIR__ . '/vendor/autoload.php';

$mongoClient = (new MongoDB\Client);

$database = $mongoClient->ecommerce;

$customerDB = $database->customer; // customer collection

$userEmail = $_SESSION['loggedInUser']; // user email retrieved from session variable

// fields received from edit profile post form
$firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
$surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

// array which sets the specified fields to the corresponding form inputs
$customer = [
    '$set' => [
        "firstName" => $firstName,
        "surname" => $surname,
        "address" => $address,
        "email" => $email,
        "phone" => $phone,
    ]
];

/* find user with current user email (customers cannot have the same email address
  so this will only return one customer) */
$findCriteria = [
    'email' => $userEmail,
];

// update the current customers document in the customer collection
$updateCustomer = $customerDB->updateOne($findCriteria, $customer);

// update should be successful, otherwise there is a database error
if ($updateCustomer->getModifiedCount() == 1) {
    echo "success";
} else {
    echo "error";
}
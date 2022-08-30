<?php

/* AJAX FILE */

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$basketDB = $mongoClient->ecommerce->basket; // basket collection
$productDB = $mongoClient->ecommerce->products; // products collection

session_start(); //Start session management

/* checks if the customer is logged in. This is to distinguish between the customer
  and staff sign out */
if (!empty($_SESSION['loggedInUser'])) {

    $findCustomer = ['email' => $_SESSION['loggedInUser']];
    $customer = $mongoClient->ecommerce->customer->find(['email' => $_SESSION['loggedInUser']]);

    foreach ($customer as $cust) {
        $customerId = $cust['customerId']; // retrieve customerId from customer
    }

    $basketProducts = []; // array to push products into
    
    //find customers basket
    $basketSearch = [
        'customerId' => $customerId,
    ];

    /* when the customer logs out, if they still have products in their basket we want to move them
      back to the products collection, and then delete the basket */
    $basket = $basketDB->find($basketSearch);

    // push current products in basket to basketProducts array
    foreach ($basket as $bask) {
        foreach ($bask['products'] as $product) {

            array_push($basketProducts, $product);
        }
    }

    // loop through the products in the basket
    foreach ($basketProducts as $productInBasket) {

        $findProduct = [
            'productId' => $productInBasket['productId'],
        ];

        // find the current product using the product id in the basket
        $productDoc = $productDB->find($findProduct);

        foreach ($productDoc as $prod) {

            $currentStock = $prod['stock']; // set current stock to stock of product
        }

        // add count back to the current stock
        $resetStock = [
            '$set' => [
                "stock" => $currentStock + $productInBasket['count'],
            ]
        ];

        // update product in database, effectively resetting the stock
        $updateProductStock = $productDB->updateOne($findProduct, $resetStock);
    }
    // if the user currently has a basket when they log out, that basket gets deleted.
    $deleteBasket = $basketDB->deleteOne($basketSearch);
}

/* remove all session variables and destroy the session,
  effectively logs the user out */
session_unset();
session_destroy();

echo "signOut";

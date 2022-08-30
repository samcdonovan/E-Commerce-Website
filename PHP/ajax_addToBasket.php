<?php

/* AJAX FILE */

/*----------------------------------------------------------------------*/
/* Helper Functions for product info retrieval */

// function that takes a product array and returns an array consisting of
// the total count and total price of the product
function getTotals($searchArray) {
    $totalCount = 0;
    $totalPrice = 0;
    $totals = [];
    for ($i = 0; $i < count($searchArray); $i++) {
        $totalCount += $searchArray[$i]['count'];
        $totalPrice += $searchArray[$i]['price'];
    }
    $totals = [
        "count" => $totalCount,
        "price" => $totalPrice,
    ];
    return $totals;
}

// function that searches all the products in a basket.
// it finds the product which has the $id id and then returns
// a value depending on the specified type.
function getProdInfo($searchArray, $id, $type) {

    for ($i = 0; $i < count($searchArray); $i++) {

        if ($searchArray[$i]['productId'] === $id && $type === "count") {
            // returns the count of the item
            return $searchArray[$i]['count'];
        }

        if ($searchArray[$i]['productId'] === $id && $type === "price") {
            // returns the price of the item
            return $searchArray[$i]['price'];
        }
        if ($searchArray[$i]['productId'] === $id && $type === "pos") {
            // returns the position in the array of the item
            return $i;
        }
    }

    return -1; // if the product does not exist in the array
}

/*----------------------------------------------------------------------*/
/* Basket Retrieval */

session_start(); // start session

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$database = $mongoClient->ecommerce; // ecommerce database
$basketDB = $database->basket; // basket collection
$productsDB = $database->products; // products collection

// get product id from the basket function associated with each item
$productId = filter_input(INPUT_POST, "productId", FILTER_SANITIZE_STRING);

$customerEmail = ""; // customerEmail is empty until changed 
$customerId = ""; // customerId is empty until changed
$basketProducts = []; // array used to store all products that are in the basket

//checks if the customer has logged in
if (isset($_SESSION['loggedInUser'])) {
    // sets email to the email stored in the session
    $customerEmail = $_SESSION['loggedInUser'];
    // if it has not been set customerEmail stays as an empty string
}

/* if the customerEmail has been set, i.e. the user is logged in,
 find that customer in the database */
if ($customerEmail != NULL || $customerEmail != "") {
 
    $customer = $database->customer->find(["email" => $customerEmail]);

    foreach ($customer as $cust) {

        // set customerId to the id associated with the logged in customer
        $customerId = $cust['customerId'];
    }
}

// find criteria for basket
$findBasket = [
    // searches for the customer Id
    "customerId" => $customerId,
];

// find basket in database and convert it to an array
$basket = iterator_to_array($basketDB->find($findBasket));

/* if the basket does not exist, create a new basket
  with the customer id and an empty products array. */
if (empty($database->basket->findOne($findBasket))) {
    $createBasket = [
        "customerId" => $customerId,
        "products" => []
    ];
    $insert = $basketDB->insertOne($createBasket);
}

/*----------------------------------------------------------------------*/
/* Product Retrieval */

/* search to see if there already exists a basket 
 with that customer id and product id */
$basketFind = [
    'customerId' => $customerId,
    'products.productId' => new MongoDB\BSON\ObjectID($productId),
];

$productInBasket = $basketDB->find($basketFind);

// if that basket exists, push the products in the basket into an array.
foreach ($productInBasket as $prod) {
    foreach ($prod['products'] as $searchItem) {
        // push current product to basketProducts array
        array_push($basketProducts, $searchItem);
    }
}

$productFind = [
    'productId' => new MongoDB\BSON\ObjectID($productId)
];

// find the relevant product in the products collection and then converts it into an array.
// it is converted to an array so that we can access the relevant fields
$product = iterator_to_array($productsDB->findOne($productFind));

/*----------------------------------------------------------------------*/
/* Add Product to basket */

/* when a product is added to the basket, the stock count of that product is reduced.
    if the customer logs out without checking out, the stock count returns to 
    its previous value (see signOut.php). if they check out then the stock count remains reduced. */

if ($product['stock'] > 0) { // checks that stock count is valid

    // if there is enough stock, set the stock of the product to current stock -1.
    $productArray = [
        '$set' => ["stock" => $product['stock'] - 1]
    ];
    $updateStock = $productsDB->updateOne($productFind, $productArray);

    // if the product does not exist in the basket, getProdInfo returns -1
    if (getProdInfo($basketProducts, $productId, "pos") == -1) {

        // push the product into the products array of the basket
        $basketArray = [
            '$push' => ["products" =>
                [
                    "productId" => new MongoDB\BSON\ObjectID($productId),
                    "count" => 1,
                    "price" => $product['price'],
                ]
            ]
        ];
    } else { // if the product DOES already exist in the basket
        
        /* getProdInfo is used to find the position of the product in the product array in the basket.
         it is then used to retrieve the current count and price in that array, and increase them */
        $basketArray = [
            '$set' => [
                "products." . getProdInfo($basketProducts, $productId, "pos") . ".count" => 
                getProdInfo($basketProducts, $productId, "count") + 1,
                "products." . getProdInfo($basketProducts, $productId, "pos") . ".price" => 
                getProdInfo($basketProducts, $productId, "price") + $product['price'],
            ]
        ];
    }
} else { // if there is < 0 in the stock count.
    echo 'invalid stock';
    return;
}

/*----------------------------------------------------------------------*/
/* Basket Update */

// update the basket using the basket array
$updateBasket = $basketDB->updateOne($findBasket, $basketArray);

$basketProducts = []; // reset the basket array

//use basketFind criteria created earlier
$productInBasket = $basketDB->find($basketFind);

// foreach product in the basket, push to basketProducts
// this is repeated from earlier because there are now new products in the basket.
foreach ($productInBasket as $prod) {
    foreach ($prod['products'] as $searchItem) {
        array_push($basketProducts, $searchItem);
    }
}

echo json_encode(getTotals($basketProducts)); // echo the total count and price

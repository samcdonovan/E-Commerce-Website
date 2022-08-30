<?php

/* AJAX FILE */

/*----------------------------------------------------------------------*/
/* Helper Functions */

/* function to check if each product in the product collection search matches  
  a product in the customers order collection. This is done to avoid recommending products
   that the customer has already purchased. */
  function checkMatch($customerOrders, $prodName) {
    foreach ($customerOrders as $orders) {
        foreach ($orders['order'] as $products) {
            if ($products['name'] == $prodName) {
                return true;
            }
        }
    }
    return false;
}

/*----------------------------------------------------------------------*/
/* Database */

session_start(); // start PHP session
require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$database = $mongoClient->ecommerce;
$ordersDB = $mongoClient->ecommerce->orders; // orders collection
$customersDB = $mongoClient->ecommerce->customer; // customer collection
$productsDB = $mongoClient->ecommerce->products; // customer collection


/*----------------------------------------------------------------------*/
/* Order Retrieval */

// retrieve search string from the search input field
$searchStr = filter_input(INPUT_POST, "search", FILTER_SANITIZE_STRING);

$findProduct = [
    '$text' => ['$search' => $searchStr]
];

// retrieve all products that match that search string
$searchProducts = $productsDB->find($findProduct);

if (!empty($_SESSION['loggedInUser'])) { // check if user is logged in
    $findCustomer = [
        'email' => $_SESSION['loggedInUser'],
    ];
    // find customer with the logged in email address
    $customer = iterator_to_array($customersDB->find($findCustomer));

    foreach ($customer as $cust) {
        // set customerId to that customers id
        $customerId = $cust['customerId'];
    }

    $findCustomersOrders = [
        'customerId' => $customerId,
    ];
    // find all orders in the order collection that contain that customer id
    $customerOrders = iterator_to_array($ordersDB->find($findCustomersOrders));
}

$count = 0; // variable used to make sure only up to 4 products are outputted

/*----------------------------------------------------------------------*/
/* Output Products */

if ($searchProducts != "") { // check that search string is not empty
    $jsonStr = '[';
    foreach ($searchProducts as $prod) { // loop through all products that match the search string
        if (!empty($_SESSION['loggedInUser'])) { // check if user is logged in

            /* if the user is logged in, only output the products that they have not previously purchased.
              this is what checkMatch is used for */
            if (!checkMatch($customerOrders, $prod['name']) && $count < 4) {
                $count++;
                $jsonStr .= json_encode($prod);
                $jsonStr .= ",";
            }
        } else { // if the user is not logged in, output the first 4 products that match the string
            if ($count < 4) {
                $count++;
                $jsonStr .= json_encode($prod);
                $jsonStr .= ",";
            }
        }
    }

    $jsonStr = substr($jsonStr, 0, strlen($jsonStr) - 1);

    $jsonStr .= ']';

    echo $jsonStr; // echo the json string containing up to 4 recommended products
    return;

} else { // if the search string is empty
    echo "invalid";
    return;
}
?>
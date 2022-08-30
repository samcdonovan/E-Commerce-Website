<?php
include_once ("globalFunctions.php");

general_header("Basket");
navigation_bar("Basket");

session_start(); // start session

/* ---------------------------------------------------------------------- */
/* Helper Functions */

// PHP function that gets either the count or price of given product
function getBasketDetails($id, $type) {
    global $basketArray;
    for ($i = 0; $i < count($basketArray); $i++) {

        if ($basketArray[$i]['productId'] == $id && $type == "count") {
            return $basketArray[$i]['count'];
        }
        if ($basketArray[$i]['productId'] == $id && $type == "price") {
            return $basketArray[$i]['price'];
        }
    }
    return -1;
}

/* ---------------------------------------------------------------------- */
/* Basket and Product Database Retrieval */

require __DIR__ . '/vendor/autoload.php';
$mongoClient = (new MongoDB\Client);
$database = $mongoClient->ecommerce;
$basketDB = $database->basket; // basket collection

$customerId = ""; // customerId remains empty if user is not logged in
$basketArray = []; // array used to put all products from the basket into another array
$productsArray = []; // array that stores product objects of the products in the basket
$checkoutArray = []; // array holds all the information to submit to the order document

// totalCount and totalPrice initialisation
$totalCount = 0;
$totalPrice = 0;

if (isset($_SESSION['loggedInUser'])) { // checks if a user is logged in
    $customerEmail = $_SESSION["loggedInUser"]; // retrieves users email from the session

    // finds that customer in the customer collection
    $customer = $database->customer->find(["email" => $customerEmail]);

    foreach ($customer as $cust) {
        $customerId = $cust['customerId']; // sets customerId to the id of the customer in the collection
    }
} else {
    // if the user is not logged in, the email is empty
    $customerEmail = "";
}

// finds the basket with the corresponding customer id
$findBasket = [
    "customerId" => $customerId,
];

$basket = $basketDB->find($findBasket); // users basket
// pushes each product from the basket into an array
foreach ($basket as $prod) {
    foreach ($prod['products'] as $searchItem) {
        array_push($basketArray, $searchItem);
    }
}

foreach ($basketArray as $item) {

    // get each product from the basket and find that product in the product collection
    $findProduct = [
        "_id" => new MongoDB\BSON\ObjectID($item['productId']),
    ];

    $product = $database->products->find($findProduct);

    foreach ($product as $prod) {

        // push the product with all product fields into an array
        array_push($productsArray, $prod);
    }
}
?>

<!-- ---------------------------------------------------------------------- -->
<!-- Basket displayed on page -->

<div class="basket" id="basketCheckout"> <!-- checkout basket div -->

    <p class="header">Basket</p> <!-- header for the basket -->

<?php
// for every product in productArray, output all the relevant info onto the page
foreach ($productsArray as $productArray) {
    echo '<div class = "item">';
    echo '<img src="' . $productArray['image'] . '">';
    echo '<div class = "itemInfo">';
    echo '<p>' . $productArray['name'] . '</p>';
    echo '<p>' . $productArray['colour'] . '</p>';
    echo '<p>Size ' . $productArray['size'] . '</p>';
    echo '</div>';
    echo '<div class = "quantity">';

    // get the count and price of the current item in the basket
    echo '<p>QTY : ' . getBasketDetails($productArray['productId'], "count") . '</p>';
    echo '<p>£' . getBasketDetails($productArray['productId'], "price") . '</p>';
    echo '</div>';
    echo '</div>';

    // info to store in the order document
    $orderInfo = [
        "productId" => $productArray['productId'],
        "name" => $productArray['name'],
        "count" => getBasketDetails($productArray['productId'], "count"),
        "price" => getBasketDetails($productArray['productId'], "price"),
    ];

    // push order info into the checkout array
    array_push($checkoutArray, $orderInfo);

    // add count and price to total count and total price
    $totalCount += getBasketDetails($productArray['productId'], "count");
    $totalPrice += getBasketDetails($productArray['productId'], "price");
}
?>

    <!-- checkout section of the basket -->
    <div class="checkout"> 
        <div class="totalsText"> <!-- text describing what is being shown -->
            <p>Total Price:</p>
            <p>Number of items:</p>
        </div>
        <div class="totals" id="totals"> <!-- actual total price and number of items -->
            <p>£0</p>
            <p>0</p> 
        </div>
        <button onclick="showCheckout()">Checkout</button> <!-- button to checkout -->
    </div>  
</div>

<!-- ---------------------------------------------------------------------- -->
<!-- User Details and order submission -->

<!-- form that is displayed when the user presses "checkout".
     this form is for the user to enter their details for this order.
     this form posts to this page. -->
<form action="basket.php" method="post" id="checkoutForm" class="hide">
    <p>Please enter your details:</p>
    <div>
        <label for="firstName">First name:</label><span class="required">*</span>
        <input type = "text" placeholder = "First name" id="firstName" name="firstName" required="required"><br>
    </div>
    <div>
        <label for="surname">Surname:</label><span class="required">*</span>
        <input type = "text" placeholder = "Surname" id="surname" name="surname" required="required"><br>
    </div>
    <div>
        <label for="address">Address:</label><span class="required">*</span>
        <input type="text" placeholder = "Address" id="address" name="address" required="required">
    </div>
    <div>
        <label for="email">Email Address:</label><span class="required">*</span>
        <input type="email" placeholder="Email Address" id="email" name="email" required="required"><br>
    </div>

    <!-- finaliseCheckout() is a function used to reset the sessionStorage basket after the order is submitted
    and set the checkoutFlag to true (explained later) -->
    <button name="submit" onclick="finaliseCheckout()">Confirm Order</button>

</form>

<?php

$ordersDB = $mongoClient->ecommerce->orders; // orders collection

// user details retrieved from the checkout form
$firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
$surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);

// array consisting of all order fields
$insertArray = [
    "customerId" => $customerId,
    "firstName" => $firstName,
    "surname" => $surname,
    "address" => $address,
    "email" => $email,
    "date" => date("d/m/Y"), // current date
    "order" => $checkoutArray, // array holding the actual order (products)
    "totalCount" => $totalCount,
    "totalPrice" => $totalPrice,
];

if (isset($_POST['submit'])) { // checks if the submit button has been pressed

    // if the form has been submitted, output the confirmation screen.
    echo '<div id="confirmation">';

    // the arrival date of the order is the current date plus 7 days
    $arrivalDate = date('d/m/Y', strtotime(' + 7 days'));

    echo '<p>Thank you for ordering at Sneak Trainers! A confirmation email containing the details of this order has been sent 
        to ' . $email . '. Your order should arrive by ' . $arrivalDate . '.</p>';
    echo '<p>You can view your order history on your Profile page.</p>';
    echo '<p>If you have any issues, please contact our support team at STsupport@mdx.ac.uk</p>';
    echo '</div>';

    // creates the order and deletes the basket
    $createOrder = $ordersDB->insertOne($insertArray);
    $deleteBasket = $basketDB->deleteOne($findBasket);
}
?>

<!-- JS functions used for displaying different relevant info on the screen -->
<script>
    window.onload = checkConfirmation(); // checks if the user has just submitted their order
    window.onload = getBasket(); // loads the total price and count into the totals box at checkout

    /* function that puts the totals from the sessionStorage basket into the
     basket page, at the checkout screen */
    function getBasket() {
        let count = JSON.parse(sessionStorage.getItem("basket")).count;
        let price = JSON.parse(sessionStorage.getItem("basket")).price;

        // sets totals to the totals found in the session storage basket array
        document.getElementById("totals").innerHTML = "<p>£" + price + "</p>" + "<p>" + count + "</p>";

    }

    /* function that hides the basket and shows the checkout form.
     called when the user presses the "checkout" button */
    function showCheckout() {
        document.getElementById("basketCheckout").classList.toggle("hide");
        document.getElementById("checkoutForm").classList.toggle("hide");
    }

    /* function that resets the sessionStorage basket and sets the checkoutFlag to true.
     called when the user submits their details at checkout */
    function finaliseCheckout() {

        var basket = {count: 0, price: 0};

        // sets the session storage basket values back to 0.
        sessionStorage.setItem("basket", JSON.stringify(basket));

        /* checkoutFlag is used to check if the user has just checked out. 
         it is used to check if the confirmation page should be displayed. */
        sessionStorage.setItem("checkoutFlag", true);
    }

    // function that checks if checkoutFlag is true and hides the basketCheckout 
    function checkConfirmation() {
        if (sessionStorage.checkoutFlag) {

            // hides the basket so that there is no overlap with the confirmation message
            document.getElementById("basketCheckout").classList.toggle("hide");

            /* removes checkoutFlag, as it is only used to check
             if the user has just submitted an order */
            sessionStorage.removeItem("checkoutFlag");

        }
    }
</script>
<?php
general_footer();
?>

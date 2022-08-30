<?php
include_once("cmsGeneral.php");

cms_header("View/Delete Orders");
cms_navBar("View/Delete Orders");

?>

<main>

    <!-- JS function to check staff is logged in -->
    <script>window.onload = cmsCheckLogin("View/Delete-Orders");</script>
        
<!-- ---------------------------------------------------------------------- -->
<!-- Customer order search form -->

    <div class ="cms" id ="View/Delete-Orders">

        <!-- carries out a get request on submission -->
        <form action="cmsViewOrders.php" method="get">

            <!-- user searches for an order by its ID -->
            <p>Order Search:</p>
            <input type="text" placeholder = "Order ID" name = "id">
            <button>Submit</button>

        </form>
        <p>-------------------------------</p>

        <!-- ---------------------------------------------------------------------- -->
        <!-- Database Section -->

        <?php
        require __DIR__ . '/vendor/autoload.php';
        $mongoClient = (new MongoDB\Client);

        $ordersDB = $mongoClient->ecommerce->orders; // orders collection
        
        // id will be sent when the user types in an id and presses "submit"
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

        /* ---------------------------------------------------------------------- */
        /* Order retrieval */

        // PHP array with id search criteria
        $findCustomerOrders = [
            'customerId' => new MongoDB\BSON\ObjectID($id), //convert to BSON for id search
        ];

        $cursor = $ordersDB->find($findCustomerOrders); // find all orders that match this criteria
        $i = 0; // iterator to get the number of orders
        
        // if $cursor is not empty, loop through all orders and print relevant info
        foreach ($cursor as $order) {

            /* echo a form with all order information, and a delete button.
              when this button is pressed, a post request is made and that order is deleted */
            echo '<form action="" method="POST">
                <p>Order ID : ' . (string) $order['_id'] . '</p>
                <input type="hidden" name = "orderId" value = "' . (string) $order['_id'] . '">
                <p>Customer ID : ' . $order['customerId'] . '</p>
                <p>Shipping Address : ' . $order['address'] . '</p>
                <p>Items : ';

            $productsInOrder = iterator_to_array($order['order']); // convert the nested order array into an array

            foreach ($productsInOrder as $product) { // loop through all products in the order
                
                // output name, date, total count and total price of each order
                echo '<p>ID : ' . $product['productId'] . '<p>' . $product['name'] . '</p><p>QTY: ' . $product['count'] . '</p>';
            }

            echo '<p>Date : ' . $order['date'] . '</p>
                <p>Quantity : ' . $order['totalCount'] . '</p>
                <p>Total Price : ' . $order['totalPrice'] . ' </p>
                <button type="submit" name="delete" value="delete' . $i . '" >Delete Order</button>
                </form>';
            /* the delete button deletes the order that it is assigned to. 
              the value of the button is delete + i (which is the current position of the order in the array) */

            echo '<p>-------------------------------</p>';

            $i++; // add one to iterator to get the total number of orders
        }

        /* ---------------------------------------------------------------------- */
        /* Order Deletion */

        if (!empty($_POST["delete"])) { // checks if "delete" has been pressed
            for ($j = 0; $j < $i; $j++) { // loops from 0 to i (number of orders)
                $deleteStr = "delete" . $j;

                // checks if the value of the delete button is the same as the current iteration
                if ($_POST["delete"] == $deleteStr) {

                    $orderId = $_POST["orderId"]; // retrieved from the post form which was submitted by the delete button

                    $findOneOrder = [// finds the order with that id
                        '_id' => new MongoDB\BSON\ObjectId($orderId),
                    ];

                    $deleteOrder = $ordersDB->deleteOne($findOneOrder); // deletes that order
                }
            }
        }
        ?>
    </div>
</main>
        
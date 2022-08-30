<?php
include_once("cmsGeneral.php");

cms_header("Edit Products");
cms_navBar("Edit Products");
?>

<!-- checks that staff is logged in -->
<script>window.onload = cmsCheckLogin("Edit-Products");</script>

<div class="cms" id="Edit-Products">

<!-- ---------------------------------------------------------------------- -->
<!-- Product Search Form -->

    <!-- user searches for a product by its id-->
    <form action="cmsEditProducts.php" method="get">
        <p>Product Search:</p>

        <input type="text" placeholder = "Product ID" name = "id">
        <button>Submit</button>
    </form>

    <p>-------------------------------</p>

    <?php
    session_start();
    require __DIR__ . '/vendor/autoload.php';

    $mongoClient = (new MongoDB\Client);

    $db = $mongoClient->ecommerce;

    /* ---------------------------------------------------------------------- */
    /* Product Output */

    // id will be sent when the user types in an id and presses "submit"
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

    $findProduct = [
        "productId" => new MongoDB\BSON\ObjectID($id),
    ];
    // find the product with the id that the user inputted
    $cursor = $db->products->find($findProduct);

    foreach ($cursor as $product) {
        // loop through product and output all fields
        echo '<p>Id : ' . $product['productId'] . '</p>
            <p>Name : ' . $product['name'] . '</p>
            <p>Gender : ' . $product['gender'] . '</p>
            <p>Brand : ' . $product['brand'] . '</p>
            <p>Colour : ' . $product['colour'] . '</p>
            <p>Size : ' . $product['size'] . '</p>
            <p>Price : ' . $product['price'] . ' </p>
            <p>Stock : ' . $product['stock'] . ' </p>';


        echo '<p>-------------------------------</p>';

        // store product id in id variable
        $id = $product['productId'];
    }
    ?>

<!-- ---------------------------------------------------------------------- -->
<!-- Product Edit Form -->

    <!-- form that allows the user to enter data which will edit the product -->
    <form action="cmsEditProducts.php" method="post">

        <p>Enter the new details of the product:</p>
        <input type="text" placeholder = "Name" name="name">
        <input type="text" placeholder = "Gender" name="gender">
        <input type="text" placeholder = "Brand" name="brand">
        <input type="text" placeholder = "Colour" name="colour">
        <input type="text" placeholder = "Size" name="size">
        <input type="text" placeholder = "Price" name="price">
        <input type="text" placeholder = "Stock" name="stock">

        <?php

        /* use id variable to post id for later use. This was done because submitting
         the search form would reload the page and set $id back to an empty string */
        echo '<input type="hidden" name="productId" value=' . $id . '>';
        ?>

        <button name="editSubmit">Submit</button> <!-- user presses submit when they are ready -->
    </form>

    <?php

    /* ---------------------------------------------------------------------- */
    /* Product Edit Form Submission */

    // retrieve all new product details from post form
    $id = filter_input(INPUT_POST, 'productId', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
    $colour = filter_input(INPUT_POST, 'colour', FILTER_SANITIZE_STRING);
    $size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_STRING);

    if (isset($_POST['editSubmit'])) { // check if edit submit button was pressed

        // set product to new fields
        $editedProduct = [
            '$set' => [
                "name" => $name,
                "gender" => $gender,
                "brand" => $brand,
                "colour" => $colour,
                "size" => (int) $size,
                "price" => (int) $price,
                "stock" => (int) $stock,
            ]
        ];

        // find product using id variable (from hidden form)
        $findProduct = [
            "productId" => new MongoDB\BSON\ObjectID($id),
        ];

        $editProduct = $db->products->updateOne($findProduct, $editedProduct);

        // check that the edit was successful
        if ($editProduct->getModifiedCount() == 1) {
            echo "Success";
        } else {
            echo "Error";
        }
    }
    ?>

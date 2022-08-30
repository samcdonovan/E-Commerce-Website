<?php
include_once("cmsGeneral.php");

cms_header("View Products");
cms_navBar("View Products");
?>

<main>

    <!-- check that staff is logged in -->
    <script>window.onload = cmsCheckLogin("View-Products")</script>

    <div class="cms" id="View-Products">

        <p>-------------------------------</p>

        <?php
        require __DIR__ . '/vendor/autoload.php';

        $mongoClient = (new MongoDB\Client);

        $db = $mongoClient->ecommerce; // main database

        $cursor = $db->products->find(); // all products in product collection
        
        // loop through every product and print all fields
        foreach ($cursor as $product) {
            echo '<p>ID : ' . $product['productId'] . '</p>
            <p>Name : ' . $product['name'] . '</p>
            <p>Gender : ' . $product['gender'] . '</p>
            <p>Brand : ' . $product['brand'] . '</p>
            <p>Colour : ' . $product['colour'] . '</p>
            <p>Size : ' . $product['size'] . '</p>
            <p>Price : ' . $product['price'] . ' </p>
            <p>Stock : ' . $product['stock'] . ' </p>';

            echo '<p>-------------------------------</p>';
        }
        ?>
    </div>
</main>


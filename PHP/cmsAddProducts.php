<?php
include_once("cmsGeneral.php");

cms_header("Add Products");
cms_navBar("Add Products");
?>

<main>

<!-- check for staff login -->
<script>window.onload = cmsCheckLogin("Add-Products");</script> 

<!-- ---------------------------------------------------------------------- -->
<!-- HTML forms for adding a product -->

    <div class = "cms" id="Add-Products">

    <!-- there are two forms on this page; the main product info form and the image upload form -->
        <form id="mainForm" action="cmsAddProducts.php" method="post"></form>
        <form id="imageForm" action="" method="post" enctype="multipart/form-data"></form>

        <div>
            <!-- image upload form -->
            <p>Image to upload (please upload the image before entering the details):</p>
            <input type="file" name="imageToUpload" form="imageForm">
            <input type="submit" value="Upload Image" name="imageSubmit" form="imageForm">
        </div>
        <!-- prompt the user to enter details of the item -->
        <p>Enter the details of the new item below:</p>

        <!-- user must then fill out all of the below fields -->
        <input type="text" placeholder = "Name" name="name" form="mainForm">
        <input type="text" placeholder = "Gender" name="gender" form="mainForm">
        <input type="text" placeholder = "Brand" name="brand" form="mainForm">
        <input type="text" placeholder = "Colour" name="colour" form="mainForm">
        <input type="text" placeholder = "Size" name="size" form="mainForm">
        <input type="text" placeholder = "Price" name="price" form="mainForm">
        <input type="text" placeholder = "Stock" name="stock" form="mainForm">
        <input type="text" placeholder = "Keywords" name="keywords" form="mainForm">  

        <button type="submit" form="mainForm" name="submit" value="submit">Submit</button> 
        
    </div>
</main>

<?php
require __DIR__ . '/vendor/autoload.php'; // library include

$mongoClient = (new MongoDB\Client);

$db = $mongoClient->ecommerce;

$collection = $db->products; // products collection

/*---------------------------------------------------------------------- */
/* Image Upload */

if (!empty($_POST['imageSubmit'])) { // check that 'imageSubmit' has been pressed
    // if the files is missing, $_FILES will not contain an array key of that file
    if (!array_key_exists("imageToUpload", $_FILES)) {
        echo 'File missing.';
        return;
    }
    // if the uploaded file does not have a name, return
    if ($_FILES["imageToUpload"]["name"] == "" || $_FILES["imageToUpload"]["name"] == null) {
        echo 'File missing.';
        return;
    }

    $uploadFileName = $_FILES["imageToUpload"]["name"];

    /*  Check if image file is a actual image or fake image
      tmp_name is the temporary path to the uploaded file. */
    if (getimagesize($_FILES["imageToUpload"]["tmp_name"]) === false) {
        echo "File is not an image.";
        return;
    }

    // Check that the file is the correct type
    $imageFileType = pathinfo($uploadFileName, PATHINFO_EXTENSION); // gets the file type
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG & PNG  files are allowed.";
        return;
    }

    $target_file = '../images/' . $uploadFileName; // file path for the file

    /* Files are uploaded to a temporary location. 
      Need to move file to the location that was set earlier in the script */
    if (move_uploaded_file($_FILES["imageToUpload"]["tmp_name"], $target_file)) {
        echo "The file " . basename($_FILES["imageToUpload"]["name"]) . " has been uploaded.";
        echo '<p>Uploaded image: <img src="' . $target_file . '"></p>'; //Include uploaded image on page
        
        // hidden form to send the filepath to the database
        echo '<input type="hidden" name="image" form="mainForm" value="' . $target_file . '">';

    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

/* ---------------------------------------------------------------------- */
/* Product Info Form Submission */

// retrieve data from form to upload the document
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
$colour = filter_input(INPUT_POST, 'colour', FILTER_SANITIZE_STRING);
$size = filter_input(INPUT_POST, 'size', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
$stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_STRING);
$keywords = filter_input(INPUT_POST, 'keywords', FILTER_SANITIZE_STRING);

// the image filepath is retrieved from a hidden form when the image is uploaded
$image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);

if (!empty($_POST['submit'])) { // check that the 'submit' button was pressed
    $dataArray = [ // PHP array containing all document fields
        "name" => $name,
        "gender" => $gender,
        "brand" => $brand,
        "colour" => $colour,
        "size" => (int) $size,
        "price" => (int) $price,
        "stock" =>(int) $stock,
        "keywords" => $keywords,
        "image" => $image, // retrieved from hidden form
    ];

    $insertResult = $collection->insertOne($dataArray); // add document to products collection

    $getProductId = [// set "productId" to the same value as "_id", for foreign key use
        '$set' => ["productId" => $collection->findOne($dataArray)['_id']]
    ];

    if ($insertResult->getInsertedCount() == 1) { // check that document was inserted into collection

        // update product with productId field
        $addProductId = $collection->updateOne($dataArray, $getProductId);
        
        echo 'Product added.';
        echo 'New document id: ' . $insertResult->getInsertedId(); // echo back the id of the inserted document
    } else {
        echo 'Error adding product';
    }
}
?>
<?php
// much like the customer facing pages, the CMS has header and navBar functions
// these are called in every CMS page to avoid duplicate code.
include_once("cmsGeneral.php");

cms_header("Login");
cms_navBar("Login");
?>

<!-- ---------------------------------------------------------------------- -->
<!-- Login Form -->

<!-- login form, posts to current page -->
<form class="cms" action="cmsLogin.php" method="post" >

    <p>You must login to use the staff CMS</p>
    <label for="username">Username:</label><br>
    <input type="text" name="username" placeholder="username"><br>

    <label for="password">Password:</label><br>
    <input type="password" name="password" placeholder="password">

    <button name="login" id= "login">Login</button> 

</form>

<?php

/* ---------------------------------------------------------------------- */
/* Login Database validation */

session_start(); // PHP session start
require __DIR__ . '/vendor/autoload.php';

$mongoClient = (new MongoDB\Client);
$staffDB = $mongoClient->ecommerce->staff; // staff collection

// gets username and password from the login form
$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

// finds the staff member with that username
$staff = $staffDB->find(["username" => $username]);

$count = 0; // used to count the number of documents

 // checks if the staff member exists
    foreach ($staff as $staffMember) {

        $count = 1; // if a staff member with that username exists, count = 1

        // checks if entered password is same as password associated with that staff member
        if ($staffMember['password'] == $password) {
            $_SESSION['staffLoggedIn'] = true;
            echo "Logged in";
        } else {
            echo "Incorrect password";
        }
    }

    // check if the login button has been pressed and that the username does not exist
if (isset($_POST["login"]) && $count == 0){
    echo "Incorrect username";
}

?>

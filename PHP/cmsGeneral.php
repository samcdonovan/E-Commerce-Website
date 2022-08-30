<?php

// this file was created to avoid code duplication for the CMS navigation.
// it is identical to the globalFunctions.php file, except all relevant links
// are for the CMS pages instead.
function cms_header($currentPage) {
    echo '<DOCTYPE html>';
    echo '<html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';

    // takes the page input and sets the title of the current page to that input
    echo '<title>' . $currentPage . '</title>';
    echo '<link rel="stylesheet" href="../CSS/styles.css">';
    echo '<script src = "../JS/login.js"></script>';
    echo '</head>';
    echo '<header>';

    echo '<div class="title">';
    echo '<h2>Staff Content Management System</h2>';
    echo '</div>';

    // button for returning to the customer pages
    echo '<div class="return">';
    echo '<a href="index.php"><button onclick="cmsSignOut()">Return to customer site</button></a>';
    echo '</div>';
    
}

// arrays that hold the names of the web pages and their corresponding php files;
// initialised outside of the function for use in two different functions
$pageName = array("Login", "View Products", "Add Products", "Edit Products", "View/Delete Orders");
$pageLink = array("cmsLogin.php", "cmsViewProducts.php", "cmsAddProducts.php", "cmsEditProducts.php", "cmsViewOrders.php");

// navigation bar function; generates a basic navigation bar.
// takes $currentPage input to check what page the user is currently on
function cms_navBar($currentPage)
{

    // calling the previously created arrays using "global" to be used in this function
    global $pageName, $pageLink;

    echo '<div class="navBar">';

    //uses a for loop to create the navigation bar using the arrays
    for ($x = 0; $x < count($pageName); $x++) {
        echo '<a ';

        if ($currentPage == $pageName[$x]) {
            echo 'class="current"';
        }

        // uses the current iteration of the for loop to retrieve the page name and
        // php link for that page, and create their links along with an icon that
        // corresponds to those links
        echo 'href="' . $pageLink[$x] . '">' . $pageName[$x] . '</a>';
    }
    echo '</div>';

    echo '</header>';

}
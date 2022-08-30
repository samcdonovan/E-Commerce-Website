<?php

// header function;
// takes $currentPage as an input to assign a title relative to the current page
function general_header($currentPage)
{
    echo '<DOCTYPE html>';
    echo '<html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';

    // takes the page input and sets the title of the current page to that input
    echo '<title>' . $currentPage . '</title>';
    echo '<link rel="stylesheet" href="../CSS/styles.css">';
    echo '<script src="../JS/products.js"></script>';
    echo '</head>';
    echo '<header>';

    // empty title to fill space on screen
    echo '<div class="title">';
    echo '</div>';

    // logo created by us
    echo '<div class="logo">';
    echo '<img src="../images/logo.png">';
    echo '</div>';

    // searchBar that sits in the top right hand corner of the page
    echo '<div class="searchBar">';
    echo '<div>';
    echo '<input type="text" placeholder="Search..." name="search" id="search">';
    echo '<input type="image" src="../images/search.png" id="searchButton">';
    echo '</div>';

    // basket icon and link that takes user to basket.php
    echo '<div class="headerBasket">';
    echo '<img src="../images/basket.png">';

    // these values are updated whenever the user adds a product to their basket
    echo '<a href="basket.php" id="basket"> 0 items / £0  </a>'; 
    echo '</div>';
    echo '</div>';
    echo '<script>loadBasket();</script>';
}

// arrays that hold the names of the web pages and their corresponding php files;
// initialised outside of the function for use in two different functions
$pageName = array("Home", "Shop", "About Us", "Profile");
$pageLink = array("index.php", "shop.php", "aboutUs.php", "profile.php");
$navIcons = array("index.png", "shop.png", "aboutUs.png", "profile.png");

// navigation bar function;
// takes $currentPage input to check what page the user is currently on
function navigation_bar($currentPage)
{

    // calling the previously created arrays using "global" to be used in this function
    global $pageName, $pageLink, $navIcons;

    echo '<div class="navBar">';

    //uses a for loop to create the navigation bar using the arrays
    for ($x = 0; $x < count($pageName); $x++) {
        echo '<a ';

        // if statement to assign the home class to the home link in the navigation bar;
        // this is because the home button on the bar has slightly different
        // styling than the rest of the buttons
        if ($pageName[$x] == "Home" && !($currentPage == "Home")) {
            echo 'class ="home"';
        }

        // if statement to check what the current page is (i.e. what page has been
        //passed to this function), and if it is the same page as the page being
        // returned in the current iteration of the for loop, set the class of this page to "current"
        if ($currentPage == $pageName[$x]) {
            echo 'class="current"';
        }

        // uses the current iteration of the for loop to retrieve the page name and
        // php link for that page, and create their links along with an icon that
        // corresponds to those links
        echo 'href="' . $pageLink[$x] . '">' . $pageName[$x] . '<img src="../images/' . $navIcons[$x] . '" alt="' . $pageName[$x] . '"></a>';
    }
    echo '</div>';

    echo '</header>';
}

// footer function; takes no input
function general_footer()
{

    // calling the arrays again for use in a for loop
    global $pageName, $pageLink;

    echo '<footer>';

    // quick links/navigation list in the footer
    echo '<div class="quickLinks">';
    echo '<h>NAVIGATE</h>';
    echo '<ul>';

    // for loop to create the quick links in the footer;
    for ($x = 0; $x < count($pageName); $x++) {
        echo '<li><a href="' . $pageLink[$x] . '">' . $pageName[$x] . '</a></li>';
    }

    echo '</ul>';
    echo '</div>';

    //sign up section with subscribe button
    echo '<div class="signUp">';
    echo '<h>SIGN UP TO WEEKLY NEWSLETTERS</h>';
    echo '<p>Enter your email below to sign up to a weekly newsletter where we talk ';
    echo 'about all things tetris!</p>';
    echo '<form action="#">';
    echo '<input type="text" placeholder="Email Address" name="mail">';
    echo '<button type="button">SUBSCRIBE</button>';
    echo '</form>';
    echo '</div>';

    // social media icons with links to respective pages
    echo '<div class="socials">';
    echo '<h>FOLLOW US</h>';
    echo '<a href = "https://www.facebook.com" target="_blank"><img src="../images/facebookLogo.png" alt="FACEBOOK"></a>';
    echo '<a href = "https://www.instagram.com" target="_blank"><img src="../images/instagramLogo.png" alt="INSTAGRAM"></a>';
    echo '<a href = "https://www.twitter.com" target="_blank"><img src="../images/twitterLogo.png" alt="TWITTER"></a>';
    echo '<a href = "https://www.linkedin.com" target="_blank"><img src="../images/linkedinLogo.png" alt="LINKEDIN"></a>';
    echo '</div>';

    // short paragraph about this project
    echo '<div class="footerAbout">';
    echo '<h>ABOUT US</h>';
    echo' <p>This is an e-commerce website that we have created for uni. <br>
            Sneak Trainer is a website that sells Trainers, and has all <br>
            relevant e-commerce features, such as an order log, a profile, <br>
            and a basket, as well as a Staff Content Management System.<br>';
    echo '</p>';
    echo '</div>';

    // small text at the bottom with privacy and copyright details
    echo '<div class="policies">';
    echo '<p>Privacy Policy</p>';
    echo '<p>Sitemap</p>';
    echo '<p>© 2021 Uni Project</p>';
    echo '</div>';

    echo '</footer>';

    echo '</body>';
    echo '</html>';
}

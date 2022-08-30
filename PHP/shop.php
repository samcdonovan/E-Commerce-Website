<?php
include_once("globalFunctions.php");

general_header("Shop");
navigation_bar("Shop");
?>

<!-- ---------------------------------------------------------------------- -->
<!-- Shop HTML -->

<main>

    <!-- banner for the top of the shop page -->
    <div class="shopBanner">
        <h1>Trainers</h1>
        <p>From the latest drops to court classics, lace up in a 
            fresh pair of creps with our range of men’s footwear. All 
            your favourite brands like adidas Originals, Nike, Lacoste 
            and Vans are bringin’ you trainers for any event, Whether 
            you’re copping a pair of Sneak Trainer Exclusives or an iconic street staple, 
            keep your look fresh and bold with every step.
        </p>
    </div>

    <!-- sort options that sit above the products -->
    <div class="sort">
        <p>Sort by price:</p>

        <div>
            <!-- buttons allowing the user to sort the products in ascending or descending order -->
            <button id="ascSort" onclick="loadContent()">Low to High &#x2193</button>
            <button id="descSort" onclick="loadContent()">High to Low &#8593</button>
        </div>
    </div>

    <!-- recommendations div, hidden by default -->
    <div class="recommendations hide" id="recommendations"></div>

   <div class="products" id="products"> <!-- div to put all products into using ajax -->

        <script>loadContent();</script> <!-- JS function to load all products -->
    
    </div> 

</main>

<!-- ---------------------------------------------------------------------- -->
    <!-- Script (Search and recommendations functions) -->

<script type='module'>
    "use strict";

    import { Recommender } from '../JS/recommender.js'; // import Recommender class from recommender.js

    let recommender = new Recommender(); // initialise Recommender obj

    /* ---------------------------------------------------------------------- */
    /* AJAX Callback Functions */

    /* main callback function for AJAX post requests. 
    takes post url, callback function and search string as arguments. */
    function postCallback(url, callback, searchStr) {

        let request = new XMLHttpRequest(); // creates new HTTP request

        request.onload = function () {
            if (request.status === 200) {

                 /* this callback is used with functions that search through the products database.
                    if the product does not exist, the post request returns "invalid" */
                
                    if (request.responseText !== "invalid") { // check for valid request

                        callback(request); // call the callback function

                    } else if (request.responseText === "invalid") {
                        alert("Database error"); // if the request returns "invalid", there is a database error
                    }
            }
                else
                    alert("Error communicating with server: " + request.status);
        };

        // post the searchStr to the specified URL
        request.open("POST", url);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("search=" + searchStr);
    }

    /* callback function used to display the items base on users search */
    function loadSearch(xhttp) {
        let productArray = JSON.parse(xhttp.responseText); // convert to JSON

        displayProducts(productArray, "search"); // display products with type "search"
    
        // call displayRecommendations callback to display relevant recommendations to customer
        postCallback("ajax_recommendations.php", displayRecommendations, recommender.getTopKeyword()); 
    }
    
    /* callback function used to display recommendations to user */
    function displayRecommendations(xhttp) {

        // converts response data into JSON format
        let productArray = JSON.parse(xhttp.responseText);

        // check that there is at least one word in the recommender
        if (recommender.getKeywordLength() != 0) {

            // calls displayProducts on the product array with type "recommendations"
            displayProducts(productArray, "recommendations");
        }
    }

    /* ---------------------------------------------------------------------- */
    /* Event listeners */

    /* event listener for searchButton */
    document.getElementById("searchButton").addEventListener("click", function() {

        // when a search is performed, the search value is added to the recommender and loadSearch() is called
        let search = document.getElementById("search").value;
        recommender.addKeyword(search);
        postCallback("ajax_searchProducts.php", loadSearch, search);
    });

    if (recommender.getKeywordLength() != 0) { 

        /* when the window has loaded, the current top keyword is passed into the displayRecommendations callback
        method and all relevant recommendations are displayed */
        window.onload = postCallback("ajax_recommendations.php", displayRecommendations, recommender.getTopKeyword()); 
    }

</script>
<?php
general_footer();
?>


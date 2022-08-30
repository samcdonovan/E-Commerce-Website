
window.onload = loadBasket; // load the session storage basket onload

/* ---------------------------------------------------------------------- */
/* Product Display Functions */

/* AJAX function that loads all products onto the shop page. 
called when the page is loaded and also when the sort buttons are pressed.*/
function loadContent() {

    var request = new XMLHttpRequest();

    request.onload = function () {

        if (request.status === 200) {

            let responseData = request.responseText;

            let productArray = JSON.parse(responseData);

            /* if the sort by price (ascending) button is clicked,
            products are sorted and displayed in ascending sorted order */
            document.getElementById('ascSort').onclick = function () {

                sort("ascending");
                return;

            };

            /* if the sort by price (descending) button is clicked,
            products are sorted and displayed in descending order */
            document.getElementById('descSort').onclick = function () {
                sort("descending");
                return;

            };

            // if no button is pressed, display products in normal order
            displayProducts(productArray, "load");
        }
        else
            alert("Error communicating with server: " + request.status);
    };

    // get request to products.php to get all products
    request.open("GET", "ajax_products.php");
    request.send();
}

/* main function used to display products.
it is called inside of loadContent() and loadSearch, so is called when they are called.
takes a product array and a type as parameter.
type is used solely to determine if this function is being called to display the recommendations. */
function displayProducts(productArray, type) {

    let output = ""; // output which will be used to load the products onto the page
    let arrLength = productArray.length;
    var objId;

    /* if this function is being called to display recommendations, output includes recommendation text,
    and the recommendations div is no longer hidden */
    if (type === "recommendations") {
        output += "<p id='recommendText'>Based on your search history, we recommend these trainers:";
        if (document.getElementById("recommendations").classList.contains("hide")) {
            document.getElementById("recommendations").classList.toggle("hide");
        }
    }

    /* nested for loops used to make sure that only 4 products are shown 
     in each row on the shop page. */
    for (var i = 1; i < Math.ceil(productArray.length / 4) + 1; i++) {
        output += "<div class='row'>";

        for (var j = productArray.length - arrLength; j < i * 4; j++) {
            arrLength -= 1;

            /* for each product in the current row output the image, name, 
            price, addToBasket icon and current stock */
            output += "<div class='column'>";
            output += "<img src='" + productArray[j].image + "'>";
            output += "<p>" + productArray[j].name + "</p>";
            output += "<p>£" + productArray[j].price;

            /* objId is the id of the product. Substr is used to remove the 
            "ObjectId("")" parts of the string so that objId is just the value of the id */
            objId = JSON.stringify(productArray[j].productId).substr(9, 24);

            /* objId is passed to addToBasket(), so each product calls addToBasket 
            with its own ID as the parameter */
            output += '<img src="../images/basket.png" class="productBasket" onclick="addToBasket(\'' + objId + '\')"></p>';
            output += "<p>Stock : " + productArray[j].stock + "</p>";
            output += "</div>";

            if (arrLength === 0) { // if there are no more items, break out of the loop
                break;
            }
        }
        output += "</div>";
    }

    /* if this is being called to display recommendations, output is sent to the recommendations div.
    otherwise, output is setn to the products div */
    if (type === "recommendations") {
        document.getElementById("recommendations").innerHTML = output;
    } else {
        document.getElementById("products").innerHTML = output;
    }
}

/* AJAX sort function, called when the user presses one of the sort buttons.
it takes the sort type as a parameter (i.e ascending or descending) */
function sort(type) {
    var request = new XMLHttpRequest();
    request.onload = function () {
        if (request.status === 200) {
            let responseData = request.responseText;

            /* product array will be all of the products in the database, 
            but sorted in the specified order */
            let productArray = JSON.parse(responseData);

            // display the now sorted products on the shop page
            displayProducts(productArray, "load");

        }
        else
            alert("Error communicating with server: " + request.status);
        return null;
    };
    // post the type of sorting to sort.php 
    request.open("POST", "ajax_sort.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("type=" + type);
}

/* ---------------------------------------------------------------------- */
/* Basket Functions */

/* AJAX function for adding products to the basket document (in the database), and for
updating the sesssion storage basket values.  
This function is called when the basket icon for each product is pressed, and it takes
that products ID as an argument. */
function addToBasket(productId) {
    var request = new XMLHttpRequest();

    request.onload = function () {
        if (request.status === 200) {

            let responseData = JSON.parse(request.responseText);

            if (responseData !== "invalid stock") {
                /* if there is valid stock, responseData will be an JSON object 
                consisting of all products in the basket */

                var basket = { count: responseData.count, price: responseData.price };
                sessionStorage.setItem("basket", JSON.stringify(basket));

                /* update session storage with current total quantity and total price of the basket,
                and update the basket info in the top right of the page (which persists across all pages) */
                document.getElementById("basket").innerHTML = responseData.count + " items / £ " + responseData.price;

            } else if (responseData === "invalid stock") {
                alert("Not enough stock!");
            }
        }
        else
            alert("Error communicating with server: " + request.status);
    };

    // post request to addToBasket.php, sends the product id
    request.open("POST", "ajax_addToBasket.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("productId=" + productId);
}

/* function that is called on every page.
updates the basket info in the top right of the page whenever a change is
made to the sessionStorage basket */
function loadBasket() {
    if (sessionStorage.getItem("basket") === null) {
        // if the sessionStorage basket does not yet exist, initialises it
        sessionStorage.setItem("basket", JSON.stringify({ "count": 0, "price": 0 }));
    }
    let count = JSON.parse(sessionStorage.getItem("basket")).count;
    let price = JSON.parse(sessionStorage.getItem("basket")).price;

    /* basket info in top right of page now reflects quantity of items 
    and price which are in the basket document */
    document.getElementById("basket").innerHTML = count + " items / £ " + price;
}
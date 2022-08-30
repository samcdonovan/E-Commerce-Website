
/* ---------------------------------------------------------------------- */
/* Login Functions */

// simple AJAX function that runs whenever a page is loaded; checks whether or not a user is logged in
function checkLogin() {
    let request = new XMLHttpRequest();

    request.onload = function () {
        if (request.responseText === "true") {// checks if a user is logged in

            // if user logs in successfully, the login, noAccount and staff divs are hidden
            document.getElementById("login").classList.toggle("hide");
            document.getElementById("noAccount").classList.toggle("hide");
            document.getElementById("staff").classList.toggle("hide");

        } else {
            // if user is not logged in, hide profile info and orders table
            document.getElementById("profileInfo").classList.toggle("hide");
            document.getElementById("orders").classList.toggle("hide");
        }
    };

    /* checkLogin.php is used for both staff and customer login, so
    for customer login, the type is customer and that is sent to checkLogin.php */
    request.open("POST", "ajax_checkLogin.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("type=customer");
}

// AJAX login function, called when login button is pressed
function login() {
    let request = new XMLHttpRequest();

    request.onload = function () {

        if (request.status === 200) {

            var responseData = request.responseText;

            if (responseData === "incorrect password") { // if the entered password is not the correct password

                if (document.getElementById("loginError").classList.contains("show")) {
                    // shows a popup error for the password being incorrect
                    document.getElementById("loginError").innerHTML = "Password not correct. Please try again.";
                } else {
                    document.getElementById("loginError").classList.toggle("show");
                    document.getElementById("loginError").innerHTML = "Password not correct. Please try again.";
                }
            } else if (responseData === "incorrect email") { // if the entered username does not exist in localStorage

                // shows the popup error for if the username is not recognised
                document.getElementById("loginError").innerHTML = "Email not recognised. Do you have an account?";
                document.getElementById("loginError").classList.toggle("show");
            } else { // checks that the entered password is the same associated with the user

                document.getElementById("loginError").innerHTML = ""; // empties the popup message
                document.getElementById("login").classList.toggle("hide"); // hides the login form
                document.getElementById("noAccount").classList.toggle("hide"); // hides register box
                document.getElementById("staff").classList.toggle("hide"); // hides staff login box

                document.getElementById("profileInfo").classList.toggle("hide"); // shows profile info
                document.getElementById("orders").classList.toggle("hide"); // shows orders table
                location.reload(); // page reload so that the PHP in the profile page is updated

                if (document.getElementById("loginError").classList.contains("show")) { // checks if the error popup is still showing
                    document.getElementById("loginError").classList.toggle("show"); // hides the popup if it is still there
                }
            }
        }
    };
    // gets email and password from user input
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    // sends user and password to login.php
    request.open("POST", "ajax_login.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("email=" + email + "&password=" + password);
}

// AJAX function for signing out
function signOut() {

    let request = new XMLHttpRequest();
    // when the button is pressed, the login form is hidden and the loggedIn element is shown in its place
    request.onload = function () {

        if (request.responseText === "signOut") {

            document.getElementById("login").classList.toggle("hide"); // hides the login form
            document.getElementById("noAccount").classList.toggle("hide"); // hides register box
            document.getElementById("staff").classList.toggle("hide"); // hides staff box

            document.getElementById("profileInfo").classList.toggle("hide"); // shows profile info
            document.getElementById("orders").classList.toggle("hide"); // shows orders box

            // set session storage basket to empty
            var basket = { count: 0, price: 0 };
            sessionStorage.setItem("basket", JSON.stringify(basket));
        }
    };
    // get request to signOut.php; removes all session vars and destroys session
    request.open("GET", "ajax_signOut.php");
    request.send();
}

/* ---------------------------------------------------------------------- */
/* CMS Login Functionality */

/* AJAX check login function for the cms pages.
takes and elementId as the argument */
function cmsCheckLogin(elementId) {
    let request = new XMLHttpRequest();
    request.onload = function () {

        // if the request returns "true" the user is logged in
        if (request.responseText !== "true") {

            /* sets the innerHTML of the cms div on the current page to a message telling
            the user that they must sign in to use instanceof. This effectively stops them from using
            the cms functions if they are not signed in */
            document.getElementById(elementId).innerHTML = "<p>You must be logged in to access this functionality. " +
                "Please go to the <a href = 'cmsLogin.php'>Staff Login page</a> to login.</p>";
        }

    };

    /* sends type = staff to checkLogin.php so that it can 
    check if the staff is logged in and return "true" if it is */
    request.open("POST", "ajax_checkLogin.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("type=staff");
}

/* basic AJAX sign out function, sends a get request to signOut.php
 which removes all session variables and destroys the session, as well
 as deleting the basket. */
function cmsSignOut() {
    let request = new XMLHttpRequest();
    request.open("GET", "ajax_signOut.php");
    request.send();
}

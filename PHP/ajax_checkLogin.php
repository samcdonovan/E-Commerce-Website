<?php

/* AJAX FILE */

session_start(); // start session

// retrieves the check login type. This will either be "customer" or "staff"
$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

if ($type == "customer") {

    // if the type is customer, check that loggedInUser exists in the session
    if (array_key_exists("loggedInUser", $_SESSION)) {
        echo "true";
    } else {
        echo 'Not logged in.';
    }
}

// if the type is staff, check that staffLoggedIn exists in the session
if ($type == "staff") {
    if (array_key_exists("staffLoggedIn", $_SESSION)) {
        echo "true";
    } else {
        echo 'Not logged in.';
    }
}
    


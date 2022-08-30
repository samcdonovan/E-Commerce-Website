<?php
include_once ("globalFunctions.php");

general_header("Profile");
navigation_bar("Profile");
?>

<script src="../JS/login.js"></script>

<!-- ---------------------------------------------------------------------- -->
<!-- HTML Section (lets the user log in, register or go to the CMS) -->

<body onload="checkLogin()"> <!-- on loading, checks to see if user is logged in -->
    <main>

        <!-- login box for if the user has an account -->
        <div class="login" id="login"> 
            <p>Existing customer?</p>

            <label for="email">Email:</label><br>
            <input type="text" id="email" name="email"><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password">

            <button id="login" onclick ="login()">Login</button> <!-- login button that calls login() -->
            <div class="popup" id="loginPopup"> <!-- error popup -->
                <p class="feedback" id="loginError"></p>
            </div>
        </div>

        <!-- register button for if the user does not have an account -->
        <div class="noAccount" id="noAccount"> 
            <p>No account? Create one now!</p>
            <a href="register.php"><button>Register</button></a>
        </div>

        <!-- button that takes the user to the staff login -->
        <div class="staff" id="staff">
            <p>Staff?</p>
            <a href="cmsLogin.php"><button>Staff CMS Login</button></a>
        </div>
        <div class="profileInfo" id="profileInfo">

            <!-- ---------------------------------------------------------------------- -->
            <!-- Database section (used to retrieve the users details and orders) -->

            <?php
            require __DIR__ . '/vendor/autoload.php';

            $mongoClient = (new MongoDB\Client);
            $database = $mongoClient->ecommerce;
            $customerDB = $database->customer; // customer collection
            $ordersDB = $database->orders; // orders collection

            $customerEmail = ""; // initialise customerEmail var, will remain empty if user has not logged in
            $customerId = ""; // initialise customerId var

            session_start(); // start PHP session

            if (!empty($_SESSION['loggedInUser'])) { // check that the user has logged in
                $customerEmail = $_SESSION["loggedInUser"]; // set customerEmail to their email
            }

            /* ---------------------------------------------------------------------- */
            /* Profile Info Section */

            // profile div, shows the user their profile information and allows them to edit and signout
            echo '<div id="profile">
                <img src = "../images/profilePic.png" alt = "PROFILE_PIC">';

            // finds the user with the logged in email, returns nothing if they are not logged in
            $customer = $customerDB->find(["email" => $customerEmail]);
            
            foreach ($customer as $cust) { // loop through the customer array and output their information

                $customerId = $cust['customerId']; // set customerId to their id

                /* output the customer infor into the profile div as 
                  well as an edit profile and sign out button */
                echo '<p id="username">First name: ' . $cust['firstName'] . '</p> 
                    <p>Surname: ' . $cust['surname'] . '</p>
                    <p>Email: ' . $cust['email'] . '</p>
                    <p>Address: ' . $cust['address'] . '</p>
                    <p id="phone">Phone: ' . $cust['phone'] . '</p>

                    <button onclick = "editProfile()">Edit</button>
                   
                    <button name="signOut" value="signOut" onclick = "signOut()">Sign Out</button>';
            }
            echo '</div></div>'; // close profile and profileInfo divs

            /* ---------------------------------------------------------------------- */
            /* Customer Orders Section */

            $findCriteria = [
                "customerId" => $customerId,
            ];

            // search orders collection for all orders associated with that customerId
            $customerOrders = $ordersDB->find($findCriteria);

            // orders table, retrieves order information and outputs to the table
            echo '<table class="orders" id="orders">
                <caption>Orders</caption>
                <thead>
                <th>Date</th><th>Recipient</th><th id="user">Item(s)</th><th>No. of items<th>Total Price</th>
                </thead>
                <tbody>';

            // loop through all the orders in the order collection and output their info to table
            foreach ($customerOrders as $orders) {

                // data for order date and recipients first name and surname
                echo '<tr><td>' . $orders['date'] . '</td> 
                     <td>' . $orders['firstName'] . ' ' . $orders['surname'] . '</td>';

                echo '<td>'; // open data tag for product info

                // products is a nested array within the order, so it must be converted to an array
                $productsInOrder = iterator_to_array($orders['order']);
                foreach ($productsInOrder as $product) {

                    // loop through all products in the order and output their name and stock count
                    echo '<p>' . $product['name'] . '</p><p>x ' . $product['count'] . '</p>';
                }

                echo '</td>'; // close tag for product info

                // last two columns contain the total quantity and price of the order
                echo '<td>' . $orders['totalCount'] . '</td>
                     <td>' . $orders['totalPrice'] . '</td></tr>';
            }

            echo '</tbody></table>'; // close table
            ?>

            <!-- ---------------------------------------------------------------------- -->
            <!-- Edit Profile Section -->

            <!-- edit form, remains hidden until user presses "editProfile" -->
            <div class="edit hide" id="edit">
                <p>Please enter your new details below:</p>
                <div>
                    <label for="firstName">First name:</label>
                    <input type = "text" placeholder = "First name" id="editFirstName" name="firstName"><br>
                </div>
                <div>
                    <label for="surname">Surname:</label>
                    <input type = "text" placeholder = "Surname" id="editSurname" name="surname"><br>
                </div>
                <div>
                    <label for="address">Address:</label>
                    <input type="text" placeholder = "Address" id="editAddress" name="address">
                </div>
                <div>
                    <label for="phone">Phone No.:</label>
                    <input type="text" placeholder = "Phone Number" id="editPhone" name="phone">
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="text" placeholder = "Email Address" id="editEmail" name="email">
                </div>

                <!-- submitEdit() is an ajax function that sends this 
                data to the server in order to update the customers details -->
                <button onclick="submitEdit()">Submit</button> 

                <!-- Return button returns user back to profile info page -->
                <button onclick="editProfile()">Return</button>
            </div> 
    </main>

<!-- ---------------------------------------------------------------------- -->
<!-- JS Functions -->

<script>

/* basic function for hiding the "profile" div and showing the "edit" div
called when the user presses editProfile */
function editProfile() {
    document.getElementById("profile").classList.toggle("hide");
    document.getElementById("edit").classList.toggle("hide");
}

// AJAX function that posts edit form to editProfile.php
function submitEdit() {
    let request = new XMLHttpRequest();

    request.onload = function () {
        if (request.status === 200) {

            let responseData = request.responseText;
            if (responseData == "success") {
                location.reload();
            } else {
                alert("Database Error");
            }
        }
        else
            alert("Error communicating with server: " + request.status);
    };

    // gets edit form data and puts them into appropriate variables
    let firstName = document.getElementById("editFirstName").value;
    let surname = document.getElementById("editSurname").value;
    let address = document.getElementById("editAddress").value;
    let email = document.getElementById("editEmail").value;
    let phone = document.getElementById("editPhone").value;

    /* sends this data to editProfile.php where it is validated
     and the customer document is edited */
    request.open("POST", "ajax_editProfile.php");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
    request.send("firstName=" + firstName + "&surname="
        + surname + "&address=" + address + "&email=" + email + "&phone=" + phone);
}

</script>
    <?php
    general_footer();
    ?>
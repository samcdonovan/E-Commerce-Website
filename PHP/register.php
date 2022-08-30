<?php
include_once ("globalFunctions.php");

general_header("Register");
navigation_bar("Register");
?>

<main>

    <!-- ---------------------------------------------------------------------- -->
    <!-- Form for registering an account on this website; -->

    <div>
        <h1 id="registerHeader">Please enter your details below to register and create an account</h1>

        <div class="register" id="register">   
            <p>Fields with <span class="required">*</span> are required</p>
            <div>
                <label for="firstName">First name:</label><span class="required">*</span>
                <input type = "text" placeholder = "First name" id="firstName" name="firstName" required="required"><br>
            </div>
            <div>
                <label for="surname">Surname:</label><span class="required">*</span>
                <input type = "text" placeholder = "Surname" id="surname" name="surname" required="required"><br>
            </div>
            <div>
                <label for="address">Address:</label><span class="required">*</span>
                <input type="text" placeholder = "Address" id="address" name="address" required="required">

            </div>
            <div>
                <label for="email">Email Address:</label><span class="required">*</span>
                <input type="email" placeholder="Email Address" id="email" name="email" required="required"><br>
                <div class="popup" id = "popup1">
                    <p class="feedback" id="emailError"> ""</p>
                </div>
            </div>
            <div>
                <label for="phone">Phone Number:</label>
                <input type="text" placeholder="Phone Number" id="phone" name="phone"><br>
            </div>
            <div>
                <label for="passwrd">Password:</label><span class="required">*</span>
                <input type="password" placeholder="Password" id="passwrd" name="passwrd" required="required">
                <div class="popup" id="popup2">
                    <p class="feedback" id="passwordError">""</p>
                </div>
            </div>
            <button name="register" onclick = "registerAccount()">Create Account</button>

        </div>

    </div>
    
<!-- ---------------------------------------------------------------------- -->
<!-- JS Registration Functions -->
    <script> 
    
// function to check that all required parts of the form are filled in
// takes an array as input. This is to avoid empty documents in the database.
function checkFilledIn(formCheck) {
    let formString = ["firstName", "surname", "address", "email", "passwrd"];

    // loops through the passed array (which will consist of the username, email and password
    // that the user inputted), and if any of the elements are null or empty, their input boxes
    // are highlighted in red
    let isEmpty;
    for (var i = 0; i < formCheck.length; i++) {
        if (formCheck[i] === null || formCheck[i] === "") {
            document.getElementById(formString[i]).style.borderColor = "red";
            
            isEmpty = true;
            
            if (i == 0) {
                // for the first occurrence of an empty form, alert the user.
                alert("Please fill all required fields");
            }
        } else {
            // if element is not empty or null, the input border stays black
            document.getElementById(formString[i]).style.borderColor = "black";
        }
    }
    if (isEmpty) {
        return false;
    } else {
        return true;
    }
}

// main register function, called when the user presses the "register button"
function registerAccount() {

    let request = new XMLHttpRequest();

    request.onload = () => {

        if (request.status === 200) {
            
            if (request.responseText === "success"){
                // if the user successfully registers an account, a feedback message is displayed
            document.getElementById("register").innerHTML = " <p>Thank you for registering an account with Sneak Trainer!" +
            " Now you can log in! If you go to the <a href='../PHP/profile.php'>Profile page</a>, you can see all of your orders,"
             +" as well as view and edit your details.</p>";
            } else {
                document.getElementById("register").innerHTML = "<p>That email already exists. Please try registering again.";
            }
        }
        else
            alert("Error communicating with server: " + request.status);
    };

    // retrieve all registration fields from the registration form
    let firstName = document.getElementById("firstName").value;
    let surname = document.getElementById("surname").value;
    let address = document.getElementById("address").value;
    let email = document.getElementById("email").value;
    let phone = document.getElementById("phone").value;
    let passwrd = document.getElementById("passwrd").value;

    let formCheck = [firstName, surname, address, email, passwrd];

    if (checkFilledIn(formCheck)) { // if all form fields are filled in

        // post form input to registration.php where it is processed and a new customer document is created
        request.open("POST", "ajax_registration.php");
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("firstName=" + firstName + "&surname="
            + surname + "&address=" + address + "&email=" + email + "&phone=" + 
            phone + "&password=" + passwrd + "&postCheck=" + "true");
    } 
}

</script>   

</main>

<?php
general_footer();
?>
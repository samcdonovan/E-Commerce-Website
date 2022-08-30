<?php
include_once ("globalFunctions.php");

general_header("About Us");
navigation_bar("About Us");
?>

<main>
    <!-- info about this project and my aims -->
    <div class="projectAims">
        <h1>What we're all about</h1>
        <p>The aim of this compay is to be your go to for
            new trainers, your source for everything important
            in the sneaker scene, and the company that all your
            friends will be talking about! Everything we do is for
            the community, our customers are at the forefront of 
            our priorities, and that will be what brings you back
            everytime.
            We work with the brands that you like and the ensures that
            we always deliver quality products on time. If you have any queries
            or complaints, please email our response team and we will
            help you in any way we can.      
        </p>
    </div>

    <!-- contact information with phone and email icons -->
    <div class="contact">
        <h1>Contact Details</h1>
        <div class="phone">
            <img src="../images/phone.png" alt="PHONE ICON">
            <p> 009999999</p>
        </div>
        <div class="email">
            <img src="../images/email.png" alt="EMAIL ICON">
            <p>9999@mdx.ac.uk</p>
        </div>
        <div class="address">
            <p>Address:<br>
                Middlesex University, The Burroughs<br>
                Hendon<br>
                London, NW4 4BT<br></p>
        </div>

        <!-- placeholder for a map -->
        <div class="mapPlaceholder">
            <img src="../images/map.png" alt="MAP">
        </div> 
    </div>

</main>

<?php
general_footer();
?>

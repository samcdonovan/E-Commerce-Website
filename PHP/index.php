<?php

// included globalFunctions in every page to use header, navigation bar and footer functions
include_once ("globalFunctions.php");

// header and navigation bar functions calls;
// current page is home so that is the input; 
// this is the same for every page except for the input
general_header("Home");
navigation_bar("Home");
?>

<main>

    <!-- slideshow showing different pictures of trainers -->
    <div class="slideshow">
        <div class="slides">
            <div class="slide" id="slide1">
                <img src="../images/slide1.png" alt="slide 1">
            </div>
            <div class="slide" id="slide2">
                <img src="../images/slide2.png" alt="slide 2">
            </div>
            <div class="slide" id="slide3">
                <img src="../images/slide3.png" alt="slide 3">
            </div>
            <div class="slide" id="slide4">
                <img src="../images/slide4.png" alt="slide 4">
            </div>
            <div class="slide" id="slide5">
                <img src="../images/slide5.png" alt="slide 5">
            </div>
            <div class="slide" id="slide6">
                <img src="../images/slide6.png" alt="slide 6">
            </div>
            <div class="slide" id="slide7">
                <img src="../images/slide7.png" alt="slide 7">
            </div>
        </div>

        <!-- next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>

        <!-- message that appears on the slideshow -->
        <div class="slideshowText">
            <p>Sneak Trainer is a company designed by you, for you!
                Our aim is to bring you the latest and greatest in the trainer industry, 
                at an affordable price! Check back here regularly for deals on your 
                favourite brands.     
            </p>
            <!-- button on the slideshow that leads to the products page -->
            <a class="shopNow" href="shop.php">Shop Now!</a> 
        </div>  

    </div>

    <!-- section underneath the slideshow showing popular trainer brands -->
    <div class="popBrands">
        <p>POPULAR BRANDS</p>

        <!-- logos for nike, adidas, reebok and puma -->
        <img src="../images/nikeLogo.png" alt="NIKE_LOGO">
        <img src="../images/adidasLogo.png" alt="ADIDAS_LOGO">
        <img src="../images/reebokLogo.png" alt="REEBOK_LOGO">
        <img src="../images/pumaLogo.png" alt="PUMA_LOGO">

    </div>
</main>

<script>

    /* JS functions for controlling slideshow on home page */
    var slideIndex = 1;
    showSlides(slideIndex);

    // Next/previous controls
    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    // function that takes an input and increments current slide by that amount
    // e.g. if user presses next, it is incremented by +1
    function showSlides(n) {
        var i;
        var slides = document.getElementsByClassName("slide");
        if (n > slides.length) {
            slideIndex = 1
        }
        if (n < 1) {
            slideIndex = slides.length
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }

        slides[slideIndex - 1].style.display = "block";
    }
</script>

<?php

// footer function takes no input
general_footer();
?>
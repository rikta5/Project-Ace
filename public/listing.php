<?php
session_start();
require '../includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    /** @noinspection PhpUndefinedVariableInspection */
    $item_id = mysqli_real_escape_string($conn, $_GET['id']);

    $listing_sql = "SELECT * FROM listings WHERE L_ID = '$item_id'";
    $result_listing = mysqli_query($conn, $listing_sql);
    $row_listing = mysqli_fetch_assoc($result_listing);


    mysqli_free_result($result_listing);
    $img_sql = "SELECT * FROM pictures WHERE Listing = '$item_id'";
    $result_img = mysqli_query($conn, $img_sql);
    $images = array();

// Fetch the results into the array
    while ($row = mysqli_fetch_assoc($result_img)) {
        $images[] = $row["Picture_Name"];
    }


}
mysqli_free_result($result_img);
$listing_type = $row_listing['Listing_Type'];
$base_path = "../img/";

$attributes_map = [
    'Cars' => ['Manufacturer', 'Model', 'Mileage', 'Transmission', 'Fuel_Type'],
    'Housings' => ['Type', 'Square_Meters', 'Num_of_Bedrooms', 'Floor'],
    'Smartphones' => ['Brand', 'Series', 'Color' , 'Storage', 'RAM'],
    'Shoes' => ['Brand', 'Model' , 'Gender' ,'Size'],
    'Laptops' => ['Brand', 'Model', 'Display_Size', 'Processor' ,'RAM', 'Storage']
];

$attributes_to_display = isset($attributes_map[$listing_type]) ? $attributes_map[$listing_type] : [];

if ($listing_type == "Smartphones"){
    $listing_type = "Phones";
}

if ($listing_type != "Other") {
    $subtype_sql = "SELECT * FROM $listing_type WHERE L_ID = '$item_id'";
    $result_subtype = mysqli_query($conn, $subtype_sql);
    $row_subtype = mysqli_fetch_assoc($result_subtype);
    mysqli_free_result($result_subtype);
}else {
    $subtype_details = [];
}




?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Discover</title>


    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/media.css">
    <link rel="stylesheet" href="../css/slider.css">

    <!-- LATO -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script
        type="module"
        src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.esm.js"
    ></script>
    <script
        nomodule=""
        src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.js"
    ></script>
</head>

<body>

<header class="header">

    <a href="./index.php">
        <img
            src="../img/AceLogo.png"
            alt="Ace logo"
            class="header-logo"
        />
    </a>
    <nav class="main-nav">
        <ul class="main-nav-list">
            <li><a href="./index.php" class="main-nav-link">Discover</a></li>
            <?php if(isset($_SESSION['U_ID'])) { ?>
                <li>
                    <a href="your_profile.php" class="main-nav-link">Your Profile</a>
                </li>
                <li>
                    <a href="../post-new-listing.php" class="main-nav-link">Post New Listing</a>
                </li>
            <?php } else { ?>
                <li>
                    <a href="Login.php" class="main-nav-link">Your Profile</a>
                </li>
                <li>
                    <a href="Login.php" class="main-nav-link">Post New Listing</a>
                </li>
            <?php } ?>
        </ul>
        <?php if(!isset($_SESSION['U_ID'])) { ?>
            <a href="Login.php" class="link-button" id="login-button">Log in</a>
        <?php } else { ?>
            <a href="../includes/Logout.php" class="link-button" id="login-button">Log out</a>
        <?php } ?>
    </nav>
    <button class="btn-mobile-nav">
        <ion-icon class="icon-mobile-nav" name="menu-outline"></ion-icon>
        <ion-icon class="icon-mobile-nav" name="close-outline"></ion-icon>
    </button>

</header>


<section class="listing-section">

    <div class="slider">
        <ul class="slides">
            <?php
            // Generate the slides and radio buttons
            for ($i = 0; $i < count($images); $i++):
                $image_src = htmlspecialchars($base_path . $images[$i]);
                $prev_img = $i == 0 ? count($images) : $i;
                $next_img = ($i + 2) > count($images) ? 1 : ($i + 2);
                ?>
                <input type="radio" name="radio-btn" id="img-<?php echo $i + 1; ?>" <?php echo $i === 0 ? 'checked' : ''; ?> />
                <li class="slide-container">
                    <div class="slide">
                        <img src="<?php echo $image_src; ?>" />
                    </div>
                    <div class="nav">
                        <label for="img-<?php echo $prev_img; ?>" class="prev">&#x2039;</label>
                        <label for="img-<?php echo $next_img; ?>" class="next">&#x203a;</label>
                    </div>
                </li>
            <?php endfor; ?>

            <li class="nav-dots">
                <?php for ($i = 0; $i < count($images); $i++): ?>
                    <label for="img-<?php echo $i + 1; ?>" class="nav-dot" id="img-dot-<?php echo $i + 1; ?>"></label>
                <?php endfor; ?>
            </li>
        </ul>
    </div>

<div class="container">

    <div class="grid grid--2-cols margin-top-md margin-bottom-sm">
<?php
    echo "<div>";
    echo "<h1 class='font-size44'>{$row_listing["Title"]}</h1>";
    echo "<h2>{$row_listing["Price"]}KM</h2>";
    echo "</div>";
    if ($row_listing["isHidden"] == null || $row_listing["isHidden"] == 0){
        echo "<div class='stock'>";
        echo "<h2>In stock</h2>";
        echo "<h3>Quantity: {$row_listing["Quantity"]}</h3>";
        echo "</div>";
    } else{
        echo "<h2 class='stock'>Out of stock</h2>";
    }
?>



    </div>

    <?php

    if(!empty($row_listing["Description"])){
        echo "<div class='description margin-bottom-md'>";

        echo "<p class='description-paragraph'>{$row_listing["Description"]}</p>";

        echo "</div>";

    } ?>


    <?php if (!empty($attributes_to_display)): ?>
        <div class="margin-bottom-md">
        <table class="listing-details-table">
            <tbody>
            <?php foreach ($attributes_to_display as $attribute): ?>
                <?php if (!is_null($row_subtype[$attribute])): ?>
                    <tr>
                        <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $attribute))). ":"; ?></th>
                        <td><?php echo htmlspecialchars($row_subtype[$attribute]); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

    <?php endif; ?>


    <div class="container flex flex-gap-lg margin-top-slg">
        <a href="">Comments</a>
        <button class="form-button purchase-button">Purchase</button>
    </div>


</div>



    <!-- PAYPAL

   <div class="pop-up transaction-pop-up">
       <div class="exit-button-payment margin-bottom-sm">
           <i class="fa-solid fa-x" style="color: #040404;"></i>
       </div>


       <div class="payment-section">
           <p class="heading-secondary margin-bottom-md">Item name</p>
           <p class="margin-bottom-sm">Payment method</p>
           <select class="select-input margin-bottom-xsm smaller-input payment-options" required>
               <option disabled selected>Select Option</option>
               <option>Credit Card</option>
               <option>Paypal</option>
           </select>
       </div>



       <div class="payment-section">
           <form class="form-payment hidden">
           <p class="margin-bottom-sm">Credit card details</p>
           <div class="grid grid--2-cols">
               <input type="text" class="general-text--input margin-bottom-xsm" placeholder="First Name">
               <input type="text" class="general-text--input margin-bottom-xsm" placeholder="Last Name">
               <div class="credit-card-input margin-bottom-xsm">
                   <input type="text" class="general-text--input" placeholder="1234 1234 1234 1234">
                   <span class="credit-card-icons">
                       <i class="fa-brands fa-cc-mastercard" style="color: #000;"></i>
                       <i class="fa-brands fa-cc-visa" style="color: #000;"></i>
                   </span>
               </div>
               <div class="flex flex-gap-sm margin-bottom-xsm">
                   <input type="text" class="general-text--input" placeholder="MM/YY">
                   <input type="text" class="general-text--input" placeholder="CVC">
               </div>


           </div>
               <div class="flex flex-column">
                   <p class="heading-tertiary--black container-button-left margin-bottom-sm">Price</p>
                   <div class="container-button-left margin-bottom-md">
                       <button class="form-button">Purchase</button>
                   </div>
               </div>
           </form>

           <form class="form-payment hidden">
           <div>
               <p class="margin-bottom-xsm"></p>
               <input type="text" class="general-text--input margin-bottom-xsm" placeholder="Email">
               <input type="text" class="general-text--input margin-bottom-xsm" placeholder="Password">
           </div>

           <p class="margin-bottom-sm">Billing Address</p>
           <input type="text" class="general-text--input margin-bottom-xsm" placeholder="Street Name">
           <div class="flex flex-gap-sm">
               <input type="text" class="general-text--input margin-bottom-xsm" placeholder="City">
               <input type="text" class="general-text--input margin-bottom-xsm" placeholder="Postal/Zip Code">
           </div>
           <div class="margin-bottom-xsm">
               <input type="checkbox" name="save-info" id="save-info">
               <label for="save-info">Save payment information for later</label>
           </div>


               <div class="flex flex-column">
                   <p class="heading-tertiary--black container-button-left margin-bottom-sm">Price</p>
                   <div class="container-button-left margin-bottom-md">
                       <button class="form-button">Purchase</button>
                   </div>
               </div>
           </form>
       </div>





    </div>
    -->
</section>

<footer class="footer">
    <div class="grid grid--3-cols footer-grid">
        <div>
            <p class="margin-bottom-sm footer-headings">ACE</p>
            <p><a href="./index.php">Discover</a></p>
            <?php if(isset($_SESSION['U_ID'])) { ?>
                <p><a href="your_profile.php">Your Profile</a></p>
                <p><a href="../post-new-listing.php">Post new listing</a></p>
            <?php } else { ?>
                <p><a href="Login.php">Your Profile</a></p>
                <p><a href="Login.php">Post new listing</a></p>
            <?php } ?>
        </div>
        <div>
            <p class="margin-bottom-sm footer-headings">CONTACT US</p>
            <p>tarik.basic@stu.ssst.edu.ba</p>
            <p>hana.catic@stu.ssst.edu.ba</p>
            <p>nikola.obradovic@stu.ssst.edu.ba</p>
        </div>
        <div>
            <p class="margin-bottom-sm footer-headings">WHERE TO FIND US?</p>
            <p>Hrasnička cesta 3a, Ilidža 71210</p>
            <p>Sarajevo, Bosnia and Herzegovina</p>
        </div>
    </div>
    <p id="copyright">Copyright &copy; 2024 Inwolve</p>
</footer>


<script src="../js/main.js"></script>
<div class="dimmed-background hidden"></div>

</body>
</html>
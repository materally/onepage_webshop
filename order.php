<?php
ob_start();
include("admin/include/dbcon.php");
include("admin/phpmailer.php");
include("functions.php");

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$required = array("lastname", "firstname", "email", "phone", "zip", "city", "address");

$ok = true;
$email_accepted = 0;

foreach ($required as $post) {
    if(empty($_POST[$post])){
        $ok = false;
    }
}

if($_POST['ua'] == 0){
    $required = array("invoice_lastname", "invoice_firstname", "invoice_zip", "invoice_city", "invoice_address");
    foreach ($required as $post) {
        if(empty($_POST[$post])){
            $ok = false;
        }
    }
}

if(isset($_POST['emailt_kap'])){
    $email_accepted = 1;
}

if($ok){
    $shipping_price = cartTotalAjax('shipping_price', 0, 0);
    // adatázis műveletek
    $email = $_POST['email'];
    $email = str_replace(" ", "", $email);
    $sqlOrders = "INSERT INTO orders (
        order_date,
        lastname,
        firstname,
        email,
        phone,
        country,
        zip,
        city,
        address,
        invoice_country,
        invoice_lastname,
        invoice_firstname,
        invoice_zip,
        invoice_city,
        invoice_address,
        transaction_number,
        transaction_name,
        payment_method,
        email_accepted,
        payment_price,
        shipping_price,
        country_price
    ) VALUES (
        NOW(),
        '".$_POST['lastname']."',
        '".$_POST['firstname']."',
        '".$email."',
        '".$_POST['phone']."',
        '".$_POST['selectedCountry']."',
        '".$_POST['zip']."',
        '".$_POST['city']."',
        '".$_POST['address']."',
        '".$_POST['invoice_country']."',
        '".$_POST['invoice_lastname']."',
        '".$_POST['invoice_firstname']."',
        '".$_POST['invoice_zip']."',
        '".$_POST['invoice_city']."',
        '".$_POST['invoice_address']."',
        '".$_POST['transaction_number']."',
        '".$_POST['transaction_name']."',
        '".$_POST['payment_method']."',
        '".$email_accepted."',
        '".$_POST['payment_price']."',
        '".$shipping_price."',
        '".$_POST['selectedCountryPrice']."'
    )";

    mysqli_query($con, $sqlOrders);
    $last_id = mysqli_insert_id($con);

    foreach ($_SESSION['cart'] as $key => $value) {
        $sqlCart = "INSERT INTO cart (order_id, product_id, product_name, ordered_price, qty) VALUES ('".$last_id."', '".$value['product_id']."', '".$value['product_name']."', '".$value['product_price']."', '".$value['qty']."')";
        mysqli_query($con, $sqlCart);
        mysqli_query($con, "UPDATE products SET stock = stock - ".$value['qty']." WHERE product_id = ".$value['product_id']);
    }
    
    $RendelesiAdatok = "<b>Név: </b>";
    $RendelesiAdatok .= $_POST['lastname'] . " " . $_POST['firstname'] . "<br>";
    $RendelesiAdatok .= "<b>E-mail cím: </b>";
    $RendelesiAdatok .= $email . "<br>";
    $RendelesiAdatok .= "<b>Telefonszám: </b>";
    $RendelesiAdatok .= $_POST['phone'] . "<br>";
    $RendelesiAdatok .= "<b>Cím: </b>";
    $RendelesiAdatok .= $_POST['selectedCountry'] . ", " . $_POST['zip'] . " " . $_POST['city'] . " " . $_POST['address'] . "<br>";
    
    if($_POST['ua'] == 0){
        $RendelesiAdatok .= "<hr><b>Számlázási név: </b>";
        $RendelesiAdatok .= $_POST['invoice_lastname'] . " " . $_POST['invoice_firstname'] . "<br>";
        $RendelesiAdatok .= "<b>Számlázási cím: </b>";
        $RendelesiAdatok .= $_POST['invoice_country'] . ", " . $_POST['invoice_zip'] . " " . $_POST['invoice_city'] . " " . $_POST['invoice_address'] . "<br>";
    }

    $Termekek = "<b>Megrendelt termékek:</b><table width='100%' border='0' cellspacing='0' cellpadding='0'>";
    $Termekek .= "<tr>";
    $Termekek .= "<td style='border:1px solid #999;'>&nbsp;</td>";
    $Termekek .= "<td style='border:1px solid #999;'>Termék</td>";
    $Termekek .= "<td style='border:1px solid #999;'>Egységár</td>";
    $Termekek .= "<td style='border:1px solid #999;'>Mennyiség</td>";
    $Termekek .= "</tr>";

    foreach ($_SESSION['cart'] as $key => $value) {
        $product = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM products WHERE product_id = ".$value['product_id']));
        $img = $product['product_image'];
        $Termekek .= "<tr>";
        $Termekek .= "<td style='border:1px solid #999;'><img src='".$img."' style='max-width:50px;'/></td>";
        $Termekek .= "<td style='border:1px solid #999;'>".$product['product_name']."</td>";
        $Termekek .= "<td style='border:1px solid #999;'>".$product['product_price']." Ft</td>";
        $Termekek .= "<td style='border:1px solid #999;'>".$value['qty']." db</td>";
        $Termekek .= "</tr>";
    }

    $szall_dij = $shipping_price;
    $fiz_dij = $_POST['payment_price'];
    $kulfoldi_dij = $_POST['selectedCountryPrice'];

    if($szall_dij > 0){
        $Termekek .= "<tr>";
        $Termekek .= "<td style='border:1px solid #999;'>&nbsp;</td>";
        $Termekek .= "<td style='border:1px solid #999;'>Szállítási díj</td>";
        $Termekek .= "<td style='border:1px solid #999;'>".$szall_dij." Ft</td>";
        $Termekek .= "<td style='border:1px solid #999;'>1 db</td>";
        $Termekek .= "</tr>";
    }

    if($fiz_dij > 0){
        $Termekek .= "<tr>";
        $Termekek .= "<td style='border:1px solid #999;'>&nbsp;</td>";
        $Termekek .= "<td style='border:1px solid #999;'>Kezelési költség</td>";
        $Termekek .= "<td style='border:1px solid #999;'>".$fiz_dij." Ft</td>";
        $Termekek .= "<td style='border:1px solid #999;'>1 db</td>";
        $Termekek .= "</tr>";
    }
    if($kulfoldi_dij > 0){
        $Termekek .= "<tr>";
        $Termekek .= "<td style='border:1px solid #999;'>&nbsp;</td>";
        $Termekek .= "<td style='border:1px solid #999;'>Külföldi szállítás</td>";
        $Termekek .= "<td style='border:1px solid #999;'>".$kulfoldi_dij." Ft</td>";
        $Termekek .= "<td style='border:1px solid #999;'>1 db</td>";
        $Termekek .= "</tr>";
    }

    $total = cartTotalAjax('total', $fiz_dij, $kulfoldi_dij);
    
    $Termekek .= "<tr>";
    $Termekek .= "<td colspan='4' style='border:1px solid #999;'>Összesen fizetendő: ".$total." Ft</td>";
    $Termekek .= "</tr>";

    $Termekek .= "</table>";

    $Mail = file_get_contents("levelek/megrendeles-visszaigazolasa.html", true);
    $Mail = str_replace("{rendelesiadatok}", $RendelesiAdatok, $Mail);
    $Mail = str_replace("{termekek}", $Termekek, $Mail);

    sendMailSMTP($email, "Sikeres megrendelés", $Mail);

    unset($_SESSION['cart']);

    header("Location: thankyou.php");

}else{
    header("Location: thankyou.php?problem");
}


<?php

/*

cart: 
    product_id
    product_name
    product_price
    qty
*/

function addToCart($product_id)
{
	global $con;
    $data = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM products WHERE product_id = ".$product_id));

    $i = count($_SESSION['cart']);

    if($i == 0){
        $_SESSION['cart'][$i]['product_id']     = $product_id;
        $_SESSION['cart'][$i]['product_name']   = $data['product_name'];
        $_SESSION['cart'][$i]['product_price']  = $data['product_price'];
        $_SESSION['cart'][$i]['qty']            = 1;
    }else{
        // létező termék a kosárban
        foreach ($_SESSION['cart'] as $key => $value) {
            if($product_id == $value['product_id']){
                // termék qty módosítás
                ++$_SESSION['cart'][$key]['qty'];
                die();
            }
        }
        // új termék
        $_SESSION['cart'][$i]['product_id']     = $product_id;
        $_SESSION['cart'][$i]['product_name']   = $data['product_name'];
        $_SESSION['cart'][$i]['product_price']  = $data['product_price'];
        $_SESSION['cart'][$i]['qty']            = 1;
    }

}

function deleteFromCart($product_id){
    foreach ($_SESSION['cart'] as $key => $value) {
        if($product_id == $value['product_id']){
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            die();
        }
    }
}

function refreshCart($keyp, $qty){
    foreach ($_SESSION['cart'] as $key => $value) {
        if($keyp == $key){
            $_SESSION['cart'][$key]['qty'] = $qty;
            //$_SESSION['cart'] = array_values($_SESSION['cart']);
            die();
        }
    }
}

function cartTotalAjax($return, $payment_price, $country_price){
    global $con;
    $total = 0;
    $shipping_price = 0;
    $total += $payment_price;
    $total += $country_price;
    foreach ($_SESSION['cart'] as $key => $value) {
        $qty = $value['qty'];
        $price = $value['product_price'];
        $sum = $qty*$price;
        $total += $sum;
    }
    $shipping_hatar = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM settings WHERE slug = 'ingyenes_szallitasi_hatar'"))['setting_value'];
    $shipping_osszeg = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM settings WHERE slug = 'szallitasi_dij'"))['setting_value'];

    if(($total - $country_price - $payment_price) < $shipping_hatar){
        $shipping_price = $shipping_osszeg;
        $total += $shipping_osszeg;
    }else{
        $shipping_price = 0;
    }

    if($return == 'total'){
        return $total;
    }

    if($return == 'shipping_price'){
        return $shipping_price;
    }
}

<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include("admin/include/dbcon.php");
include("admin/include/helpers.php");
include("functions.php");

if(isset($_GET['f']) AND $_GET['f'] == 'addtocart'){
    if(!empty($_GET['product_id'])){
        addToCart($_GET['product_id']);
    }
}

if(isset($_GET['f']) AND $_GET['f'] == 'showcart'){
    if(count($_SESSION['cart']) == 0){
        echo 'empty';
    }else{
        foreach($_SESSION['cart'] as $key => $value){
            $img = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM products WHERE product_id = ".$value['product_id']))['product_image'];
            echo '
                <input type="hidden" name="product[name]" value="'.$value['product_name'].'"><input type="hidden" name="product[qty]" value="'.$value['qty'].'">
                <div class="row vcenter fader" style="border:1px solid #fff;">
                    <div class="col-xs-2 col-md-2 col-lg-2 mb-sm-30 ">
                        <img src="'.$img.'" class="img-responsive" style="width:100%;"/>
                    </div>
                    <div class="col-xs-5 col-md-5 col-lg-5 mb-sm-30 text-left " style="color:#000;">
                        <b>'.$value['product_name'].'</b><br>
                        '.nicePrice($value['product_price']).' / db
                    </div>
                    <div class="col-xs-3 col-md-3 col-lg-3 mb-sm-30 text-center ">
                        <input type="number" id="productQty_'.$key.'" value="'.$value['qty'].'" name="product" class="cartQty" min="0" max="9" style="color:#000" maxlength="1"/>&nbsp;&nbsp;&nbsp;
                        <a href="#!" onclick="return refreshCart('.$key.')"><i class="fa fa-refresh" aria-hidden="true" style="font-size:19px; color:#84b813;"></i></a>
                    </div>
                    <div class="col-xs-1 col-md-1 col-lg-1 mb-sm-30 text-center ">
                        <a href="#!" onclick="return deleteFromCart('.$value['product_id'].')"><i class="fa fa-trash" aria-hidden="true" style="font-size:19px; color:red;"></i></a>
                    </div>
                </div>
            ';
        }
    }
}

if(isset($_GET['f']) AND $_GET['f'] == 'deletefromcart'){
    if(!empty($_GET['product_id'])){
        deleteFromCart($_GET['product_id']);
    }
}

if(isset($_GET['f']) AND $_GET['f'] == 'refreshcart'){
    if(!empty($_GET['qty'])){
        refreshCart($_GET['key'], $_GET['qty']);
    }
}

if(isset($_GET['f']) AND $_GET['f'] == 'carttotal'){
    $total = cartTotalAjax('total', $_GET['payment_price'], $_GET['country_price']);
    echo '
    <div class="col-sm-12 col-md-12 col-lg-12 text-center fader" style="font-size:30px; padding: 20px 0 20px 0;">
        <span class="matetitle">Végösszeg: </span><span class="matetitle tcolor"><span id="orderFormTotalShow">'.nicePrice($total).'</span></span>
    </div>
    ';
}

if(isset($_GET['f']) AND $_GET['f'] == 'shipping_price'){
    $shipping_price = cartTotalAjax('shipping_price', 0, 0);
    if(isset($_GET['rest'])){
        $total = cartTotalAjax('total', 0, 0);
        $total -= $shipping_price;
        $settings = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM settings WHERE slug = 'ingyenes_szallitasi_hatar'"))['setting_value'];
        $rest = $total - $settings;
        if($rest >= 0){
            echo "&nbsp;";
        }else{
            $rest = substr($rest, 1);
            echo '(még <b>'.nicePrice($rest).'</b> értékben vásárolj és INGYEN visszük!)';
        }
    }else{
        echo nicePrice($shipping_price);
    }
}

if(isset($_GET['f']) AND $_GET['f'] == 'totalpricetobackend'){
    $total = cartTotalAjax('total', $_GET['payment_price'], $_GET['country_price']);
    echo $total;
}

if(isset($_GET['f']) AND $_GET['f'] == 'card_limit'){
    $total = cartTotalAjax('total', $_GET['payment_price'], $_GET['country_price']);
    $limitDB = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM settings WHERE slug = 'bankkartya_limit'"))['setting_value'];
    if($total > $limitDB){
        // csak bankkártyás fizetés
        echo "CSAK";
    }else{
        echo "NEMCSAK";
    }
    
}

?>
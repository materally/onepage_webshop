<?php
// <body onload="showCart();">
?>


<section class="newletter-1">
    <div class="container">
        <div class="row" id="megrendelo">
            <div class="col-sm-12 col-md-12 col-lg-12 mb-sm-30 bg-white" style="">

                <div class="row">
                    <nav class="navbar navbar-default nav-tabs">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <a class="navbar-brand" href="#!">Kategóriák</a>
                            </div>
                            <div id="bs-example-navbar-collapse-2">
                                <ul class="nav navbar-nav">
                                    <?php
                                    echo '<li class="active"><a data-toggle="tab" href="#pillAll">Összes</a></li>';
                                    $category = mysqli_query($con, "SELECT * FROM category");

                                    while ($c = mysqli_fetch_assoc($category)) {
                                        echo '<li><a data-toggle="tab" href="#pill' . $c['category_id'] . '">' . $c['category_name'] . '<span class="sr-only">(current)</span></a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>




                <div class="row">
                    <div class="tab-content">
                        <div id="pillAll" class="tab-pane fade in active">
                            <?php
                            $products = mysqli_query($con, "SELECT * FROM products");
                            echo '<div class="col-12" style="text-align: center; font-size: 18px; padding: 10px; font-weight: 600; color:#000; text-transform:uppercase;"><span style="border-bottom:3px solid #84B813; padding-left:10px; padding-right:10px;">Összes</span></div>';
                            while ($p = mysqli_fetch_assoc($products)) {
                                //echo '<input type="hidden" id="productPrice_' . $p['product_id'] . '" value="' . $p['product_price'] . '">';
                                //col-xs-12 col-sm-6 col-md-2 col-lg-2

                                echo '
                                <div class="_product_item">
                                    <a href="' . $p['product_image'] . '" data-fancybox="gallery"><img src="' . $p['product_image'] . '" class="img-responsive" style="width:100%;"/></a>
                                    <h3 class="_product_item_h3">' . $p['product_name'] . '</h3>
                                    <p><b>' . nicePrice($p['product_price']) . '</b></p>
                                    <div class="quantity">
                                        <button type="button" onclick="return addToCart(' . $p['product_id'] . ')" class="btn button btn-setting bd-color-setting bd-radius-50 addToCartBtn">Kosárba tesz</button>
                                    </div>
                                </div>';
                            }
                            ?>
                        </div> <!-- #/pillAll -->
                        <?php
                        $category = mysqli_query($con, "SELECT * FROM category");
                        while ($c = mysqli_fetch_assoc($category)) {
                            echo '<div class="tab-pane fade" id="pill' . $c['category_id'] . '">';
                            echo '<div class="col-12" style="text-align: center; font-size: 18px; padding: 10px; font-weight: 600; color:#000; text-transform:uppercase;"><span style="border-bottom:3px solid #84B813; padding-left:10px; padding-right:10px;">' . $c['category_name'] . '</span></div>';
                            $products = mysqli_query($con, "SELECT * FROM products WHERE category_id = " . $c['category_id'] . "");

                            while ($p = mysqli_fetch_assoc($products)) {
                                echo '
                                <div class="_product_item">
                                    <a href="' . $p['product_image'] . '" data-fancybox="gallery"><img src="' . $p['product_image'] . '" class="img-responsive" style="width:100%;"/></a>
                                    <h3 class="_product_item_h3">' . $p['product_name'] . '</h3>
                                    <p><b>' . nicePrice($p['product_price']) . '</b></p>
                                    <div class="quantity">
                                        <button type="button" class="btn button btn-setting bd-color-setting bd-radius-50 addToCartBtn" onclick="return addToCart(' . $p['product_id'] . ')">Kosárba tesz</button>
                                    </div>
                                </div>';
                            }
                            echo '</div>';
                        }

                        ?>
                    </div>
                </div>
            </div> <!-- ./col-sm-12 col-md-12 col-lg-12 mb-sm-30 bg-white -->
        </div> <!-- #./megrendelo -->




        <div class="row" id="checkoutPage" style="margin-top:30px;">
            <div class="col-sm-12 col-md-6 col-lg-6 mb-sm-30">
                <!-- Contact form element -->
                <div class="col-sm-12" style="padding-bottom:10px; color:#84B813; padding-left:10px!important;"><b>KOSARAD TARTALMA</b></div>
                <div id="showCart"></div>
                <!--/ Contact form element -->
            </div>
            <!--/ col-sm-12 col-md-6 col-lg-6 mb-sm-30 -->


            <div class="col-sm-12 col-md-6 col-lg-6 mb-sm-30">
                <!-- Contact form element -->
                <div class="form-group">
                    <div class="row">
                        <!-- Response wrapper -->
                        <form method="post" action="order.php" id="orderForm">

                            <div class="col-sm-12 rendelesi_adatok_mobile" style="padding-left:15px; color:#84B813"><b>RENDELÉSI ADATOK</b></div>

                            <div class="col-sm-6">
                                <label class="control-label">VEZETÉKNÉV*</label>
                                <input type="text" name="lastname" id="lastname" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">KERESZTNÉV*</label>
                                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">E-MAIL CÍM*</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">TELEFONSZÁM*</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>


                            <div class="col-sm-4">
                                <label class="control-label">IRÁNYÍTÓSZÁM*</label>
                                <input type="text" name="zip" id="zip" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>
                            <div class="col-sm-8">
                                <label class="control-label">VÁROS*</label>
                                <input type="text" name="city" id="city" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">CÍM, HÁZSZÁM, EMELET, AJTÓ*</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                            </div>

                            <div class="col-sm-6">
                                <label class="control-label">ORSZÁG*</label>
                                <select name="country" id="country" class="form-control" onchange="return changeCountry()">
                                    <?php
                                    $countries = mysqli_query($con, "SELECT * FROM countries ORDER by country_id ASC");
                                    while ($country = mysqli_fetch_assoc($countries)) {
                                        echo '<option value="' . $country['country'] . '|' . $country['price'] . '">' . $country['country'] . ' (+' . $country['price'] . ' Ft)</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <input type="hidden" id="ua" name="ua" value="1">
                            <input type="hidden" id="payment_method" name="payment_method" value="bankkartya">
                            <input type="hidden" id="payment_price" name="payment_price" value="0">
                            <input type="hidden" id="selectedCountry" name="selectedCountry" value="Magyarország">
                            <input type="hidden" id="selectedCountryPrice" name="selectedCountryPrice" value="0">
                            <input type="hidden" id="transaction_number" name="transaction_number" value="">
                            <input type="hidden" id="transaction_name" name="transaction_name" value="">
                            <input type="hidden" id="totalPriceToBackend" name="totalPriceToBackend" value="0">

                            <div class="col-sm-12" style="text-align:center">
                                <div class="" style="margin:0 auto; text-align:left; display:inline-block; padding-top:20px;">
                                    <label style="font-weight:500" for="same_as_above"><input type="checkbox" id="same_as_above" name="same_as_above" checked /><span style="font-size:12px; padding-left:10px;">A számlázási adatok megegyeznek a szállítási adatokkal. </span></label>
                                </div>
                            </div>

                            <div id="szamla" style="border:1px solid white;">
                                <div class="col-sm-12" style="padding-left:15px; color:#84B813"><b>SZÁMLÁZÁSI ADATOK</b></div>
                                <div class="col-sm-6">
                                    <label class="control-label">VEZETÉKNÉV*</label>
                                    <input type="text" name="invoice_lastname" id="invoice_lastname" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label">KERESZTNÉV*</label>
                                    <input type="text" name="invoice_firstname" id="invoice_firstname" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label">IRÁNYÍTÓSZÁM*</label>
                                    <input type="text" name="invoice_zip" id="invoice_zip" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                                </div>
                                <div class="col-sm-8">
                                    <label class="control-label">VÁROS*</label>
                                    <input type="text" name="invoice_city" id="invoice_city" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label">CÍM, HÁZSZÁM, EMELET, AJTÓ*</label>
                                    <input type="text" name="invoice_address" id="invoice_address" class="form-control" placeholder="" value="" tabindex="0" maxlength="35" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label">ORSZÁG*</label>
                                    <select name="invoice_country" id="invoice_country" class="form-control">
                                        <?php
                                        $countries = mysqli_query($con, "SELECT * FROM countries ORDER by country_id ASC");
                                        while ($country = mysqli_fetch_assoc($countries)) {
                                            echo '<option value="' . $country['country'] . '">' . $country['country'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12" style="padding-top:20px; padding-bottom:10px;">
                                <div class="col-sm-12" style="padding-left:0px; color:#84B813"><b>SZÁLLÍTÁSI DÍJ: </b>
                                    <span style="padding-left:10px; color:#000; font-weight:bold; font-size:16px;" id="szallitasiDijShow"></span>
                                    <span style="padding-left:10px; color:#000; font-size:11px;" id="szallitasiDijRestShow"></span>
                                </div>
                            </div>

                            <div class="col-sm-12" style="padding-top:20px; padding-bottom:10px;">
                                <div class="col-sm-12" style="padding-left:0px; color:#84B813"><b>FIZETÉSI MÓD</b></div>
                                <div class="btn-group" role="group" aria-label="...">
                                    <?php
                                    $payments = mysqli_query($con, "SELECT * FROM payments ORDER by payment_id DESC");
                                    while ($payment = mysqli_fetch_assoc($payments)) {
                                        $class = ($payment['payment_id'] == 2) ? "activePaymentBtn" : "";
                                        $price = ($payment['price'] == 0) ? "INGYENES" : "+" . $payment['price'] . " Ft";
                                        echo '<button type="button" id="' . $payment['slug'] . 'Btn" class="btn PaymentBtn ' . $class . '" onclick="return changePayment(\'' . $payment['slug'] . '\', ' . $payment['price'] . ')">' . $payment['name'] . ' (' . $price . ')</button>';
                                    }
                                    ?>
                                    <!-- <button type="button" id="bankkartyaBtn" class="btn PaymentBtn activePaymentBtn" onclick="return changePayment('bankkartya')">BANKKÁRTYÁVAL (INGYENES)</button>
                                <button type="button" id="utanvetBtn" class="btn PaymentBtn" onclick="return changePayment('utanvet')">UTÁNVÉTEL (+600 Ft)</button> -->
                                </div>
                            </div>


                            <div class="col-sm-12" style="text-align:center">
                                <div class="" style="margin:0 auto; text-align:left; display:inline-block; padding-top:20px;">

                                    <label style="font-weight:500" for="check1"><input type="checkbox" id="check1" name="check1" value="check1" /><span style="font-size:12px; padding-left:10px;">Elolvastam, megértettem és elfogadom az <a href="adatvedelem.pdf" target="_blank" style="text-decoration:underline">adatkezelési szabályzatot</a></span></label><br>

                                    <label style="font-weight:500" for="check2"><input type="checkbox" id="check2" name="check2" value="check2" /><span style="font-size:12px; padding-left:10px;">Elfogadom az <a href="elallasinyilatkozat.pdf" target="_blank" style="text-decoration:underline">Elállási szabályzatot</a></span></label><br>

                                    <label style="font-weight:500" for="emailt_kap"><input type="checkbox" id="emailt_kap" name="emailt_kap" value="1" /><span style="font-size:12px; padding-left:10px;">Szeretnék e-maileket kapni ajánlatokról és hasznos tudnivalókról</span></label><br>
                                </div>

                                <!-- Contact form send button -->

                                <div id="orderFormTotalShow"></div>

                                <button type="button" id="orderButton" class="orderBtn" tabindex="0" onclick="return checkForm()">
                                    MEGRENDELEM ÉS BANKKÁRTYÁVAL FIZETEK
                                </button>


                                <?php
                                $limitDB = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM settings WHERE slug = 'bankkartya_limit'"))['setting_value'];
                                ?>
                                <p id="cardLimitInfo" class="cardLimitInfo"><?= nicePrice($limitDB) ?> felett csak bankkártyás fizetési lehetőség van!</p>

                            </div>

                            <div class="cf_response"></div>
                        </form>
                    </div>
                </div>
                <!--/ Contact form element -->
            </div>
            <!--/ col-sm-12 col-md-6 col-lg-6 mb-sm-30 -->


        </div><!-- <div class="row" id="orderForm" style="margin-top:30px;"> -->



    </div>

</section>
<!-- End Subscribe -->

<?php
include("_footer.php");
?>
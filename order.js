console.log("init..")

// TEST
var paylike = Paylike('xxxxxx');

$("#checkoutPage").hide();

function showCart(){
    cartTotal();
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=showcart",
        success: function(data){
            if(data == "empty"){
                $("#checkoutPage").hide(500);
            }else{
                $("#checkoutPage").show(500);
                document.getElementById("showCart").innerHTML = data;
            }
        }
    });  
}

function addToCart(product_id){
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=addtocart&product_id="+product_id,
        success: function(data){
            swal({
                icon: 'success',
                className: 'addToCartPopup',
                title: 'A termék sikeresen kosárba helyezve!',
                buttons: false,
                timer: 2000
            })
            .then((val) => {
                showCart();
            })
        }
    });
}

function deleteFromCart(product_id){

    swal({
        title: "Biztosan törlöd a terméket a kosaradból?",
        icon: "warning",
        buttons: ['Mégsem', true],
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          swal("Termék törölve!", {
            icon: "success",
            buttons: false,
            timer: 2000
          })
          .then(() => {
            $.ajax({
                type: "GET",
                url: "ajax.php",
                data: "f=deletefromcart&product_id="+product_id,
                success: function(data){
                    showCart();
                }
            });
          })
        }
      });

}

function refreshCart(key){
    var qty = document.getElementById("productQty_"+key).value;

    if(qty == 0 || qty < 0){
        swal({
            icon: 'warning',
            className: 'addToCartPopup',
            title: 'Érvényes értéket adj meg!',
            buttons: false,
            timer: 2000
        })
    }else{
        if(qty > 9){
            document.getElementById("productQty_"+key).value = 1;
            showCart();
            return false;
        }
        $.ajax({
            type: "GET",
            url: "ajax.php",
            data: "f=refreshcart&key="+key+"&qty="+qty,
            success: function(data){
                showCart();
            }
        });
    } 
}

function cartTotal(){
    var payment_price = document.getElementById("payment_price").value;
    var country_price = document.getElementById("selectedCountryPrice").value;
    calculateShipping();
    totalPriceToBackend(payment_price, country_price);
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=carttotal&payment_price="+payment_price+"&country_price="+country_price,
        success: function(data){
            document.getElementById("orderFormTotalShow").innerHTML = data;
        }
    });
}

function cardLimit(total){
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=card_limit&total="+total,
        success: function(data){
            if(data == "CSAK"){
                // bankkártyás csak
                cartTotalCardLimit();
                document.getElementById("cardLimitInfo").style.display = "block";
                document.getElementById("utanvetBtn").disabled = true;
                $("#utanvetBtn").removeClass("activePaymentBtn");
                $("#bankkartyaBtn").addClass("activePaymentBtn");
                document.getElementById("payment_method").value = 'bankkartya';
                document.getElementById("payment_price").value = 0;
                document.getElementById("orderButton").innerText = "MEGRENDELEM ÉS BANKKÁRTYÁVAL FIZETEK"
            }else{
                document.getElementById("cardLimitInfo").style.display = "none";
                document.getElementById("utanvetBtn").disabled = false;
            }
        }
    });
}

function cartTotalCardLimit(){
    var country_price = document.getElementById("selectedCountryPrice").value;
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=carttotal&payment_price=0&country_price="+country_price,
        success: function(data){
            document.getElementById("orderFormTotalShow").innerHTML = data;
        }
    });
}

function calculateShipping(){
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=shipping_price",
        success: function(data){
            document.getElementById("szallitasiDijShow").innerHTML = data;
            calculateShippingRest();
        }
    });
}

function calculateShippingRest(){
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=shipping_price&rest",
        success: function(data){
            document.getElementById("szallitasiDijRestShow").innerHTML = data;
        }
    });
}

function totalPriceToBackend(payment_price, country_price){
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "f=totalpricetobackend&payment_price="+payment_price+"&country_price="+country_price,
        success: function(data){
            document.getElementById("totalPriceToBackend").value = data;
            cardLimit(data);
        }
    });
}

function changePayment(payment, payment_price){
    //var payment_price;
    if(payment == 'bankkartya'){
        $("#utanvetBtn").removeClass("activePaymentBtn");
        $("#bankkartyaBtn").addClass("activePaymentBtn");
        document.getElementById("payment_method").value = 'bankkartya';
        document.getElementById("payment_price").value = payment_price;
        document.getElementById("orderButton").innerText = "MEGRENDELEM ÉS BANKKÁRTYÁVAL FIZETEK"
        //payment_price = 0;
    }
    if(payment == 'utanvet'){
        $("#utanvetBtn").addClass("activePaymentBtn");
        $("#bankkartyaBtn").removeClass("activePaymentBtn");
        document.getElementById("payment_method").value = 'utanvet';
        document.getElementById("payment_price").value = payment_price;
        document.getElementById("orderButton").innerText = "MEGRENDELEM ÉS A FUTÁRNAK FIZETEK"
        //payment_price = 600;
    }
 
    cartTotal();
}

function changeCountry(){
    var val = $("#country").val();
    var price = val.substr(val.indexOf("|") + 1)
    var country = val.substr(0, val.indexOf("|"))
    $("#selectedCountry").val(country)
    $("#selectedCountryPrice").val(price)
    cartTotal();
}

function checkForm(){

    var ok = true;

    $('#lastname, #firstname, #email, #phone, #zip, #city, #address').each(function() {
        if ($(this).val() == '') {
            $(this).parent().effect('shake', {times: 3}, 100).find('.form-control').addClass('error');
            ok = false;
        }else{
            $(this).parent().find('.form-control').removeClass('error');
        }
    });

    if($("#ua").val() == 0){
        $('#invoice_lastname, #invoice_firstname, #invoice_zip, #invoice_city, #invoice_address').each(function() {
            if ($(this).val() == '') {
                $(this).parent().effect('shake', {times: 3}, 100).find('.form-control').addClass('error');
                ok = false;
            }else{
                $(this).parent().find('.form-control').removeClass('error');
            }
        });
    }

    $('#check1, #check2').each(function() {
        if (!$(this).is(':checked')) {
            $(this).parent().effect('shake', {times: 3}, 100);
            ok = false;
        }
    });

    if(!ok){
        return false;
    }

    if(!validateEmail($("#email").val())){
        ok = false;
        swal({
            icon: 'warning',
            className: 'addToCartPopup',
            title: 'Érvényes e-mail címet adj meg!',
            buttons: false,
            timer: 2000
        })
    }

    if(!validatePhone($("#phone").val())){
        ok = false;
        swal({
            icon: 'warning',
            className: 'addToCartPopup',
            title: 'Érvényes telefonszámot adj meg!',
            buttons: false,
            timer: 2000
        })
    }

    if(ok){
        // megrendelés
        if($("#payment_method").val() == "bankkartya"){
            // kártyás a fülke
            pay();
        }else{
            orderForm();
        }
    }

}

function orderForm(transaction_number, transaction_name){
    $("#transaction_name").val(transaction_name);
    $("#transaction_number").val(transaction_number);
    $("#orderForm").submit();
}


function pay(){
    var fullPrice = document.getElementById('totalPriceToBackend').value;

    var Termekek = [];
    var product_name = document.getElementsByName('product[name]');
    var product_qty = document.getElementsByName('product[qty]');
    for (i=0; i<product_name.length; i++){
        var Termek = {
            termek: product_name[i].value,
            mennyiseg: product_qty[i].value,
        }
        Termekek.push(Termek)
    }

    var Custom = {
        Név: document.getElementById("lastname").value + " " + document.getElementById("firstname").value,
        Email: document.getElementById("email").value,
        Telefonszam: document.getElementById("phone").value,
        Termékek: Termekek
    };

    paylike.popup({
        currency: 'HUF',
        amount: fullPrice*100,
        custom: Custom
    }, function( err, r ){
        if (err)
            return console.warn(err);
        orderForm(r.transaction.id, r.custom.Neved);
    });
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validatePhone(phone) {
    var phonePattern = /\d(\d|[\s\/\(\)-]+\d){5,}/;
    return phonePattern.test(phone);
}
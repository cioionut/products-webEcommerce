function addToCart(user_id,product_id){

    var request = $.ajax({
        url: "/scripts/add_to_cart.php",
        data: { user_id: user_id, product_id: product_id },
        type: "POST",
        dataType: "text"
    });

    request.done(function(data){
        data = JSON.parse(data);
        console.log(data);
        document.getElementById("myCart").innerHTML="New " + data.product_name + " added";
        document.getElementById("countCart").innerHTML=data.nr_products;
    });

    request.fail(function( jqXHR, textStatus ) {
        alert( "Request failed: " + textStatus );
    });
}
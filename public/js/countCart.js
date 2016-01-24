function countCart(user_id){
    if(user_id != null && user_id != ''){
        var request = $.ajax({
        url: "/scripts/countCart.php",
        data: { user_id: user_id },
        type: "POST",
        dataType: "text"
        });

        request.done(function(data){
            var x = document.getElementById("countCart").innerHTML = data;
        });

        request.fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });
    }
}
function delete_field(id){
    var r = confirm("Are you sure you want to delete this file?");
    if(r == true)
    {
/*        request = $.ajax({
            url: '/scripts/del.php',
            data: {'file' : file , 'id' : id},
            type: 'POST'
        });

        request.done(function(data){
            console.log(data);
        });

        request.fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });*/

        var x = document.getElementById(id);
        x.innerHTML = '';

        var y = document.createElement("input");
        y.type = "file";

        if(id == 'file') {
            y.name = "file";
            y.accept=".txt,.pdf,.doc,.docx";
        }
        else {
            y.name = "image";
            y.accept=".jpg,.jpeg,.png";
        }

        x.appendChild(y);
    }
}
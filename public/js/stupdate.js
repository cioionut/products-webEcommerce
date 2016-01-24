function stupdate() {
    var a = document.getElementById("upButton");
    a.innerHTML = '';
    var span = document.createElement('span');
    span.className = "glyphicon glyphicon-refresh spinning";
    a.appendChild(span);
    a.innerHTML = a.innerHTML + " Processing... ";

    var request = $.ajax({
        url: "/orders/stupdate",
        type: "POST",
        dataType: "json"
    });

    request.done(function (data) {
        console.log(data);
        a.innerHTML = 'Update Again';
        for(i = 0; i < data.length; ++i){
            document.getElementById('stateId' + data[i].id).innerHTML = data[i].state;
        }
    });

    request.fail(function (jqXHR, textStatus) {
        a.innerHTML = 'Processing Failed';
        a.className = "btn btn-lg btn-warning";
    });
}
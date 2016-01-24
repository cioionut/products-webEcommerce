function processPayment() {
    if(!document.getElementById('procButton')) {
        var divButton = document.getElementById("divButton");
        var a = document.createElement('a');
        a.className = "btn btn-lg btn-info";
        a.id = 'procButton';
        var span = document.createElement('span');
        span.className = "glyphicon glyphicon-refresh spinning";
        a.appendChild(span);
        a.innerHTML = a.innerHTML + " Processing... ";
        divButton.appendChild(a);

        var request = $.ajax({
            url: "paypal",
            type: "POST",
            dataType: "json"
        });

        request.done(function (data) {
            if (!data.error) {
                console.log(data.approvalLink);
                a.innerHTML = 'Approval Link';
                a.href = data.approvalLink;
            }

        });

        request.fail(function (jqXHR, textStatus) {
            a.innerHTML = 'Processing Failed';
            a.className = "btn btn-lg btn-warning";
        });
    }
}
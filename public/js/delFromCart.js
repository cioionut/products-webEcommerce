function delFromCart(formId){
    var conf = confirm("Are you sure ?");
    if(!conf){
        var del_form = document.getElementById(formId);
        del_form.action = '';
    }
    else {
        document.getElementById(formId).submit();
    }
}
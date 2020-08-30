jQuery(document).ready(function() {
    var checked = false;
    jQuery(".pageDelete").submit(function(event) {
        if (! checked) {
            event.preventDefault()
            var answer = confirm("Delete the page")
            if (answer){
                checked = true;
                event.target.submit();
            } else {
                
            }
        }
    });
});
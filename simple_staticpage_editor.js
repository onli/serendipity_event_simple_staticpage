jQuery("#preview").ready(function() {
    jQuery("#preview").click(function() {
        showPreview();
    });
    jQuery("#preview").keyup(function(event){
        if(event.keyCode == 13){
            jQuery(this).click();
        }
        
    });

    
});
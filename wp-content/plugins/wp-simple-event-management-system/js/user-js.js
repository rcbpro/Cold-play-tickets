jQuery(document).ready(function($){
    if($('.validate_form').length) {
        $('.validate_form').validationEngine();
    }
    if($('.tabs').length) {
        $('.tabs').tabs({fx : {opacity : 'toggle', height : 'toggle'}});
    }
    $('input[type="text"]').focus(function() {
        this.select();
    });
    $('input[type="text"]').blur(function() {
        if ($.trim(this.value) == ''){
            this.value = (this.defaultValue ? this.defaultValue : '');
        }
    });
});
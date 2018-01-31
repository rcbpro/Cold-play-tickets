jQuery(document).ready(function($){
    if($('a.del_button').length) {
        $('a.del_button').click(function(){
            return confirm("Are you sure you want to delete? No undo!");
        });
    }
    if($('.date_field').length) {
        $(".date_field").datepicker({ dateFormat: 'yy-mm-dd' });
    }
    
    if($('a.reset_but').length) {
        $('a.reset_but').click(function(){
            return confirm("Are you sure you want to RESET? No undo!");
        });
    }
});
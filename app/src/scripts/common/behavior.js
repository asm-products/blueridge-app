$(document).ready(function(){
    $('#devwidth').html($(window).width());
    $(window).resize(function() {
        $('#devwidth').html($(window).width());
    });
});
/**
 * Application
 *
 * global application js
 */
 $(document).ready(function(){
    $('.app-navigation .nav-link').click(function(){
       var img = new Image();
       img.src = $('#loading-bar').attr('src');
       $(this).parent('li').addClass('active');
       $(this).parent('li').siblings().removeClass('active');
       $('#loading-message p').html($(this).data('message'));
       $('.panel').html($('#loading-message').html());
   });
});
/**
 * Projects 
 * 
 */
 $(document).ready(function(){
    $('#project-selection-list').submit(function(){
        $('.apply-project-selections').addClass('disabled').attr('disabled','disabled').text('Working...');
    });
});
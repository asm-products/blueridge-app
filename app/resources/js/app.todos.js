/**
 * To Dos
 * 
 */
 $(document).ready(function(){
    var user_id = $("#module").attr('data-user');
    var jqxhr = $.get( "api/todos/?u="+user_id);
    jqxhr.done(function(response){
        $(".loading").hide();
        $("#tally").html("Projects: "+response.projects+", To Dos: "+response.count);
        var template = Blueridge['todo-list.html'].render(response);
        $("#todos").html(template).mixitup({
            layoutMode: 'list',
            sortOnLoad: ['data-duedate','desc'],
            transitionSpeed: 300,
            onMixStart: function(config) {
                $("html, body").animate({ scrollTop: 0 }, "fast");
                if (config.filter != 'all' && config.filter != 'mix_all'){
                    $('.app-assignee').addClass('soloed');
                    $('.app-filter-message, .mix-filters').show();
                } else {
                    $('.app-assignee').removeClass('soloed');
                    $('.app-filter-message, .mix-filters').hide();
                }
            },
        });
    });

    $('.app-mute-assignee').click(function(){
        var token = $(this).data('filter');
        var name = $(this).siblings('.app-assignee-name').html();
        $('.mix.' + token).hide();
        $('#app-assignee-filter-show-all').removeClass('active');
        $('<span class="unhide label" data-token="' + token + '"">' + name + ' <i class="icon-plus"></i></span>').appendTo('.muted-people');
    });
    $('.muted-people').on('click', '.unhide', function() {
       var token = $(this).data('token');
       $('.mix.' + token).show();
       $(this).detach();
   });
    $('#app-assignee-filter-show-all').click(function(){
       $('.mix').show();
       $('.unhide').detach();
   });
    $('#app-assignee-filter-show-unassigned').click(function(){
       $('.unhide').detach();
   });

    /* Check To-do as done */
    $('.app-checkdone-submit').change(function(event){    

       var todoId = $(this).attr('data-todo-id');
       console.log('we are checking this off'+todoId);
       request = $.ajax({
          url: "/api/todos/"+todoId+'/',
          type: "post",
          data: {completed:true}
      });
       request.done(function (response, textStatus, jqXHR){
        // log a message to the console
        console.log(response);
        console.log("Hooray, it worked!");
    });

   });

    /* Toggle To-do details */
    $('.app-todo-title').click(function(){
       $(this).parents('.row-fluid').siblings('.app-todo-details').toggle();
   });

    /* Change duedate */
    $('.app-todo-changeduedate').click(function(event){
       event.preventDefault();
       $(this).popover({
          animation: false,
          placement: "bottom",
          title: "Set the due date:",
          content: "this should be a calendar"
      });
   });

    /* Change assignee */
    $('.app-assignee-name').click(function(event){
       event.preventDefault();
       $(this).popover({
          animation: false,
          placement: "bottom",
          title: "Assign this to-do to:",
          content: "names of potential assignees here"
      });
   });

});
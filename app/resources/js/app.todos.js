/**
 * To Dos
 *
 */
 $(document).ready(function(){
    var userid = $("#module").attr('data-user');
    var jqxhr = $.get( "api/todos/?userid="+userid);
    jqxhr.done(function(response){
        $(".loading").hide();
        $("#tally").html("Projects: "+response.projects+", To Dos: "+response.count);
        if(response.count > 0){
            renderTodos(response);
        } else {
            console.log('no todos');
            $(".messages").show();
        }
        $(".app-checkdone-submit" ).on( "change", checkOffTodo );

        $(".app-todo-title").click(function(e){
            e.preventDefault();
            details = $(this).parents('.row-fluid').siblings('.app-todo-details'); //.toggle();
            console.log($(details).attr('data-comment-count'));
            if ($(details).attr('data-comment-count') > 0) {
                $(details).toggle();
            }
        });

        $(".todo-commentform .btn").click(function(){
            todo_uid= $(this).attr('data-todo-uid');
            comment = $("#"+todo_uid).val();
            // console.log(todo_uid);
            // console.log(comment);
        });
    });

    function renderTodos(response) {
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
    }

    function checkOffTodo() {
        var todoid = $(this).attr('data-todo-id');
        var userid = $("#module").attr('data-user');
        $(this).parents('.app-todo-list-item').fadeOut().remove();
        request = $.post( "/api/todos/"+todoid+'/', {user:userid,payload:{completed:true}});
    }

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
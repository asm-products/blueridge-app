$(document).ready(function() {

    var todos ={
        name: 'todos',
        url: "api/todos/?userid="+$("#module").attr('data-user'),
        method: 'GET'
    };
    jqxhrFetcher(todos);
});

function jqxhrFetcher(resource){

    var jqxhr = $.ajax({
        url:resource.url,
        type:resource.method,
        xhrFields: {
            onprogress: function (e) {
                console.log(e);
                if (e.lengthComputable) {
                    console.log(e.loaded / e.total * 100 + '%');
                }
            }
        },
    });

    jqxhr.done(function(response){
        console.log(resource);
        switch(resource.name)
        {
            case 'todos':
            handleTodos(response);
            break;
            case 'comments':
            handleComments(response);
            break;
        }

    });

}

// Todos

function handleTodos(result) {
    $(".loading").hide();
    $("#tally").html("Projects: "+result.projects+", To Dos: "+result.count);
    if(result.count < 0){
        console.log('no todos');
        $(".no-todos").show();
        return;
    } else {
        var template = Blueridge['todo-list.html'].render(result);
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

        $(".app-checkdone-submit" ).on( "change", checkOffTodo );
        $(".add-comment-btn").on("click",addComment);

    }
}

function checkOffTodo() {
    var todoid = $(this).attr('data-todo-id');
    var userid = $("#module").attr('data-user');
    $(this).parents('.app-todo-list-item').fadeOut().remove();
    request = $.post( "/api/todos/"+todoid+'/', {user:userid,payload:{completed:true}});
}


// comments

function addComment() {
    console.log('sending comment');
    console.log($(this).attr('data-todo-id'));
    // request = $.post( "/api/comments/"+todoid+'/', {user:userid,payload:{completed:true}});
}

function handleComments(response) {
    console.log('handling comments');
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


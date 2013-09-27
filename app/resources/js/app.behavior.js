// app.behavior.js

$(document).ready(function(){

	$('.app-navigation .nav-link').click(function(){
		var img = new Image();
		img.src = $('#loading-bar').attr('src');
		$(this).parent('li').addClass('active');
		$(this).parent('li').siblings().removeClass('active');
		$('#loading-message p').html($(this).data('message'));
		$('.panel').html($('#loading-message').html());
	});

	var plan_limits = {
		'br-free':3,
		'br-solo':5,
		'br-manager':10,'br-pro':30
	};
	var plan_limit = plan_limits[$('#project-list').data('plan')];

	$('#project-list').on('change', '.project', function(){
		var box = $(this);
		var selected_count = $('.project:checked').size();
		if(selected_count > plan_limit) {
			$('.modal-body p').html('Whoops, your plan allows ' + plan_limit + ' projects at a time. You can upgrade your plan to see more projects at once.');
			box.attr("checked", false);
			$('#plan-limit-modal').modal();
		}
	});

  /**
  * To-dos Page
  */

  // var tokenArray = new Object;
  // $('.assignee-name').each(function(){
  //   var token = $(this).data('nametoken');
  //   tokenArray[token] = $(this).text();
  // });

  // $.each(tokenArray, function(index,value){
  //   $('<button class="btn btn-mini filter" data-filter="' + index + '"">' + value + '</button>').appendTo('.mix-filters');
  // });

  $('#todos').mixitup({
    animateGridList: false,
    //sortOnLoad: ['data-duedate','desc'],
    //effects: ['fade'],
    transitionSpeed: 0,
    onMixStart: function(config) {
      // $("html, body").animate({ scrollTop: 0 }, "fast");
      // if (config.filter != 'all' && config.filter != 'mix_all'){
      //   $('.app-assignee').addClass('soloed');
      //   $('.app-filter-message, .mix-filters').show();
      // } else {
      //   $('.app-assignee').removeClass('soloed');
      //   $('.app-filter-message, .mix-filters').hide();
      // }
    },
    onMixEnd: function(config) {
      $('.filter').bind('click', function(){
        $('#todos').mixitup('filter');
      });
    }
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



	/**
	* Projects Page
	*/

	$('#project-selection-list').submit(function(){
		$('.apply-project-selections').addClass('disabled').attr('disabled','disabled').text('Working...');
	});


	/**
	* Profile Page
	*/
	$('#update-subscription').submit(function(){
		if($('#update-payment').data('card') == 'none') {
			event.preventDefault();
			$('#payment-empty-modal').modal();
			return false;
		}
	});

	$('#dismiss-payment-warning').click(function(){
		return initStripe();
	});

	$('#payment-button').click(function(){
		return initStripe();
	});

	function initStripe() {
		var token = function(res){
			var $input = $('<input type=hidden name=paymentToken />').val(res.id);
			$('#update-payment').append($input).submit();
		};
		StripeCheckout.open({
			key: $('#update-payment').attr('data-key'),
			address:false,
			currency:'usd',
			name:'BlueRidge',
			panelLabel:'Update Payment Method',
			token:token
		});
		return false;
	}
});
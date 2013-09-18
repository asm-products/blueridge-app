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
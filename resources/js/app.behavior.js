// app.behavior.js

$(document).ready(function(){

	var plan_limits = {'br-free':3,'br-solo':5,'br-manager':10,'br-pro':30}
	var plan_limit = plan_limits[$('#project-list').data('plan')]

	// Makes sure they don't check more boxes than they should.
	$('#project-list').on('change', '.project', function(){
		var box = $(this);
		var selected_count = $('.project:checked').size();
		if(selected_count > plan_limit) {
			$('.modal-body p').html('Whoops, your plan allows ' + plan_limit + ' projects at a time. You can upgrade your plan to see more projects at once.');
			box.attr("checked", false);
			$('#plan-limit-modal').modal();
		}
	});


});
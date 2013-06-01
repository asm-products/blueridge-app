'use strict';

/* Services */
angular.module('blueRidge.services', ['ngResource']).
factory('ToDos', function($resource) {
	//var url = 'http://dev-api.blueridgeapp.com/todos/?user_id=5197a7887f95f2c5a6000000';
	return $resource('/data/todos.json', {}, {
		query: {method:'GET', isArray:true}
	});
});
'use strict';
angular.module('blueRidge.services', ['ngResource']).
factory('ToDos', function($resource) {
	var url = 'http://api.blueridgeapp.com/todos';
	var ToDos = $resource(url,{},{
		query : {method:'GET', isArray:true}
	});
	return ToDos;
});
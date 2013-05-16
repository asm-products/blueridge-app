'use strict';
angular.module('blueRidge', ['blueRidge.services']).
config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/todos', {
		templateUrl: 'partials/todos.html', 
		controller: 'ToDoCtrl'
	});
	$routeProvider.when('/people', {
		templateUrl: 'partials/people.html', 
		controller: 'PeopleCtrl'
	});
	$routeProvider.otherwise({
		redirectTo: '/todos'
	});
}]);
'use strict';
angular.module('blueRidge', ['blueRidge.services']).
config(['$routeProvider','$locationProvider', function($routeProvider,$locationProvider) {
	$routeProvider.when('/', {
		//templateUrl: 'views/home.html', 
		controller: 'ToDoCtrl'
	});	
	$routeProvider.when('/todos', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDoCtrl'
	});
	$routeProvider.when('/people', {
		templateUrl: 'views/people.html', 
		controller: 'PeopleCtrl'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
	$locationProvider.html5Mode(true);
}]);
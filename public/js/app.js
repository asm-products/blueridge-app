'use strict';
angular.module('blueRidge', ['blueRidge.services']).
config(['$routeProvider',function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl: 'views/home.html', 
		controller: 'HomeCtrl'
	});
		
	$routeProvider.when('/todos', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDoCtrl'
	});
	$routeProvider.when('/login', {
		templateUrl: 'views/login.html', 
		controller: 'LoginCtrl'
	});
	$routeProvider.when('/people', {
		templateUrl: 'views/people.html', 
		controller: 'PeopleCtrl'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
}]);
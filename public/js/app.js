'use strict';
angular.module('blueRidge', ['blueRidge.services']).
config(['$routeProvider',function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl: 'views/home.html', 
		//controller: 'HomeCtrl',
		access:'access.public'
	});
	$routeProvider.when('/login', {
		templateUrl: 'views/login.html', 
		//controller: 'LoginCtrl',
		access:'access.public'
	});

	$routeProvider.when('/todos/:userId/', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDosController',
		access:'access.user'
	});

	$routeProvider.when('/people/:userId/', {
		templateUrl: 'views/people.html', 
		controller: 'PeopleCtrl',
		access:'access.user'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
}]).
run();
'use strict';
angular.module('blueRidge', ['blueRidge.services','ui.bootstrap'])
.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	$routeProvider.when('/', {
		templateUrl: 'views/home.html',
		controller: 'HomeCtrl',		
	});
	$routeProvider.when('/dash', {
		templateUrl: 'views/dash.html', 
		controller: 'DashCtrl'
	});

	$routeProvider.when('/todos', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDoCtrl'
	});
	$routeProvider.when('/me', {
		templateUrl: 'views/me.html', 
		controller: 'MeCtrl'
	});

	$routeProvider.when('/people', {
		templateUrl: 'views/people.html', 
		controller: 'PeopleCtrl'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
	$locationProvider.html5Mode(true);
	
}]).run();
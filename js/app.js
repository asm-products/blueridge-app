'use strict';
var blueRidgeApp = angular.module('blueRidgeApp', ['blueRidgeApp.controllers','blueRidgeApp.services','ui.bootstrap'])
.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	$routeProvider.
	when('/', {
		templateUrl: 'views/home.html',
		controller: 'HomeCtrl',		
	})
	.when('/dash', {
		templateUrl: 'views/dash.html', 
		controller: 'DashCtrl'
	})
	.when('/todos', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDoCtrl'
	})
	.when('/me', {
		templateUrl: 'views/me.html', 
		controller: 'MeCtrl'
	})
	.when('/people', {
		templateUrl: 'views/people.html', 
		controller: 'PeopleCtrl'
	})
	.when('/basecamp', {
		templateUrl: 'views/home.html', 
		controller: 'BasecampCtrl'
	})
	.when('/connect', {
		templateUrl: 'views/home.html', 
		controller: 'ConnectCtrl'
	})
	.otherwise({
		redirectTo: '/'
	});
	$locationProvider.html5Mode(true);
}]).run();
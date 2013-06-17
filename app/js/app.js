'use strict';
var blueRidgeApp = angular.module('blueRidgeApp', ['blueRidgeApp.controllers','blueRidgeApp.services','ui.bootstrap','restangular'])
.config(['$routeProvider', '$locationProvider','RestangularProvider', function($routeProvider, $locationProvider,RestangularProvider) {
	$routeProvider.
	when('/', {
		templateUrl: 'views/home.html',
		controller: 'HomeCtrl',		
	})
	.when('/settings', {
		templateUrl: 'views/settings.html', 
		controller: 'SettingsCtrl'
	})
	.when('/signout', {
		templateUrl: 'views/home.html',
		controller: 'SignOutCtrl'
	})
	.when('/todos', {
		templateUrl: 'views/todos.html', 
		//controller: 'ToDoCtrl'
	})
	.when('/me', {
		templateUrl: 'views/me.html', 
		//controller: 'MeCtrl'
	})
	.when('/people', {
		templateUrl: 'views/people.html', 
		//controller: 'PeopleCtrl'
	})
	.when('/basecamp', {
		templateUrl: 'views/loading.html', 
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
	RestangularProvider.setBaseUrl("/api");
	RestangularProvider.setListTypeIsArray(false);
}]).run();
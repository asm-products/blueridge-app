'use strict';
var blueRidgeApp = angular.module('blueRidgeApp', ['blueRidgeApp.controllers','blueRidgeApp.services','ui.bootstrap','restangular'])
.config(['$routeProvider', '$locationProvider','$dialogProvider','RestangularProvider', function($routeProvider, $locationProvider,$dialogProvider,RestangularProvider) {
	$routeProvider.
	when('/', {
		templateUrl: 'views/home.html',
		controller: 'HomeCtrl',		
	})
	.when('/todos', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDoCtrl'
	})
	.when('/me', {
		templateUrl: 'views/me.html', 
		controller: 'MeCtrl'
	})
	.when('/projects', {
		templateUrl: 'views/projects.html', 
		controller: 'SettingsCtrl'
	})		
	.when('/signout', {
		templateUrl: 'views/home.html',
		controller: 'SignOutCtrl'
	})
	.when('/connect', {
		templateUrl: 'views/home.html', 
		controller: 'ConnectCtrl'
	})
	.when('/basecamp', {
		templateUrl: 'views/loading.html', 
		controller: 'BasecampCtrl'
	})
	.otherwise({
		redirectTo: '/'
	});
	$locationProvider.html5Mode(true);
	$dialogProvider.options({backdropClick: false, dialogFade: true});
	RestangularProvider.setBaseUrl("/api");
	RestangularProvider.setDefaultHttpFields({cache: true},{headers:{'User-Agent':'blueridgeapp'}});
	RestangularProvider.setListTypeIsArray(false);
}]).run();
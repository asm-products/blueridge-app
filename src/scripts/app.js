var blueRidgeApp = angular.module('blueRidgeApp', ['blueRidgeApp.controllers','blueRidgeApp.services','blueRidgeApp.directives','ui.bootstrap','restangular'])
.config(['$routeProvider', '$locationProvider','$dialogProvider','RestangularProvider', function($routeProvider, $locationProvider,$dialogProvider,RestangularProvider) {    $routeProvider.
    when('/', {
        templateUrl: '/views/site/home.html',
        controller: 'HomeCtrl'
    })
    .when('/signout', {
        templateUrl: '/views/site/home.html',
        controller: 'SignOutCtrl'
    })
    .when('/connect', {
        templateUrl: '/views/site/home.html',
        controller: 'ConnectCtrl'
    })
    .when('/pricing', {
        templateUrl: '/views/site/pricing.html'
    })
    .when('/preview', {
        templateUrl: '/views/site/preview.html'
    })
    .when('/privacy', {
        templateUrl: '/views/site/privacy.html'
    })
    .when('/app/todos', {
        templateUrl: '/views/app/todos.html',
        controller: 'ToDoCtrl'
    })
    .when('/app/me', {
        templateUrl: '/views/app/me.html',
        controller: 'MeCtrl'
    })
    .when('/app/projects', {
        templateUrl: '/views/app/projects.html',
        controller: 'ProjectCtrl'
    })
    .when('/basecamp', {
        templateUrl: '/views/app/init.html',
        controller: 'BasecampCtrl'
    })
    .otherwise({
        redirectTo: '/'
    });
    $locationProvider.html5Mode(true);
    RestangularProvider.setBaseUrl('/api');
    RestangularProvider.setListTypeIsArray(false);
}]).run();
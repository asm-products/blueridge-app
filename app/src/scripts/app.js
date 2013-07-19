var blueRidgeApp = angular.module('blueRidgeApp', ['blueRidgeApp.controllers','blueRidgeApp.services','blueRidgeApp.directives','ui.bootstrap','restangular']);
blueRidgeApp.config(['$routeProvider', '$locationProvider','$dialogProvider','RestangularProvider', function($routeProvider, $locationProvider,$dialogProvider,RestangularProvider) {$routeProvider.
    when('/', {
        templateUrl: '/views/site/home.html',
        controller: 'HomeCtrl'
    })
    .when('/connect', {
        templateUrl: '/views/site/connect.html',
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
    .when('/app/signout', {
        templateUrl: '/views/app/signout.html',
        controller: 'SignOutCtrl'
    })
    .when('/app/basecamp', {
        templateUrl: '/views/app/init.html',
        controller: 'BasecampCtrl'
    })
    .otherwise({
        redirectTo: '/'
    });
    $locationProvider.html5Mode(true);
    RestangularProvider.setBaseUrl('/api');
    RestangularProvider.setListTypeIsArray(false);
}]);
blueRidgeApp.run(function($rootScope,$location) {
    $rootScope.$on('$routeChangeStart', function(ev,data) {
        var path = window.location.pathname.split( '/' );
        $rootScope.groupName='site';
        if(path[1]=='app'){
            $rootScope.groupName='app';
        }

        $rootScope.isActive = function(route) {
            return route === $location.path();
        };
    });
});

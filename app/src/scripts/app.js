var blueRidgeApp = angular.module('blueRidgeApp', [
    'blueRidgeApp.services',
    'blueRidgeApp.directives',
    'ui.bootstrap',
    'restangular',
    'angular-google-analytics'
    ]);
blueRidgeApp.config(['$routeProvider', '$locationProvider','$dialogProvider','RestangularProvider','AnalyticsProvider', function($routeProvider, $locationProvider,$dialogProvider,RestangularProvider,AnalyticsProvider) {
    $routeProvider.
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
    .when('/about', {
        templateUrl: '/views/site/about.html'
    })
    .when('/app/todos', {
        templateUrl: '/views/app/todos.html',
        controller: 'TodosCtrl'
    })
    .when('/app/profile', {
        templateUrl: '/views/app/profile.html',
        controller: 'ProfileCtrl'
    })
    .when('/app/projects', {
        templateUrl: '/views/app/projects.html',
        controller: 'ProjectsCtrl'
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

    AnalyticsProvider.setAccount('UA-31702803-4');
    AnalyticsProvider.trackPages(true);
    AnalyticsProvider.setDomainName('blueridgeapp.com');
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

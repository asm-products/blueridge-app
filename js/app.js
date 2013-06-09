'use strict';
angular.module('blueRidge', ['blueRidge.services','ui.bootstrap'])
.config(['$routeProvider', '$locationProvider','$httpProvider', function($routeProvider, $locationProvider,$httpProvider) {
	var access = routingConfig.accessLevels;
	$routeProvider.when('/', {
		templateUrl: 'views/home.html', 		
		access:'access.anon'
	});
	$routeProvider.when('/dash', {
		templateUrl: 'views/dash.html', 
		controller: 'DashCtrl',
		access:'access.user'
	});

	$routeProvider.when('/todos', {
		templateUrl: 'views/todos.html', 
		controller: 'ToDoCtrl',
		access:'access.user'
	});
	$routeProvider.when('/me', {
		templateUrl: 'views/me.html', 
		controller: 'MeCtrl',
		access:'access.user'
	});

	$routeProvider.when('/people', {
		templateUrl: 'views/people.html', 
		controller: 'PeopleCtrl',
		access:'access.user'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
	$locationProvider.html5Mode(true);
	
	
	var interceptor = ['$location', '$q', function($location, $q) {
		function success(response) {
			return response;
		}
		function error(response) {
			if(response.status === 401) {
				$location.path('/');
				return $q.reject(response);
			}
			else {
				return $q.reject(response);
			}
		}
		return function(promise) {
			return promise.then(success, error);
		}
	}];
	$httpProvider.responseInterceptors.push(interceptor);
	

}])//.run();

.run(['$rootScope', '$location', 'Auth', function ($rootScope, $location, Auth) {
	$rootScope.$on("$routeChangeStart", function (event, next, current) {
		$rootScope.error = null;

		if (!Auth.authorize(next.access)) {
			if(Auth.isLoggedIn()) {
				$location.path('/dash');
			}
			else{ 
				$location.path('/');
			}
		}
	});

	$rootScope.appInitialized = true;
}]);

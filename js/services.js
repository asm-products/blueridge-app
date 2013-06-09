'use strict';
angular.module('blueRidge.services', ['ngResource','ngCookies'])
.factory('ToDos', function($resource) {
	//var url = 'http://dev-api.blueridgeapp.com/todos';
	var url ='/data/todos.json';
	var ToDos = $resource(url,{},{
		query : {method:'GET', isArray:true}
	});
	return ToDos;
})
.factory('User', function($resource) {
	//var url = 'http://dev-api.blueridgeapp.com/users/:id';
	var url='/data/user.json';
	return $resource(url);
})
.factory('Auth', function($http, $rootScope, $cookieStore) {
	var accessLevels = routingConfig.accessLevels
	, userRoles = routingConfig.userRoles;

	$rootScope.user = $cookieStore.get('user') || { username: '', role: userRoles.public };
	$cookieStore.remove('user');

	$rootScope.accessLevels = accessLevels;
	$rootScope.userRoles = userRoles;

	return {
		authorize: function(accessLevel, role) {
			if(role === undefined)
				role = $rootScope.user.role;
			return accessLevel & role;
		},
		isLoggedIn: function(user) {
			if(user === undefined)
				user = $rootScope.user;
			return user.role === userRoles.user || user.role === userRoles.admin;
		},
		register: function(user, success, error) {
			$http.post('/register', user).success(success).error(error);
		},
		signin: function(user, success, error) {
			$http.post('/login', user).success(function(user){
				$rootScope.user = user;
				success(user);
			}).error(error);
		},
		logout: function(success, error) {
			$http.post('/logout').success(function(){
				$rootScope.user.username = '';
				$rootScope.user.role = userRoles.public;
				success();
			}).error(error);
		},
		accessLevels: accessLevels,
		userRoles: userRoles
	};

});
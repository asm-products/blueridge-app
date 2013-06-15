'use strict';
angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	if (Auth.isLoggedIn()) {
		$location.path('/me');
	}
})
.controller('SettingsCtrl',function($scope,$location,Auth,User,Basecamp){	
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();
})
.controller('SignInCtrl',function($scope,$location,Auth,User){

	$scope.opts = {
		backdropFade: true,
		dialogFade:true
	};
	$scope.open = function() {
		$scope.shouldBeOpen = true;
	};

	$scope.close = function() {
		$scope.shouldBeOpen = false;
	};

	$scope.signin = function(user) {
		$scope.signedIn=Auth.authorize(user);
		if($scope.signedIn){
			$location.path('/me');
		}
	}
})
.controller('ToDoCtrl',function($scope,$location,Auth,ToDos,User){	
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.todos = ToDos.query();
	console.log(User.get());
	//$scope.user = User.get();
})
.controller('MeCtrl',function($scope,$location,Auth,Restangular){
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	
	Restangular.one('users',Auth.currentUser()).get().then(function(user){
		$scope.user = user;
	});
	
})
.controller('PeopleCtrl',function($scope,$location,Auth,User) {
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();

})
.controller('ConnectCtrl',function($scope,Restangular) {
	Restangular.one('providers','basecamp').get().then(function(provider){
		window.location=provider.authUrl;
	});
})
.controller('BasecampCtrl',function($scope,$location,Restangular,Auth) {
	var code = $location.search().code;
	var providers= Restangular.all('users').customPOST("", {}, {}, {code:code,provider:'basecamp'}).then(function(user){
		Auth.authorize(user);
		$scope.user = user;
		window.location='/';
		//$location.path('/'); 
	},function() {
		console.log("There was an error saving");
	});
});
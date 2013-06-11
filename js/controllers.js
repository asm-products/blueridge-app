'use strict';
angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	if (Auth.isLoggedIn()) {
		$location.path('/dash');
	}
})
.controller('DashCtrl',function($scope,$location,Auth,User){	
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
			$location.path('/dash');
		}
	}
})
.controller('ToDoCtrl',function($scope,$location,Auth,ToDos,User){	
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.todos = ToDos.query();
	$scope.user = User.get();
})
.controller('MeCtrl',function($scope,$location,Auth,User){
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();
})
.controller('PeopleCtrl',function($scope,$location,Auth,User) {
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();

})
.controller('ConnectCtrl',function($scope) {	
	var url = "https://launchpad.37signals.com/authorization/new?client_id=cbc3f4cff1def7da310df4d74c7baaffa106772a&redirect_uri=http%3A%2F%2Fdev-www.blueridgeapp.com%2Fbasecamp&type=web_server"
	window.location=url;

})
.controller('BasecampCtrl',function($scope,$location,Auth) {

	var params = $location.search();
	console.log(params.code);
	//$location.path('/');
	//-window.close();
});
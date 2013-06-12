'use strict';
angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	/*if (Auth.isLoggedIn()) {
		$location.path('/dash');
	}*/
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
.controller('ConnectCtrl',function($scope,Basecamp) {
	var basecamp =  Basecamp.connect(function() {
		window.location=basecamp.authUrl;
	});
})
.controller('BasecampCtrl',function($scope,$location,Basecamp) {
	var verificationCode = $location.search().code;
	var basecamp = new Basecamp({code:verificationCode});
	var authCode = basecamp.$authorize();

	//console.log(basecamp.$save());

	
	//$location.path('/');
	//-window.close();
});
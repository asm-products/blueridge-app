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
.controller('ConnectCtrl',function($scope,Restangular) {
	Restangular.one('providers','basecamp').get().then(function(provider){
		window.location=provider.authUrl;
	});
})
.controller('BasecampCtrl',function($scope,$location,Restangular) {
	var code = $location.search().code;

	//change the post to a 
	var providers= Restangular.one('providers','basecamp').customPOST("", {}, {}, {code:code}).then(function(user){
		console.log(user);
	});
	
	//providers.post({code:code}).then(function(user){
	//	console.log(user);
	//});

	/*
	Restangular.post('providers','basecamp',{code:code}).then(function(user) {
		console.log(user);
	}, function() {
		console.log("There was an error saving");
	});
	*/

	//console.log(code);

	//create a todo
	//var basecamp = new Basecamp();
	//basecamp.code = code;
	//var newuser = basecamp.$save();
	//console.log(basecamp.$authorize());
	//todo1.foo = 'bar';
	//todo1.something = 123;
	//todo1.$save();


	//$scope.authCode = function(){
	//	$scope.user=Basecamp.authorize({code:code});
	//	Auth.authorize($scope.user);
	//	$location.path('/');
	//}
});
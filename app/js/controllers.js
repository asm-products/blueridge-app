'use strict';
angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	if (Auth.isSignedIn()) {
		$location.path('/me');
	}
})
.controller('SettingsCtrl',function($scope,$location,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	var blueRidgeUser = Restangular.one('users',Auth.getProfileUser().id);
	$scope.user = blueRidgeUser.get();


	$scope.update = function(user) {
		console.log(user);
		blueRidgeUser=user;
		console.log(user);
		blueRidgeUser.put();
	}	
})
.controller('SignOutCtrl',function($scope,$location,Auth){	
	Auth.signOut();
	$location.path('/');
})
.controller('SignInCtrl',function($scope,$location,Auth){

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
.controller('ToDoCtrl',function($scope,$location,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	Restangular.one('users',Auth.getProfileUser().id).get().then(function(user){
		$scope.user = user;
		console.log(user);
	});

	//$scope.todos = ToDos.get();
	//console.log(User.get());
	//$scope.user = User.get();
})
.controller('MeCtrl',function($scope,$location,Auth,Restangular){
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}

/*	Restangular.one('users',Auth.profile().id).get().then(function(user){
		$scope.user = user;
	});
*/	
})
.controller('PeopleCtrl',function($scope,$location,Auth,User) {
	if (!Auth.isSignedIn()) {
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
	var basecampUser = Restangular.all('users');
	basecampUser.post({code:code,provider:'basecamp'}).then(function(auth){
		Auth.authorize(auth);
		$location.path('/settings').search('code',null); 
	},function() {
		console.log("There was an error saving");
	});

});
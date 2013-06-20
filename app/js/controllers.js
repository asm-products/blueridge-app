'use strict';
angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	if (Auth.isSignedIn()) {
		$location.path('/todos');
	}
})
.controller('SettingsCtrl',function($scope,$location,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	var blueRidgeUser = Restangular.one('users',Auth.getProfileUser().id);
	$scope.user = blueRidgeUser.get();

	$scope.updateAccounts = function(accounts) {
		blueRidgeUser.accounts=accounts;
		blueRidgeUser.put();
		if($location.path()=='/projects'){
			$location.path('/todos');
		}
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
			$location.path('/activity');
		}
	}
})
.controller('ToDoCtrl',function($scope,$location,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}

	var blueRidgeTodos = Restangular.all('todos');
	blueRidgeTodos.getList({user:Auth.getProfileUser().id}).then(function(data){
		$scope.user=data.user;
		$scope.todos=data.todos;
	});

})
.controller('MeCtrl',function($scope,$location,Auth,Restangular){
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	var blueRidgeUser = Restangular.one('users',Auth.getProfileUser().id);
	$scope.user = blueRidgeUser.get();

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
		$location.path('/projects').search('code',null); 
	},function() {
		console.log("There was an error saving");
	});

});
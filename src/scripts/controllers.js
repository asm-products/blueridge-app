angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	if (Auth.isSignedIn()) {
		$location.path('/app/todos');
	}
})
.controller('ProjectCtrl',function($scope,$location,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	$scope.firstTime=false;
	/*if (Auth.isInit()) {
		$scope.firstTime=true;
	}*/
	var blueRidgeUser = Restangular.one('users',Auth.getProfileUser().id);
	$scope.user = blueRidgeUser.get();
	$scope.updated = false;
	
	$scope.updateAccounts = function(accounts) {
		blueRidgeUser.accounts=accounts;
		blueRidgeUser.put().then(function(){
			$scope.updated = true;
			$location.path('/app/todos');
		});
	};
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
			$location.path('/app/todos');
		}
	};
})
.controller('ToDoCtrl',function($scope,$location,$filter,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	var blueRidgeUser = Restangular.one('users',Auth.getProfileUser().id);
	$scope.user = blueRidgeUser.get();
	$scope.loading = true;

	blueRidgeUser.all('todos').getList().then(function(result){
		var todos = result.todos;
		$scope.todos = todos;
		$scope.loading = false;
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
		$location.path('/app/projects').search('code',null); 
	},function() {
		console.log("There was an error saving account");
	});

});
angular.module('blueRidgeApp.controllers', [])
.controller('HomeCtrl',function($scope,$location,Auth){
	if (Auth.isSignedIn()) {
		$location.path('/app/todos');
	}
})
.controller('NavCtrl',function($scope){
	$scope.isCollapsed = false;
})
.controller('ProjectCtrl',function($scope,$location,$filter,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	$scope.noob=Auth.isNoob();

	$scope.loading = true;
	blueRidgeUser= Restangular.one('users',Auth.currentUser());

	blueRidgeUser.getList('accounts').then(function(result){
		$scope.accounts = result.accounts;
		$scope.loading = false;

	});

	$scope.updateAccounts = function(accounts) {
		blueRidgeUser.accounts=accounts;
		blueRidgeUser.put().then(function(){
			$scope.updated = true;
			$location.path('/app/todos');
			Auth.promoteNoob();
		});
	};
})
.controller('SignOutCtrl',function($scope,$location,Auth){	
	Auth.signOut();
	$location.path('/');
})
.controller('SignInCtrl',function($scope,$location,Restangular,Auth){

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
		blueRidgeAccess = Restangular.all('auth');
		blueRidgeAccess.post(user).then(function(auth){
			Auth.authorize(auth);
			$scope.shouldBeOpen = false;
			$location.path('/app/todos');
		},function(auth) {
			console.log(auth);
			console.log("There was an error logging in");
		});
	};
})
.controller('ToDoCtrl',function($scope,$location,$filter,Restangular,Auth){	
	if (!Auth.isSignedIn()) {
		$location.path('/');
	}
	var blueRidgeUser = Restangular.one('users',Auth.currentUser());
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
	var blueRidgeUser = Restangular.one('users',Auth.currentUser());
	$scope.user = blueRidgeUser.get();

})
.controller('ConnectCtrl',function($scope,Restangular) {
	Restangular.one('providers','basecamp').get().then(function(provider){
		window.location=provider.authUrl;
	});
})
.controller('BasecampCtrl',function($scope,$location,$filter,Restangular,Auth) {
	var code = $location.search().code;
	var basecampUser = Restangular.all('users');
	basecampUser.post({code:code,provider:'basecamp'}).then(function(auth){
		Auth.authorize(auth);
		$location.path('/app/projects').search('code',null); 
	},function() {
		console.log("There was an error saving account");
	});

})
.controller('SubscriptionCtrl',function($scope,$location,Auth,Restangular){
	Restangular.one('services','cashier').get().then(function(cashier){
		$scope.cashier=cashier;
	});

	$scope.open=function(){
		$scope.shouldBeOpen = true;
		Stripe.setPublishableKey($scope.cashier.key);

	};
	$scope.createToken = function(card){
		Stripe.card.createToken(card, $scope.subscribe);
		console.log(card);
	};
	$scope.subscribe = function(status, responce){
		//Stripe.card.createToken(card, $scope.subscribe);
		console.log(status);
		console.log(responce);
	};
});
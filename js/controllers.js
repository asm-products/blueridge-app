'use strict';
/* Controllers */

function HomeCtrl($scope,$location,Auth){
	if (Auth.isLoggedIn()) {
		$location.path('/dash');
	}
}
function DashCtrl($scope,$location,Auth,User){	

	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();
}
function SignInCtrl($scope,$location,Auth,User){
	
	$scope.open = function () {
		$scope.shouldBeOpen = true;
	};

	$scope.close = function () {
		$scope.shouldBeOpen = false;
	};

	$scope.opts = {
		backdropFade: true,
		dialogFade:true
	};
	$scope.signin = function(user) {
		$scope.signedIn=Auth.authorize(user);
		if($scope.signedIn){
			$location.path('/dash');
		}
	}; 
}
function ToDoCtrl($scope,Auth,ToDos,User){	
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.todos = ToDos.query();
	$scope.user = User.get();
}

function MeCtrl($scope,Auth,User){
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();
}


function PeopleCtrl($scope,Auth,User) {
	if (!Auth.isLoggedIn()) {
		$location.path('/');
	}
	$scope.user = User.get();

}
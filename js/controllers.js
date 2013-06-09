'use strict';
/* Controllers */


function DashCtrl($rootScope,$scope,Auth,ToDos,User){
}

function SignInCtrl($scope,Auth,User){
	
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

	//
	$scope.user={};
	$scope.login = function(user) {
		$scope.user= angular.copy(user);
		console.log(user);
		Auth.login({
			username: $scope.username,
			password: $scope.password,
			rememberme: $scope.rememberme
		},
		function(res) {
			$location.path('/');
		},
		function(err) {
			$rootScope.error = "Failed to login";
		});
		
	}; 

}
function ToDoCtrl($rootScope,$scope,Auth,ToDos,User){
	/*
	$scope.todos = ToDos.query({user_id:$routeParams.userId});
	$scope.user = User.get({id:$routeParams.userId});
	console.log($scope.user);
	*/
}

function MeCtrl($rootScope,$scope,Auth,User){
	$scope.user = User.get();
}


function PeopleCtrl($rootScope,$scope,Auth,User) {
	//$scope.members = Project.findMembers();
	//console.log($scope.members);
}
/*
function UsersCtrl($scope, Users) {
	//$scope.members = Project.findUser();
	//console.log($scope.members);
}
*/
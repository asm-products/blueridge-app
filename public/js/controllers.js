'use strict';
/* Controllers */
/*function LoginCtrl($scope, User) {
	//$scope.members = Project.findUser();
	//console.log($scope);
}*/
function ToDosController($scope,$routeParams,ToDos){
	$scope.todos = ToDos.query({user_id:$routeParams.userId});
	console.log($scope.todos);
}

function PeopleCtrl($scope, Project) {
	//$scope.members = Project.findMembers();
	//console.log($scope.members);
}
function UsersCtrl($scope, Users) {
	//$scope.members = Project.findUser();
	//console.log($scope.members);
}

'use strict';
/* Controllers */
function HomeCtrl($scope, Project) {
	//$scope.members = Project.findUser();
	//console.log($scope);
}
function ToDosController($scope, ToDos){
	$scope.todos = ToDos.query();
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
function LoginCtrl($scope, Project) {
	//$scope.members = Project.findUser();
	//console.log($scope);
}
'use strict';
/* Controllers */
function ToDoCtrl($scope, Project) {
	$scope.todos = Project.findTodos();
	console.log($scope.todos);
}

function PeopleCtrl($scope, Project) {
	$scope.members = Project.findMembers();
	console.log($scope.members);
}
angular.module('project', ['mongolab']).
  config(function($routeProvider) {
    $routeProvider.
      when('/', {controller:ListCtrl, templateUrl:'view/list.html'}).
      otherwise({redirectTo:'/'});
  });
 
 
function ListCtrl($scope, Project) {
  $scope.projects = Project.query();
}
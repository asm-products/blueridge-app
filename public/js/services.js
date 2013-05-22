'use strict';

/* Services */
angular.module('blueRidge.services', ['ngResource']).
factory('Project', function($resource) {

//$scope.Users = $resource("http://localhost/users/:id",{id:'@id'});
  var Project = $resource('http://dev-api.blueridgeapp.com/todos/:id/',{id: '@id' });
  return Project;
});
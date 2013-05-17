'use strict';

/* Services */
angular.module('blueRidge.services', ['ngResource']).
factory('Project', function($resource) {
  var Project = $resource('http://dev-api.blueridgeapp.com/todos/',
      { apiKey: '4f847ad3e4b08a2eed5f3b54' }, 
      { 
    	  'findMembers' : {method:'GET', isArray:true},
    	  'findTodos' : {method:'GET', isArray:true},
      });

  return Project;
});
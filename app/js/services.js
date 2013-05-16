'use strict';

/* Services */
angular.module('blueRidge.services', ['ngResource']).
factory('Project', function($resource) {
  var Project = $resource('https://api.mongolab.com/api/1/databases/angularjs/collections/:type',
      { apiKey: '4f847ad3e4b08a2eed5f3b54' }, 
      { 
    	  'findMembers' : {method:'GET', params: {type: 'members'}, isArray:true},
    	  'findTodos' : {method:'GET', params: {type: 'Todos'}, isArray:true},
      });

  return Project;
});
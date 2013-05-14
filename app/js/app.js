'use strict';


// Declare app level module which depends on filters, and services
angular.module('blueRidge', ['blueRidge.filters', 'blueRidge.services', 'blueRidge.directives', 'blueRidge.controllers']).
  config(['$routeProvider', function($routeProvider) {
    $routeProvider.when('/todos', {templateUrl: 'partials/todos.html', controller: 'ToDoCtrl'});
    $routeProvider.when('/people', {templateUrl: 'partials/people.html', controller: 'PeopleCtrl'});
    $routeProvider.otherwise({redirectTo: '/todos'});
  }]);

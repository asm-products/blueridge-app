/*! blueridge 13-07-2013 */
(function ( window, angular, undefined ) {
;var blueRidgeApp = angular.module('blueRidgeApp', [
    'blueRidgeApp.controllers',
    'blueRidgeApp.services',
    'blueRidgeApp.directives',
    'ui.bootstrap',
    'restangular'
  ]).config([
    '$routeProvider',
    '$locationProvider',
    '$dialogProvider',
    'RestangularProvider',
    function ($routeProvider, $locationProvider, $dialogProvider, RestangularProvider) {
      $routeProvider.when('/', {
        templateUrl: 'views/site/home.html',
        controller: 'HomeCtrl'
      }).when('/todos', {
        templateUrl: 'views/app/todos.html',
        controller: 'ToDoCtrl'
      }).when('/me', {
        templateUrl: 'views/app/me.html',
        controller: 'MeCtrl'
      }).when('/projects', {
        templateUrl: 'views/app/projects.html',
        controller: 'ProjectCtrl'
      }).when('/signout', {
        templateUrl: 'views/site/home.html',
        controller: 'SignOutCtrl'
      }).when('/connect', {
        templateUrl: 'views/site/home.html',
        controller: 'ConnectCtrl'
      }).when('/basecamp', {
        templateUrl: 'views/app/init.html',
        controller: 'BasecampCtrl'
      }).when('/pricing', { templateUrl: 'views/site/pricing.html' }).when('/preview', { templateUrl: 'views/site/preview.html' }).when('/privacy', { templateUrl: 'views/site/privacy.html' }).otherwise({ redirectTo: '/' });
      $locationProvider.html5Mode(true);
      RestangularProvider.setBaseUrl('/api');
      RestangularProvider.setListTypeIsArray(false);
    }
  ]).run();;angular.module('blueRidgeApp.controllers', []).controller('HomeCtrl', [
  '$scope',
  '$location',
  'Auth',
  function ($scope, $location, Auth) {
    if (Auth.isSignedIn()) {
      $location.path('/todos');
    }
  }
]).controller('ProjectCtrl', [
  '$scope',
  '$location',
  'Restangular',
  'Auth',
  function ($scope, $location, Restangular, Auth) {
    if (!Auth.isSignedIn()) {
      $location.path('/');
    }
    var blueRidgeUser = Restangular.one('users', Auth.getProfileUser().id);
    $scope.user = blueRidgeUser.get();
    $scope.updated = false;
    $scope.updateAccounts = function (accounts) {
      blueRidgeUser.accounts = accounts;
      blueRidgeUser.put().then(function () {
        $scope.updated = true;
      });
    };
  }
]).controller('SignOutCtrl', [
  '$scope',
  '$location',
  'Auth',
  function ($scope, $location, Auth) {
    Auth.signOut();
    $location.path('/');
  }
]).controller('SignInCtrl', [
  '$scope',
  '$location',
  'Auth',
  function ($scope, $location, Auth) {
    $scope.opts = {
      backdropFade: true,
      dialogFade: true
    };
    $scope.open = function () {
      $scope.shouldBeOpen = true;
    };
    $scope.close = function () {
      $scope.shouldBeOpen = false;
    };
    $scope.signin = function (user) {
      $scope.signedIn = Auth.authorize(user);
      if ($scope.signedIn) {
        $location.path('/activity');
      }
    };
  }
]).controller('ToDoCtrl', [
  '$scope',
  '$location',
  '$filter',
  'Restangular',
  'Auth',
  function ($scope, $location, $filter, Restangular, Auth) {
    if (!Auth.isSignedIn()) {
      $location.path('/');
    }
    var blueRidgeUser = Restangular.one('users', Auth.getProfileUser().id);
    $scope.user = blueRidgeUser.get();
    $scope.loading = true;
    blueRidgeUser.all('todos').getList().then(function (result) {
      var todos = result.todos;
      $scope.todos = todos;
      $scope.loading = false;
    });
  }
]).controller('MeCtrl', [
  '$scope',
  '$location',
  'Auth',
  'Restangular',
  function ($scope, $location, Auth, Restangular) {
    if (!Auth.isSignedIn()) {
      $location.path('/');
    }
    var blueRidgeUser = Restangular.one('users', Auth.getProfileUser().id);
    $scope.user = blueRidgeUser.get();
  }
]).controller('ConnectCtrl', [
  '$scope',
  'Restangular',
  function ($scope, Restangular) {
    Restangular.one('providers', 'basecamp').get().then(function (provider) {
      window.location = provider.authUrl;
    });
  }
]).controller('BasecampCtrl', [
  '$scope',
  '$location',
  'Restangular',
  'Auth',
  function ($scope, $location, Restangular, Auth) {
    var code = $location.search().code;
    var basecampUser = Restangular.all('users');
    basecampUser.post({
      code: code,
      provider: 'basecamp'
    }).then(function (auth) {
      Auth.authorize(auth);
      $location.path('/projects').search('code', null);
    }, function () {
      console.log('There was an error saving');
    });
  }
]);;angular.module('blueRidgeApp.directives', []).directive('loadingContainer', function () {
  return {
    restrict: 'A',
    scope: false,
    link: function (scope, element, attrs) {
      var loadingLayer = $('<div class="loading"></div>').appendTo(element);
      $(element).addClass('loading-container');
      scope.$watch(attrs.loadingContainer, function (value) {
        loadingLayer.toggle(value);
      });
    }
  };
});;angular.module('blueRidgeApp.services', ['ngCookies']).factory('Auth', [
  '$cookieStore',
  function ($cookieStore) {
    return {
      authorize: function (auth) {
        $cookieStore.put('_blrdgapp', auth);
        return true;
      },
      signOut: function () {
        return $cookieStore.remove('_blrdgapp');
      },
      isSignedIn: function () {
        return $cookieStore.get('_blrdgapp') ? true : false;
      },
      getProfileUser: function () {
        return $cookieStore.get('_blrdgapp');
      }
    };
  }
]);;})( window, window.angular );
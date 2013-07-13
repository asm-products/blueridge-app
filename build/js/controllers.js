angular.module('blueRidgeApp.controllers', []).controller('HomeCtrl', [
  '$scope',
  '$location',
  'Auth',
  function ($scope, $location, Auth) {
    if (Auth.isSignedIn()) {
      $location.path('/todos');
    }
  }
]).controller('SettingsCtrl', [
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
    $scope.updateAccounts = function (accounts) {
      blueRidgeUser.accounts = accounts;
      $scope.updated = false;
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
  'ngTableParams',
  function ($scope, $location, $filter, Restangular, Auth, ngTableParams) {
    if (!Auth.isSignedIn()) {
      $location.path('/');
    }
    var blueRidgeUser = Restangular.one('users', Auth.getProfileUser().id);
    $scope.user = blueRidgeUser.get();
    var blueRidgeTodos = Restangular.all('todos');
    $scope.loading = true;
    $scope.tableParams = new ngTableParams({
      page: 1,
      total: 0,
      count: 30,
      sorting: { overDueBy: 'desc' }
    });
    blueRidgeTodos.getList({ user: Auth.getProfileUser().id }).then(function (result) {
      var data = result.todos;
      $scope.todos = data;
      $scope.tableParams.total = data.length;
      $scope.$watch('tableParams', function (params) {
        $scope.loading = false;
        var orderedData = params.sorting ? $filter('orderBy')(data, params.orderBy()) : data;
        $scope.todos = orderedData.slice((params.page - 1) * params.count, params.page * params.count);
      }, true);
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
]);
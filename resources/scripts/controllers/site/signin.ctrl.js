angular.module('blueRidgeApp')
.controller('SignInCtrl',function($scope,$location,Restangular,Auth){
    $scope.alerts = [];
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };
    $scope.signin = function(user) {
        blueRidgeAccess = Restangular.all('auth');
        blueRidgeAccess.post(user).then(function(auth){
            Auth.authorize(auth);
            $location.path('/app/todos');
        },function(auth) {
          $scope.alerts.push({type: 'error', msg: "Oh No! That didn't work. Please double check you email and password"});
      });
    };
});
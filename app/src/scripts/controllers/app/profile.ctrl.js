angular.module('blueRidgeApp')
.controller('ProfileCtrl',function($scope,$location,Auth,Restangular){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    var blueRidgeUser = Restangular.one('users',Auth.currentUser());
    $scope.user = blueRidgeUser.get();
});
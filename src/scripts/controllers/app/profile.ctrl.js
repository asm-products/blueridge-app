angular.module('blueRidgeApp')
.controller('ProfileCtrl',function($rootScope,$scope,$location,Auth,Restangular){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    blueRidgeUser.get().then(function (user){
        $scope.user = user;
    });

});
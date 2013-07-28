angular.module('blueRidgeApp')
.controller('ProfileCtrl',function($rootScope,$scope,$location,Auth,Restangular,PaymentHelpers){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    blueRidgeUser.get().then(function (user){
        $scope.user = user;
    });

});
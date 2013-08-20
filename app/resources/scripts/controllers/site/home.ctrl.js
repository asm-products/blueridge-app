angular.module('blueRidgeApp')
.controller('HomeCtrl',function($scope,$location,Auth){
    if (Auth.isSignedIn()) {
        $location.path('/app/todos');
    }
});
angular.module('blueRidgeApp')
.controller('SignOutCtrl',function($scope,$location,Auth){  
    Auth.signOut();
    $location.path('/signin');
});
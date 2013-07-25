angular.module('blueRidgeApp')
.controller('ProjectsCtrl',function($scope,$location,$filter,Restangular,Auth){  
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    $scope.noob=Auth.isNoob();

    $scope.loading = true;
    blueRidgeUser= Restangular.one('users',Auth.currentUser());

    blueRidgeUser.getList('accounts').then(function(result){
        $scope.accounts = result.accounts;
        $scope.loading = false;

    });

    $scope.updateAccounts = function(accounts) {
        blueRidgeUser.accounts=accounts;
        blueRidgeUser.put().then(function(){
            $scope.updated = true;
            $location.path('/app/todos');
            Auth.promoteNoob();
        });
    };
});
angular.module('blueRidgeApp')
.controller('TodosCtrl',function($scope,$location,$filter,Restangular,Auth){ 
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    var blueRidgeUser = Restangular.one('users',Auth.currentUser());
    $scope.user = blueRidgeUser.get();
    $scope.loading = true;

    blueRidgeUser.all('todos').getList().then(function(result){
        var todos = result.todos;
        $scope.todos = todos;
        $scope.loading = false;
    });

    $scope.exportCSV = function exportCSV(){
        window.location.href='/export';
    };

});
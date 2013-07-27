angular.module('blueRidgeApp')
.controller('ProjectsCtrl',function($scope,$location,$filter,Restangular,Auth){  
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    $scope.noob=Auth.isNoob();

    $scope.loading = true;
    blueRidgeUser= Restangular.one('users',Auth.currentUser());

    blueRidgeUser.getList('projects').then(function(result){
        console.log(result);

        $scope.projects = result.projects;
        $scope.loading = false;

    });

    $scope.update= function(projects) {
        console.log(projects);
        //blueRidgeUser.projects=projects;
        
       // blueRidgeUser.put().then(function(){
        //    $scope.updated = true;
        //    $location.path('/app/todos');
        //    Auth.promoteNoob();
        //});

    };
});
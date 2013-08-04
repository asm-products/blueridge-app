angular.module('blueRidgeApp')
.controller('ProjectsCtrl',function($scope,$location,$filter,Restangular,Auth){  
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    $scope.noob=Auth.isNoob();
    $scope.loading = true;
    blueRidgeUser= Restangular.one('users',Auth.currentUser());

    blueRidgeUser.getList('projects').then(function(response){
        $scope.projects = response.projects;
        $scope.loading = false;

    });

    $scope.update= function() {
        var projects = $scope.projects;
        var selected=[];
        angular.forEach(projects,function(project){
            if(project.selected){
                selected.push(project.id);
            }
        });
        blueRidgeUser.profile = {
            projects:selected
        };
        blueRidgeUser.put().then(function(){
            $scope.updated = true;
            $location.path('/app/todos');
            Auth.promoteNoob();
        });
    };
});
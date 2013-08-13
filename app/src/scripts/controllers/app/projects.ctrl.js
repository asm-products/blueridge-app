angular.module('blueRidgeApp')
.controller('ProjectsCtrl',function($scope,$location,$filter,Restangular,Auth){  
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    $scope.noob=Auth.isNoob();
    $scope.shouldBeOpen = false;
    $scope.loading = true;
    blueRidgeUser= Restangular.one('users',Auth.currentUser());

    blueRidgeUser.getList('projects').then(function(response){
        $scope.projects = response.projects;
        $scope.loading = false;

    });

    $scope.subscription={
        plan:{id:'',name:''},
        card:''
    };
    $scope.maxalert=false;
    $scope.max=1;
    blueRidgeUser.one('subscription').get().then(function(subscription){
        $scope.subscription=subscription;
        switch(subscription.plan.id){
            case 'br-solo':
            max = 5;
            message= "The Solo plan allows you to see 5 projects at a time. If you'd like to see more (and who wouldn't), please upgrade to Pro.";
            break;
            case 'br-pro':
            message= "Wow! You've maxed out the Pro plan. If you really need to see more than 20 projects at once please let us know. Maybe we'll make a special plan for you. &#x263A";
            max = 20;
            break;
            default:
            max = 1;
            message= "Slow down, sparky. You can only see one project at a time on the Free plan. Please upgrade to see more than one at a time.";
        }
        $scope.max=max;
        $scope.maxmessage=message;

    });

    $scope.isMaxed= function isMaxed(){
        if($scope.selectedCount == max){  
            return true;
        }
        return false;
    };


    $scope.checkChange = function checkChange (project) {
        if (project.selected) {
            $scope.currentProject = project;
        }
        $scope.selectedCount = 0;
        angular.forEach($scope.projects, function(p) {
            $scope.selectedCount += (p.selected ? 1 : 0);
        });

    };

    $scope.update= function update() {
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
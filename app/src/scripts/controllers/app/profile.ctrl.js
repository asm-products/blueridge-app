blueRidgeApp.controller('ProfileCtrl',['$scope','$location','Auth','Restangular',function ProfileCtrl($scope,$location,Auth,Restangular){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    
    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    $scope.user = blueRidgeUser.get();
    $scope.subscription={
        plan:{id:'',name:''},
        card:''
    };

    blueRidgeUser.one('subscription').get().then(function(subscription){
        $scope.subscription=subscription;
    });

    $scope.changePlan = function changePlan(plan){
        $location.path('/app/cart/'+plan);
    };
    $scope.isCurrentPlan = function isCurrentPlan(plan){
        if($scope.subscription.plan.id == plan){
            return true;
        }
        return false;
    };

}]);
blueRidgeApp.controller('ProfileCtrl',['$scope','$location','Auth','Restangular',function ProfileCtrl($scope,$location,Auth,Restangular){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    $scope.user = blueRidgeUser.get();

    blueRidgeUser.one('subscription').get().then(function (result){
        $scope.subscription = result.subscription;        
    });

    $scope.changePlan = function changePlan(plan){
        $location.path('/app/cart/'+plan);
    };
    $scope.isCurrentPlan = function isCurrentPlan(plan){
        if( $scope.subscription.plan == plan){
            return true;
        }
        return false;
    };

}]);
blueRidgeApp.controller('CartCtrl',['$scope','$route','$routeParams','$location','Auth','Restangular','$cookieStore',function ProfileCtrl($scope,$location,$route,$routeParams,Auth,Restangular,$cookieStore){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    $scope.cart = $route;
    var safeApply = function safeApply(fn) {
        if($scope.$$phase || $scope.$root.$$phase){
            fn();
        }else{
            $scope.$apply(fn);
        }
    };


    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    $scope.user = blueRidgeUser.get();
    $scope.subscription = {};
    
    var blueRidgeUserSubscription = Restangular.copy(blueRidgeUser);
    var subscriber = blueRidgeUser.one('subscription').get().then(function(result){
        $scope.subscription=result.subscription;    
    });
    $scope.paymentMethodIsSet =function paymentMethodisSet (){        
        
        if(_.isEmpty($scope.subscription.payment)){
            return false;
        }
        return true;
    };


    Restangular.one('services','cashier').get().then(function(stripe){
        $scope.stripe = stripe;
    });

    $scope.addPayment = function addPayment(token) {
        $scope.subscription={payment:token};
        $scope.$apply();
    };

    $scope.updateSubscription=function updateSubscription(plan){

        blueRidgeUser.subscription = {
            plan:plan,
            payment:$scope.subscription.payment
        };

        blueRidgeUser.put().then(function(response){
            $location.path('/app/profile');
        });
    };
    
}]);
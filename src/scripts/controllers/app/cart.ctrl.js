blueRidgeApp.controller('CartCtrl',['$scope','$route','$routeParams','$location','Auth','Restangular','$cookieStore',function ProfileCtrl($scope,$location,$route,$routeParams,Auth,Restangular,$cookieStore){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    $scope.activity = $route;
    var safeApply = function safeApply(fn) {
        if($scope.$$phase || $scope.$root.$$phase){
            fn();
        }else{
            $scope.$apply(fn);
        }
    };

    $scope.changePlan=function changePlan(plan){
        console.log('changing plan');
    };

    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    $scope.user = blueRidgeUser.get();
    $scope.payment={};

    Restangular.one('services','cashier').get().then(function(stripe){
        $scope.stripe = stripe;
    });


    $scope.addPayment = function makePayment(token) {
        $scope.payment = token;
        console.log(token);
        $scope.$apply();
    };
}]);
blueRidgeApp.controller('ProfileCtrl',['$scope','$route','$location','Auth','Restangular','$cookieStore',function ProfileCtrl($scope,$location,$route,Auth,Restangular,$cookieStore){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    var safeApply = function safeApply(fn) {
        if($scope.$$phase || $scope.$root.$$phase){
            fn();
        }else{
            $scope.$apply(fn);
        }
    };

    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    blueRidgeUser.get().then(function (user){
        $scope.user = user;
        $scope.plan = $scope.user.profile.plan;
    });

    Restangular.one('services','cashier').get().then(function(stripe){
        $scope.stripe = stripe;
    });


    $scope.makePayment = function makePayment(token,plan) {
        safeApply(function() {
            $scope.wait = true;
        });

        var customer= {
            user:$scope.user.id,
            token:token.id,
            plan:plan
        };

        Restangular.all('subscriptions').post(customer).then(
            function success (resource){
                safeApply(function() {
                    $scope.wait = false;
                       //$location.url('/invoice/' + resource._id);
                });

                console.log('result from api');
                console.log(resource);
            },
            function error(){
                console.log('There was an error saving');
                safeApply(function() {
                    $scope.wait = true;
                    $route.reload();
                });
            });
    };
}]);
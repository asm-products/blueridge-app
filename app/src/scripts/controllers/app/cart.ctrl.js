blueRidgeApp.controller('CartCtrl',['$scope','$route','$location','Auth','Restangular',function ProfileCtrl($scope,$route,$location,Auth,Restangular){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }
    $scope.cart = $route.current.params;

    var safeApply = function safeApply(fn) {
        if($scope.$$phase || $scope.$root.$$phase){
            fn();
        }else{
            $scope.$apply(fn);
        }
    };


    blueRidgeUser = Restangular.one('users',Auth.currentUser());

    $scope.user = blueRidgeUser.get();

    $scope.subscription={
        plan:{id:'',name:''},
        cards:''
    };
    $scope.tokenid=null;

    blueRidgeUser.one('subscription').get().then(function(subscription){
        $scope.subscription=subscription;
    });

    $scope.stripe = Restangular.one('services','subscriber').get();

    $scope.addPayment = function addPayment(token) {
        $scope.subscription.card=token.card;
        $scope.tokenid=token.id;
        $scope.$apply();
    };

    $scope.needsPayment = function needsPayment() {
        if($scope.cart.plan == 'free')
        {
            return false;
        }
        return true;
    };

    $scope.paymentMethodIsSet =function paymentMethodisSet (){        

        if(_.isEmpty($scope.subscription.card)){
            return false;
        }
        return true;
    };

    $scope.updateSubscription=function updateSubscription(){

        blueRidgeUser.subscription = {
            plan:'br-'+$scope.cart.plan,
            payment: $scope.tokenid
        };
        
        blueRidgeUser.put().then(function(response){
            safeApply(function() {
                $scope.wait = false;
                $location.path('/app/profile');
            });            
        });

    };

}]);
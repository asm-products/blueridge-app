angular.module('blueRidgeApp')
.controller('ProfileCtrl',function($scope,$location,Auth,Restangular,PaymentHelpers){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    $scope.alerts = [];
    
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };

    var blueRidgeUser = Restangular.one('users',Auth.currentUser()).get().then(function (user){
        $scope.user = user;
        $scope.plan = $scope.user.plan;
        $scope.setPlan($scope.plan);
    });

    var blueRidgeCashier = Restangular.one('services','cashier');
    blueRidgeCashier.get().then(function(stripe){
        Stripe.setPublishableKey(stripe.key);
    });

    


    $scope.setPlan = function(plan){
        switch (plan){
            case 'solo': case 'pro':
            canSubscribe = true;
            break;
            default:
            canSubscribe = false;
        }
        $scope.card= {'plan':plan,'name':$scope.user.name};
        $scope.canSubscribe=canSubscribe;
    };

    $scope.createPayment = function(card){
        var expiry = PaymentHelpers.cardExpiryVal(card.expiry);
        var subscriber = {
            name:card.name,
            number:card.number,
            exp_month:expiry.month,
            exp_year:expiry.year
        };

        Stripe.card.createToken(subscriber,$scope.subscribe);
    };

    $scope.subscribe =  function(status,response){

        if (response.error) {
            $scope.alerts.push({ type: 'error', msg: 'Oh my! '+response.error.message });
        } else {
            var customer= {user:$scope.user.id, payment:response,plan:$scope.card.plan};
            console.log(response);
            console.log(customer);

            Restangular.all('subscriptions').post(customer).then(function(result){
                console.log('hello');
                console.log(data);
            });
        }

    };
});
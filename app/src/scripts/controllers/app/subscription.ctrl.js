angular.module('blueRidgeApp')
.controller('SubscriptionCtrl',function($scope,$location,Auth,Restangular,PaymentHelpers){
    Restangular.one('services','cashier').get().then(function(cashier){
        $scope.cashier=cashier;
    });

    $scope.alerts = [];

    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };

     $scope.close=function(){
        $scope.shouldBeOpen = false;
    };

    $scope.open=function(){
        $scope.shouldBeOpen = true;
        Stripe.setPublishableKey($scope.cashier.key);

    };

    $scope.makePayment = function(card){

        if(typeof card != 'undefined'){
            var expiry = PaymentHelpers.cardExpiryVal(card.expiry);

            var subscriber = {
                name:card.name,
                number:card.number,
                exp_month:expiry.month,
                exp_year:expiry.year
            }
            Stripe.card.createToken(subscriber, $scope.subscribe);

        }else{
            $scope.alerts.push({ type: 'error', msg: 'Oh snap! Double check your card details and try submitting again.' });
        }

    };

    $scope.subscribe = function(status, response){
        if (response.error) {
            $scope.alerts.push({ type: 'error', msg: 'Oh my! '+response.error.message });
        } else {

            console.log(response);
        }
    };
});
angular.module('blueRidgeApp')
.controller('SubscriptionCtrl',function($scope,$location,Auth,Restangular){
    Restangular.one('services','cashier').get().then(function(cashier){
        $scope.cashier=cashier;
    });

    $scope.open=function(){
        $scope.shouldBeOpen = true;
        Stripe.setPublishableKey($scope.cashier.key);

    };
    $scope.createToken = function(card){
        Stripe.card.createToken(card, $scope.subscribe);
        console.log(card);
    };
    $scope.subscribe = function(status, responce){
        if (response.error) {
            // show errors
        } else {
            

        }
        //Stripe.card.createToken(card, $scope.subscribe);
        console.log(status);
        console.log(responce);
    };
});
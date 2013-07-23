angular.module('blueRidgeApp')
.controller('SubscriptionCtrl',function($scope,$location,Auth,Restangular){
    Restangular.one('services','cashier').get().then(function(cashier){
        $scope.cashier=cashier;
    });

    $scope.alerts = [];

    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };

    $scope.open=function(){
        $scope.shouldBeOpen = true;
        Stripe.setPublishableKey($scope.cashier.key);

    };

    $scope.makePayment = function(card){
        console.log(card);
        //Stripe.card.createToken(card, $scope.subscribe);
    };

    $scope.subscribe = function(status, response){

        console.log(status);
        if (response.error) {
            console.log(response.error); 
            //$scope.alerts.push({ type: 'error', msg: response});
        } else {
            console.log(response);
        }
    };
});
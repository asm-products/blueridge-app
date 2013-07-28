angular.module('blueRidgeApp')
.controller('ProfileCtrl',function($rootScope,$scope,$location,Auth,Restangular,PaymentHelpers){
    if (!Auth.isSignedIn()) {
        $location.path('/');
    }

    $scope.alerts = [];
    


    $scope.list = [];
    $scope.text = 'hello';

    $scope.card={};
    $scope.subscribe = function() {

        var subscriber = {
            name:this.card.name,
            number:this.card.number,
            expiry:this.card.expiry
        };

        console.log(subscriber);

        if (this.text) {
            this.list.push(this.text);
            this.text = '';
        }
    };


    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };

    blueRidgeUser = Restangular.one('users',Auth.currentUser());
    blueRidgeUser.get().then(function (user){
        $scope.user = user;
        $scope.plan = $scope.user.profile.plan;
        $scope.setPlan($scope.plan);
    });

    blueRidgeCashier = Restangular.one('services','cashier');
    blueRidgeCashier.get().then(function(stripe){
        Stripe.setPublishableKey(stripe.key);
    });

    blueRidgeSubscription = Restangular.all('subscriptions');

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
        Stripe.card.createToken(subscriber,function(status,response){
            if (response.error) {
                $scope.alerts.push({ type: 'error', msg: 'Oh my! '+response.error.message });
            } else {
                $rootScope.token = response.id;
            }
        });
    };




});
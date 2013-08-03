blueRidgeApp.directive('stripe', [function stripe() {
  return {
    restrict: 'EAC',
    scope: {
      settings: '=stripe',
      token: '=',
      callback: '&',
    },
    link: function ($scope, element, attrs) {
      element.on('click', function click() {

        StripeCheckout.open({
          key: $scope.settings.key,
          address: false,
          amount: attrs.price,
          currency:'usd',
          name:'BlueRidge',
          description: attrs.description,
          panelLabel: 'Subscribe',
          token: function token(res) {
            $scope.callback({token: res});
          }
        });
        return false;
      });
    }
  };
}]);
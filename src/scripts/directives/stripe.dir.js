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
          currency:'usd',
          name:'BlueRidge',
          panelLabel: 'Add Card',
          token: function token(res) {
            $scope.callback({token: res});
          }
        });
        return false;
      });
    }
  };
}]);
angular.module('blueRidgeApp')
.controller('SignInCtrl',function($scope,$location,Restangular,Auth){

    $scope.opts = {
        backdropFade: true,
        dialogFade:true
    };
    $scope.open = function() {
        $scope.shouldBeOpen = true;
    };

    $scope.close = function() {
        $scope.shouldBeOpen = false;
    };

    $scope.signin = function(user) {
        blueRidgeAccess = Restangular.all('auth');
        blueRidgeAccess.post(user).then(function(auth){
            Auth.authorize(auth);
            $scope.shouldBeOpen = false;
            $location.path('/app/todos');
        },function(auth) {
            console.log(auth);
            console.log("There was an error logging in");
        });
    };
});
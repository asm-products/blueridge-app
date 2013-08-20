angular.module('blueRidgeApp')
.controller('ConnectCtrl',function($scope,Restangular) {
    Restangular.one('providers','basecamp').get().then(function(provider){
        window.location=provider.authUrl;
    });
});
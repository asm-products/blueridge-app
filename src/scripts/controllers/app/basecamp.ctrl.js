angular.module('blueRidgeApp')
.controller('BasecampCtrl',function($scope,$location,$filter,Restangular,Auth) {
    var code = $location.search().code;
    var basecampUser = Restangular.all('users');
    basecampUser.post({code:code,provider:'basecamp'}).then(function(auth){
        Auth.authorize(auth);
        $location.path('/app/projects').search('code',null); 
    },function() {
        console.log("There was an error saving account");
    });

});
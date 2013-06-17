'use strict';
angular.module('blueRidgeApp.services', ['ngCookies'])
.factory('Auth',function($cookieStore){	
	return {		
		authorize:function(auth){
			$cookieStore.put('_blrdgapp', auth);
			return true;
		},
		isLoggedIn:function(){
			return ($cookieStore.get('_blrdgapp'))?true:false;
		},
		currentUser:function(){
			return $cookieStore.get('_blrdgapp');
		},
		logout:function(){
			return $cookieStore.remove('_blrdgapp');
		}
	}
	
})
.factory('User',function(user){
	$scope.user=user;		
});
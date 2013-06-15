'use strict';
angular.module('blueRidgeApp.services', ['ngCookies'])
.factory('Auth',function($resource,$cookieStore){	
	return {		
		authorize:function(user){
			//get the user and set a hashed key
			$cookieStore.put('_blrdgapp', user.id);
			return true;
		},
		isLoggedIn:function(){
			return ($cookieStore.get('_blrdgapp'))?true:false;
		},
		currentUser:function(){
			return $cookieStore.get('_blrdgapp');
		}
	}
	
});
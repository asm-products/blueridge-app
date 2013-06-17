'use strict';
angular.module('blueRidgeApp.services', ['ngCookies'])
.factory('Auth',function($cookieStore){	
	return {		
		authorize:function(auth){
			$cookieStore.put('_blrdgapp', auth);
			return true;
		},
		signOut:function(){
			return $cookieStore.remove('_blrdgapp');
		},
		isSignedIn:function(){
			return ($cookieStore.get('_blrdgapp'))?true:false;
		},
		getProfileUser:function(){
			return $cookieStore.get('_blrdgapp');
		}		
	}
	
});
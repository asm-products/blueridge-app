angular.module('blueRidgeApp.services', ['ngCookies'])
.factory('Auth',function($cookieStore){	
	return {		
		authorize:function(auth){
			$cookieStore.put('_blrdgapp', auth);
			$cookieStore.put('_blrdgapp_init', auth.init);
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
		},
		isInit:function(){
			return ($cookieStore.get('_blrdgapp_init'))?true:false;
		},
		removeInit:function(){
			return $cookieStore.remove('_blrdgapp_init');
		}
	};
});
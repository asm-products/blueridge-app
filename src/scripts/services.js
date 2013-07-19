angular.module('blueRidgeApp.services', ['ngCookies'])
.factory('Auth',function($cookieStore){	
	return {		
		authorize:function(auth){
			$cookieStore.put('_blrdgapp', auth);
			$cookieStore.put('_blrdgapp_rkie',auth.init); 
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
		isNoob:function(){
			return ($cookieStore.get('_blrdgapp_rkie'))?true:false;
		},
		promoteNoob:function(){
			return $cookieStore.remove('_blrdgapp_rkie');
		},

	};
});

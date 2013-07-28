angular.module('blueRidgeApp.services.auth', ['ngCookies'])
.factory('Auth',function($cookieStore){	
	return {		
		authorize:function(auth){
			$cookieStore.put('_blrdgapp', Base64.encode(auth.id));
			if (auth.init === true){
				$cookieStore.put('_blrdgapp_rkie',auth.init);
			}			
			return true;
		},
		signOut:function(){
			return $cookieStore.remove('_blrdgapp');
		},
		isSignedIn:function(){
			return ($cookieStore.get('_blrdgapp'))?true:false;
		},
		currentUser:function(){
			var stored = $cookieStore.get('_blrdgapp');
			return Base64.decode(stored);
		},
		isNoob:function(){
			return ($cookieStore.get('_blrdgapp_rkie'))?true:false;
		},
		promoteNoob:function(){
			return $cookieStore.remove('_blrdgapp_rkie');
		},

	};
});

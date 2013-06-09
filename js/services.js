'use strict';
angular.module('blueRidge.services', ['ngResource','ngCookies'])
.factory('ToDos', function($resource,$cookieStore) {
	var userId = $cookieStore.get('blueridge');
	var url = 'http://api.blueridgeapp.com/todos';
	//var url ='/data/todos.json';
	var ToDos = $resource(url,{user_id:userId},{
		query : {method:'GET', isArray:true}
	});
	return ToDos;
})
.factory('User', function($resource) {
	var userId = $cookieStore.get('blueridge');
	//var url = 'http://dev-api.blueridgeapp.com/users/:id';
	var url='/data/user.json';
	return $resource(url);
})
.factory('Auth',function($resource,$cookieStore){
	return {		
		authorize:function(user){
			//get the user and set a hashed key
			$cookieStore.put('blueridge', '51af77e891b097bc17000000');
			return true;
		},
		isLoggedIn:function(){
			// calculate the hashes
			return $cookieStore.get('blueridge');
		}
	}
});
'use strict';
angular.module('blueRidgeApp.services', ['ngResource','ngCookies'])
.factory('ToDos', function($resource,$cookieStore) {
	var userId = $cookieStore.get('blueridge');
	var url = 'http://api.blueridgeapp.com/todos';
	var ToDos = $resource(url,{user_id:userId},{
		query : {method:'GET', isArray:true}
	});
	return ToDos;
})
.factory('User', function($resource,$cookieStore) {
	var userId = $cookieStore.get('blueridge');
	var url = 'http://dev-api.blueridgeapp.com/users/'+userId;
	//var url='/data/user.json';
	return $resource(url);
})
.factory('Auth',function($resource,$cookieStore){

	var authorize = function (user){
		console.log('authorize user');
	}

	var getCode = function(){
		apiurl = "http://dev-api.blueridgeapp.com/service/basecamp";
	}
	return {
		authorize:authorize
	}
	/*
	return {		
		authorize:function(user){
			//get the user and set a hashed key
			$cookieStore.put('blueridge', '51b50ee791b097df41000000');
			return true;
		},
		isLoggedIn:function(){
			// calculate the hashes
			return $cookieStore.get('blueridge');
		}
	}
	*/
})
.factory('Basecamp',function($resource,$cookieStore){

	var Basecamp= $resource('/services.php',{},{
		connect: {method:'GET'},
		authorize:{method:'POST'}
	});

	return Basecamp;
});
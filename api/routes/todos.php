<?php
/**
 * Todos
 * @deprecated
 */

use \BlueRidge\Entities\Todo;

$app->get('/api/todos(/:id(:/segment))', function ($id = null,$segment=null) use ($app) {

	$collection = new \StdClass();

	$todo= new Todo($app);
	if(!empty($id)){
		$params=['id'=>$id,$app->request()->get()];
		$todo->fetchOne($params);		

		$collection = $todo->toArray();
	}else{
		$collection->todos = $todo->fetch($app->request()->get());
	}

	if(empty($collection)){
		$app->response()->status(404);
	}

	$resource = json_encode($collection);
	echo $resource;
	
});

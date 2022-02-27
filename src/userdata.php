<?php 

class User {

	public function __construct() {}

	public function login($app, $username, $password){
		
		$sql = "SELECT * FROM users WHERE username = '$username' and password = '$password'";
		$stmt = $app['db']->prepare($sql);
		$stmt->execute();
		$user = $stmt->fetch();

		return $user;
	}

	public function getAllList($app, $userID){

		$sql = "SELECT * FROM todos WHERE user_id = '$userID'";
        $todos = $app['db']->fetchAll($sql);

		return  $todos;
	}

	public function getTodoItem($app, $id, $userID){
		// get user info from session
		$user = $app['session']->get('user');
		
		// check if current user getting correct todo item
		if($user["id"] == $userID){
			$sql = "SELECT * FROM todos WHERE id = '$id' AND user_id = '$userID'";
			$todoItem = $app['db']->fetchAssoc($sql);
		}else{
			$todoItem = '';
		}
		
		return  $todoItem;
	}

	public function addTodoItem($app, $userID, $description){

		$sql = "INSERT INTO todos (user_id, description) VALUES ('$userID', '$description')";
		$addItem = $app['db']->executeUpdate($sql);
		
		return  $addItem;
	}

	public function deleteTodoItem($app, $id){

		$sql = "DELETE FROM todos WHERE id = '$id'";
		$deleteItem = $app['db']->executeUpdate($sql);
		
		return  $deleteItem;
	}


}

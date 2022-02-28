<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('user', $app['session']->get('user'));

    return $twig;
}));


$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', [
        'readme' => file_get_contents('README.md'),
    ]);
});


$app->match('/login', function (Request $request) use ($app) {
    $user = New User();
    $username = $request->get('username');
    $password = $request->get('password');
    $message = '';

    if ($username) {
        // Login
        $userLogin = $user->login($app, $username, $password);
        
        // check user 
        if ($userLogin){
            $app['session']->set('user', $userLogin);
            return $app->redirect('/todo');
        }else {
            // update error message
            $message = 'Please check your username or passwords';
        }
    }

    return $app['twig']->render('login.html', array(
        'message' => $message,
    ));
});


$app->get('/logout', function () use ($app) {
    $app['session']->set('user', null);
    return $app->redirect('/');
});


$app->get('/todo/{id}', function ($id) use ($app) {
    $userInfo = New User();

    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $userId = $user['id'];

    if ($id){

        $todo = $userInfo->getTodoItem($app, $id, $userId);

        if($todo){
            $todoItem = $todo;
        }else{
            $todoItem = '';
        }

        return $app['twig']->render('todo.html', [
            'todo' => $todoItem,
        ]);
        
    } else {

        $todos = $userInfo->getAllList($app, $userId);

        if($todos){
            $todoList = $todos;
        }else{
            $todoList = '';
        }

        return $app['twig']->render('todos.html', [
            'todos' => $todoList,
        ]);
    }
})
->value('id', null);

$app->match('/todolist', function (Request $request) use($app) {
    $userInfo = New User();

    if (null === $user = $app['session']->get('user')) {
        $err = [
            "message" => "Unauthorized"
        ];
        http_response_code(401);
        return '';
    }
    $userId = $user['id'];
    $todoList = $userInfo->getAllList($app, $userId);

    return json_encode($todoList);
});

$app->match('/todoitem/{id}', function ($id) use ($app) {
    $userInfo = New User();

    if (null === $user = $app['session']->get('user')) {
        $err = [
            "message" => "Unauthorized"
        ];
        http_response_code(401);
        return '';
    }
    $userId = $user['id'];
    $todo = $userInfo->getTodoItem($app, $id, $userId);

    if($todo){
        $todoItem = $todo;
    }else{
        $todoItem = '';
    }

    return json_encode($todoItem);
})
->value('id', null);

$app->post('/todo/add', function (Request $request) use ($app) {

    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/login');
    }

    $userInfo = New User();
    $user_id = $user['id'];
    $description = $request->get('description');

    $updateData = $userInfo->addTodoItem($app, $user_id, $description);

    if($updateData){

        http_response_code(200);
        return json_encode($updateData);

    }else{

        http_response_code(500);
        $err = [
            "message" => "Something went wrong"
        ];
        return json_encode($err);
    }
    exit();
});


$app->match('/todo/delete/{id}', function ($id) use ($app) {

    $userInfo = New User();
    $userInfo->deleteTodoItem($app, $id);

    return $app->redirect('/todo');
});
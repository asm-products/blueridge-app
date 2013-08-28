<?php
/**
 * Signin Routes
 */
use \BlueRidge\Entities\User;
use  \BlueRidge\Utilities\Doorman;


$app->get("/signin/", function () use ($app) {
    $flash = $app->view()->getData('flash');

    $error = '';
    if (isset($flash['error'])) {
        $error = $flash['error'];
    }

    $urlRedirect = '/';

    if ($app->request()->get('r') && $app->request()->get('r') != '/signout/' && $app->request()->get('r') != '/signin/') {
        $_SESSION['urlRedirect'] = $app->request()->get('r');
    }

    if (isset($_SESSION['urlRedirect'])) {
        $urlRedirect = $_SESSION['urlRedirect'];
    }

    $email_value = $email_error = $password_error = '';

    if (isset($flash['email'])) {
        $email_value = $flash['email'];
    }

    if (isset($flash['errors']['email'])) {
        $email_error = $flash['errors']['email'];
    }

    if (isset($flash['errors']['password'])) {
        $password_error = $flash['errors']['password'];
    }

    $app->render('/site/signin.html', [
        'error' => $error, 
        'email_value' => $email_value, 
        'email_error' => $email_error, 
        'password_error' => $password_error, 
        'urlRedirect' => $urlRedirect
        ]);
});



$app->post("/signin/", function () use ($app) {


    $email = $app->request()->post('email');
    $password = $app->request()->post('password');

    if(empty($email) || empty($password)){
        $app->response()->status(403);
        $app->flash('errors', 'Missing Credentials');
        $app->redirect('/signin');
    }


    $user = new User($app);
    $user->fetch(['email'=>$email]);
    $authorization = Doorman::authorize($password,$user->key);


    $errors = array();

    if (empty($authorization)) {
        $app->flash('errors', $errors);
        $app->redirect('/signin');
    }

    $_SESSION['user'] = $user->id;

    if (isset($_SESSION['urlRedirect'])) {
        $tmp = $_SESSION['urlRedirect'];
        unset($_SESSION['urlRedirect']);
        $app->redirect($tmp);
    }

    $app->redirect('/');
});
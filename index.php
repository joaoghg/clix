<?php 

session_start();

require_once("vendor/autoload.php");
require_once("require/myAutoload.php");

ini_set('display_errors', 0);

use Page\PageAdmin;
use Slim\Factory\AppFactory;
use Page\Page;
use Model\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = AppFactory::create();

$app->get('/', function() {

    $page = new Page();

    $page->setTpl("index");

});

$app->get('/admin', function() {

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");

});

$app->get('/admin/login', function() {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    $page->setTpl("login");

});

$app->post('/admin/login', function() {

    User::login($_POST['login'], $_POST['password']);

    header("Location: /admin");
    exit;
});

$app->get('/admin/logout', function (){

    User::logout();

    header("Location: /admin/login");
    exit;
});

$app->get('/admin/users', function (){

    User::verifyLogin();

    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl("users", array(
        "users" => $users
    ));
});

$app->get('/admin/users/create', function (){

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("users-create");
});

$app->post('/admin/users/create', function (){

    User::verifyLogin();

    $user = new User();

    $_POST['inadmin'] = isset($_POST['inadmin']) ? 1 : 0;

    $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
        "cost"=>12
    ]);

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;
});

$app->get('/admin/users/{iduser}/delete', function (Request $request, Response $response, $args){

    User::verifyLogin();

    $user = new User();

    $user->get((int) $args['iduser']);

    $user->delete();

    header("Location: /admin/users");
    exit;
});

$app->get('/admin/users/{iduser}', function (Request $request, Response $response, $args){

    User::verifyLogin();

    $user = new User();

    $user->get((int)$args['iduser']);

    $page = new PageAdmin();

    $page->setTpl("users-update", array(
        'user' => $user->getData()
    ));
});

$app->post('/admin/users/{iduser}', function (Request $request, Response $response, $args){

    User::verifyLogin();

    $user = new User();

    $user->get((int)$args['iduser']);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;
});

$app->get("/admin/forgot", function() {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false
    ]);

    $page->setTpl("forgot");

});

$app->post("/admin/forgot", function() {

    $user = User::getForgot($_POST['email']);

});

$app->run();
<?php 

session_start();

require_once("vendor/autoload.php");
require_once("require/myAutoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('display_errors', 0);

use DB\Sql;
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

    $page = new PageAdmin();

    $page->setTpl("login");

});

$app->post('/admin/login', function() {

    try{
        $data = json_decode(file_get_contents('php://input'), true);

        User::login($data['login'], $data['password']);

        http_response_code(200);
        $retorno['status'] = true;
    }catch(\Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

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

    $page->setTpl("users", array("users" => $users));

});

$app->get('/admin/users/create', function (){

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("users-create");

});

$app->get('/checkout', function() {

    $page = new Page();

    $page->setTpl("checkout");
});

$app->run();
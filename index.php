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
        User::login($_POST['login'], $_POST['password']);

        header("Location: /admin");
    }catch(\Exception $erro){
        echo($erro->getMessage());
    }
    exit();
});

$app->run();
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

$app->run();
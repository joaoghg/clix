<?php 

session_start();

require_once("config.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('display_errors', 0);

use DB\Sql;
use Page\PageAdmin;
use Slim\Factory\AppFactory;
use Page\Page;
use Model\User;
use Model\Categoria;
use Model\Products;
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

$app->post('/admin/users/create', function (){

    try{
        User::verifyLogin();

        $data = json_decode(file_get_contents('php://input'), true);

        User::save($data['ps_nome'], $data['user_senha'], $data['ps_contato'], $data['ps_email'], $data['user_login'], $data['user_admin']);

        http_response_code(201);
        $retorno['status'] = true;
    }catch(\Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

});

$app->get('/admin/users/{user_codigo}/delete', function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $user_codigo = $args['user_codigo'];

    User::delete($user_codigo);

    header("Location: /admin/users");
    exit();
});

$app->get('/admin/users/{user_codigo}', function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $user_codigo = $args['user_codigo'];

    $user = User::get($user_codigo);

    $page = new PageAdmin();

    $page->setTpl("users-update", ["user" => $user]);

});

$app->post('/admin/users/update', function (){

    try{
        User::verifyLogin();

        $data = json_decode(file_get_contents('php://input'), true);

        User::update($data['user_codigo'], $data['ps_nome'], $data['ps_contato'], $data['ps_email'], $data['user_login'], $data['user_admin']);

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

$app->get('/checkout', function() {

    $page = new Page();

    $page->setTpl("checkout");
});

$app->get('/admin/categorias', function (){

    User::verifyLogin();

    $categorias = Categoria::listAll();

    $page = new PageAdmin();

    $page->setTpl("categorias", array("categorias" => $categorias));

});

$app->get('/admin/categorias/create', function (){

    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("categorias-create");

});

$app->post('/admin/categorias/create', function (){

    try{
        User::verifyLogin();

        $data = json_decode(file_get_contents('php://input'), true);

        Categoria::save($data['cat_descricao']);

        http_response_code(201);
        $retorno['status'] = true;
    }catch(\Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

});

$app->get('/admin/categorias/{cat_codigo}/delete', function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $cat_codigo = $args['cat_codigo'];

    Categoria::delete($cat_codigo);

    header("Location: /admin/categorias");
    exit();
});

$app->get("/admin/categorias/{cat_codigo}", function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $cat_codigo = $args['cat_codigo'];

    $categoria = Categoria::get($cat_codigo);

    $page = new PageAdmin();

    $page->setTpl("categorias-update", array( "categoria" => $categoria ));

});

$app->post('/admin/categorias/update', function (){

    try{
        User::verifyLogin();

        $data = json_decode(file_get_contents('php://input'), true);

        Categoria::update($data['cat_codigo'], $data['cat_descricao']);

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

$app->get('/admin/products', function (){

    User::verifyLogin();

    $products = Products::listAll();

    $page = new PageAdmin();

    $page->setTpl("products", array("products" => $products));

});

$app->get('/admin/products/create', function (){

    User::verifyLogin();

    $categorias = Categoria::listAll();

    $page = new PageAdmin();

    $page->setTpl("products-create", ["categorias" => $categorias]);

});

$app->post('/admin/products/create', function (){

    User::verifyLogin();

    extract($_REQUEST);

    try{

        $conn = new Sql();

        $conn->beginTransaction();

        $imagens = $_FILES['imagens'];

        Products::save($conn, $prd_descricao, $prd_preco, $prd_peso, $prd_largura, $prd_altura, $prd_comprimento, $prd_obs, $categorias, $imagens);

        $conn->commit();
        $retorno['status'] = true;
        http_response_code(201);
    }catch(Exception $erro){
        $conn->rollback();
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

});

$app->get("/admin/products/{prd_codigo}", function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $prd_codigo = $args['prd_codigo'];

    $produto = Products::get($prd_codigo);

    $categorias = Products::getCategorias($prd_codigo);

    $imagens = Products::getImages($prd_codigo);

    $page = new PageAdmin();

    $page->setTpl("products-update", array( "produto" => $produto, "imagens" => $imagens, "categorias" => $categorias ));

});

$app->run();
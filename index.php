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
use Model\Cart;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app = AppFactory::create();

$app->get('/', function() {

    $products = Products::listAll();

    $page = new Page();

    $dados['products'] = $products;
    if(isset($_SESSION['user'])){
        $dados['user'] = $_SESSION['user'];
    }

    $page->setTpl("index", $dados);
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

    $produtos = Cart::get();

    $page = new Page();

    $page->setTpl("checkout", array("produtos" => $produtos, "user" => $_SESSION['user']));
});

$app->get('/blank', function() {

    $page = new Page();

    $page->setTpl("blank");
});

$app->get('/pagamento', function() {

    $page = new Page();

    $page->setTpl("pay", array("user" => $_SESSION['user']));
});

$app->get('/product/{prd_codigo}', function(Request $request, Response $response, array $args) {

    $page = new Page();

    $product = Products::get($args['prd_codigo']);

    $images = Products::getImages($args['prd_codigo']);

    $categorias = Products::getCategorias($args['prd_codigo']);

    $relateds = Products::getByCategorias($product['cat_codigos'], $args['prd_codigo']);

    $page->setTpl("product", array("product" => $product, "images" => $images, "categorias" => $categorias, "relateds" => $relateds, "user" => $_SESSION['user']));
});

$app->get('/wishlist', function() {

    $page = new Page();

    $page->setTpl("wishlist", array("user" => $_SESSION['user']));
});

$app->get('/cart', function() {

    $page = new Page();

    $produtos = Cart::get();

    $page->setTpl("cart", array("produtos" => $produtos, "user" => $_SESSION['user']));
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

    if($products[0]['categorias'] === null){
        $products = [];
    }

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

    extract($_POST);

    try{

        $conn = new Sql();

        $conn->beginTransaction();

        $imagens = $_FILES['imagens'];

        $products = new Products();

        $products->save($conn, $prd_descricao, $prd_preco, $prd_peso, $prd_largura, $prd_altura, $prd_comprimento, $prd_obs, $categorias, $imagens);

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

$app->post("/admin/products/{prd_codigo}", function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $prd_codigo = $args['prd_codigo'];
    extract($_POST);

    try{

        $products = new Products();

        $products->update($prd_codigo, $prd_descricao, $prd_preco, $prd_peso, $prd_largura, $prd_altura, $prd_comprimento, $prd_obs, $categorias);

        $retorno['status'] = true;
        http_response_code(200);
    }catch(Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));
});

$app->post("/admin/products/images/delete", function (){

    User::verifyLogin();

    extract($_POST);

    try{

        Products::deleteImage($img_codigo);

        $imagens = Products::getImages($prd_codigo);

        $retorno['status'] = true;
        $retorno['imagens'] = $imagens;
        http_response_code(200);
    }catch(Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));
});

$app->post("/admin/products/images/create", function (){

    User::verifyLogin();

    extract($_POST);
    extract($_FILES);

    try{

        Products::saveImage($imagens, $prd_codigo);

        $imagens = Products::getImages($prd_codigo);

        $retorno['status'] = true;
        $retorno['imagens'] = $imagens;
        http_response_code(200);
    }catch(Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));
});

$app->get("/admin/products/images/create", function (){

    User::verifyLogin();

    extract($_POST);
    extract($_FILES);

    try{

        Products::saveImage($imagens, $prd_codigo);

        $imagens = Products::getImages($prd_codigo);

        $retorno['status'] = true;
        $retorno['imagens'] = $imagens;
        http_response_code(200);
    }catch(Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));
});

$app->get("/admin/products/{prd_codigo}/delete", function (Request $request, Response $response, array $args){

    User::verifyLogin();

    $prd_codigo = $args['prd_codigo'];

    Products::delete($prd_codigo);

    header("Location: /admin/products");
    exit();

});

$app->get('/login', function() {

    $page = new Page();

    $page->setTpl("login_user");
});

$app->get('/store[/{categoria}]', function (Request $request, Response $response, array $args){

    if(isset($args['categoria'])){
        $produtos = Products::getByCatDescricao($args['categoria']);
    }
    else{
        $produtos = Products::listAll();
    }

    $categorias = Categoria::listAll();
    
    $page = new Page();

    $page->setTpl("store", array("produtos" => $produtos, "categorias" => $categorias, "filtro" => $args['categoria'], "user" => $_SESSION['user']));

});

$app->post('/store', function(){

    extract($_POST);

    try{

        if(!isset($categorias)){
            $categorias = [];
        }

        $produtos = Products::listAll($categorias, $preco_min, $preco_max);

        $retorno['produtos'] = $produtos;
        http_response_code(200);
    }catch(Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

});

$app->get('/products/{prd_codigo}', function(Request $request, Response $response, array $args){

    try{

        $produto = Products::get($args['prd_codigo']);

        $retorno['produto'] = $produto;
        http_response_code(200);
    }catch(Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

});

$app->post('/store/buscar', function(){

    $produtos = Products::getByDescricao($_POST['filtro']);

    $categorias = Categoria::listAll();

    $page = new Page();

    $page->setTpl("store", array("produtos" => $produtos, "categorias" => $categorias, "user" => $_SESSION['user']));

});

$app->post('/login', function() {

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

$app->get('/logout', function (){

    User::logout();

    header("Location: /login");
    exit;
});

$app->post('/users/create', function (){

    try{

        $data = json_decode(file_get_contents('php://input'), true);

        User::save($data['ps_nome'], $data['user_senha'], "", "", $data['user_login'], $data['user_admin']);

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

$app->post('/addCart', function (){

    try{

        User::verifyLoginUser();

        $data = json_decode(file_get_contents('php://input'), true);

        Cart::add($data['prd_codigo']);

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

$app->get('/getCart', function() {

    try{

        $produtos = Cart::get();

        http_response_code(201);
        $retorno['produtos'] = $produtos;
        $retorno['status'] = true;
    }catch(\Exception $erro){
        http_response_code(400);
        $retorno['status'] = false;
        $retorno['msg'] = $erro->getMessage();
    }
    header("Content-Type: application/json");
    exit(json_encode($retorno));

});

$app->post('/removeCart', function (){

    try{

        User::verifyLoginUser();

        $data = json_decode(file_get_contents('php://input'), true);

        Cart::remove($data['carprd_codigo']);

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

$app->run();
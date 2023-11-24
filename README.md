Primeiramente instalar o XAMPP, e clonar o projeto dentro dele na pasta htdocs.
Para conseguir abrir o projeto no navegador com o endereço "clix.com/80" é necessário ir no disco local (C) na pasta Windows, system 32, drivers, etc e adicionar na pasta host a linha "127.0.0.1 clix.com". O outro arquivo necessário também no disco local (C), pasta xampp, apache, conf, extra, abrir httpd-vhost e adicionar:

    <VirtualHost *:80>
         DocumentRoot "C:\xampp\htdocs\clix"
         ServerName clix.com
    <\VirtualHost>

Agora no projeto, é necessário instalar o composer pelo navegador, e no terminal de qualquer pasta do projeto executar o seguinte comando: "composer", caso não haja resposta, reinicie o computador. Se tiver resposta execute o comando "composer update".
Para que o projeto funcione corretamente, crie um arquivo .env na raiz do projeto preenchendo as chaves que existem no arquivo .env.example.
Crie também na raiz do projeto uma pasta chamada views-cache, que será utilizada pelo raintTpl.

Nesse projeto foi utilizado o slim framework para criação das API e estruturação das rotas do projeto dentro do arquivo index.php, exemplo:

Definindo o framework;

    use Slim\Factory\AppFactory;

Criação da Rota;

    $app = AppFactory::create();

    $app->get('/', function() {
    
    $products = Products::listAll();
    
    $page = new Page();
    
    $page->setTpl("index", array("products" => $products));
});

<?php 
session_start();


require_once("vendor/autoload.php");
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

//Rota principal
$app->get('/', function() {
    
    $page = new Page();

    $page->setTpl("index");

});

//Rota do usuário Admin
$app->get('/admin', function() {
    	
	User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("index");

});

//Rota login do admin
$app->get('/admin/login', function() {
    
    $page = new PageAdmin([
    	"header"=>false,
    	"footer"=>false
    ]);

    $page->setTpl("login");

});

//Rota de Post de login admin
$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});

//Rota  get logout admin
$app->get('/admin/logout', function() {
    
    User::logout();

    header("Location: /admin/login");
    exit;
});

//rota de que lista todos os usúarios
$app->get('/admin/users', function(){
    
    User::verifyLogin();

    $users = User::listAll(); //listando todos os usuários

    $page = new PageAdmin();

    $page->setTpl("users", array(
    	"users"=>$users
    ));
 
});

//rota para criar usuários Admin
$app->get('/admin/users/create', function() {
    
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("users-create");
 
});

//Rota para deletar usuário Admin
$app->get('/admin/users/:iduser/delete', function($iduser){
	User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->delete();

    header("Location: /admin/users");
    exit;


});


//rota para editar usuários Admin
$app->get('/admin/users/:iduser', function($iduser) {
    
    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
 
});

//rota para salvar o usuário Admin
$app->post('/admin/users/create', function(){

	 User::verifyLogin();

	 $user = new User();

     //verifica se o inadmin foi marcado
     $_POST["inadmin"] = (isset($_POST["inadmin"])) ?1:0;


	 $user->setData($_POST);

	 $user->save();

     header("Location: /admin/users");
     exit;         

});
	

//rota para salvar a edição usuário Admin
$app->post('/admin/users/:iduser', function($iduser){
	User::verifyLogin();

    $user = new User();

     //verifica se o inadmin foi marcado
     $_POST["inadmin"] = (isset($_POST["inadmin"])) ?1:0;

    $user->get((int)$iduser);

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;
});


//método da rota do esqueci e-mail
$app->get('/admin/forgot', function(){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot");
});

//enviando o e-mail do esqueci senha
$app->post("/admin/forgot", function(){

   $user = User::getForgot($_POST["email"]);

   header("Location: /admin/forgot/sent");
   exit;

});

$app->get("/admin/forgot/sent", function(){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-sent");
});

$app->get("/admin/forgot/reset", function(){

    $user = User::validForgotDecrypt($_GET["code"]);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset", array(
        "name"=>$user["desperson"],
        "code"=>$_GET["code"]
    ));

});

$app->post("/admin/forgot/reset", function(){

    $forgot = User::validForgotDecrypt($_POST["code"]);

    User::setForgotUsed($forgot["idrecovery"]);

    $user = new User();

    $user->get((int)$forgot["iduser"]);

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
        "cost"=>12
    ]);


    $user->setPassword($password);

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-reset-success");
});

$app->run();

 ?>
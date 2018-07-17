<?php 
session_start();


require_once("vendor/autoload.php");
use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

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

//acendo a tela de envio de onde coloca o e-mail para o reset
$app->get("/admin/forgot/sent", function(){

    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);

    $page->setTpl("forgot-sent");
});


//acendo  a tela onde colocar a nova senha
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

//acendo o método que faz o reste da senha
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

//acessanndo a tela das categorias.
$app->get("/admin/categories", function(){
    User::verifyLogin();

    $categories = Category::listAll();

    $page = new PageAdmin();

    $page->setTpl("categories", array(
        "categories"=>$categories
    ));

});

//acessando a cadastrar categoria.
$app->get("/admin/categories/create", function(){
   
    User::verifyLogin();

    $page = new PageAdmin();

    $page->setTpl("categories-create");

});
//fazendo o cadastro de categoria
$app->post("/admin/categories/create", function(){

    User::verifyLogin();

    $category = new Category();

    $category->setData($_POST);

    $category->save();

    header("Location: /admin/categories");
    exit;

});

//rota pra deletar
$app->get("/admin/categories/:idcategory/delete", function($idcategory){
       
       User::verifyLogin();

        //instancia o objeto
        $category = new Category(); 
        //carrega o idcategory caso ainda tenha no banco
        $category->get((int)$idcategory);

        $category->delete();

        header("Location: /admin/categories");
        exit;

});

//rota get de edição de categoria, envia pra tela de edição
$app->get("/admin/categories/:idcategory", function($idcategory){
        
        User::verifyLogin();

        //instancia o objeto
        $page = new PageAdmin();

        $category = new Category();

        $category->get((int)$idcategory);

        $page->setTpl("categories-update", array(
        "category"=>$category->getValues()
        ));  

});

//rota post que faz a atualização no banco da categoria
$app->post("/admin/categories/:idcategory", function($idcategory){
        
        User::verifyLogin();

        //instancia o objeto
        $category = new Category();

        $category->get((int)$idcategory);

        $category->setData($_POST);

        $category->save();

        header("Location: /admin/categories");
        exit;
}); 


$app->get("/categories/:idcategory", function($idcategory){
    $category = new Category();

    $category->get((int)$idcategory);

    $page = new Page();

    $page->setTpl("category", [
        'category'=>$category->getValues(),
        'products'=>[]
    ]); 

});


$app->run();

 ?>
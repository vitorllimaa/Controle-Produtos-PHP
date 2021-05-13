<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \controle\pageadmin;
use \controle\login;
use \controle\model\User;
use \controle\model\product;
use \controle\model\estoque;

$app = new Slim();

$app->config('debug', true);

$app->get('/admin', function() {

	User::verifyLogin();
	$page = new pageadmin();
	$page->setTpl("index");
	
	
});

$app->get('/admin/login', function() {
    
	$page = new login([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login"); 
});
// post - envia para o banco
//get - envia para o html
$app->post('/admin/login', function(){

	User::login($_POST["login"], $_POST["password"]);
	$user = new User;
	header("Location: /admin");
	
	exit;
});

$app->get('/admin/logout', function(){

	User::logout();
	header("Location: /admin/login");
	exit;

});

$app->get('/admin/users', function(){
	
	User::verifyLogin();
	$users = User::listAll();
	$page = new pageadmin();
	$page->setTpl("users", array(
		"users"=>$users
	));

});

$app->get("/admin/users/create", function(){
	User::verifyLogin();
	$page = new pageadmin();
	$page->setTpl("users-create");

});

$app->get('/admin/users/:iduser/delete', function($id_login){
	User::verifyLogin();
	$user = new User;
	$user->delete($id_login);
    header("Location: /admin/users");
	exit;

});

$app->get('/admin/users/:iduser', function($id_login){
	User::verifyLogin();
    $user = new User;
	$user->get($id_login);
	$page = new pageadmin();
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});

$app->post("/admin/users/create", function(){
	User::verifyLogin();
	$user = new User;
	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
	exit;

	
});

$app->post("/admin/users/:iduser", function($id_login){
	User::verifyLogin();
	$user = new User;
	$user->get((int)$id_login);
	$user->setData($_POST);
	$user->update($id_login);
	header("Location: /admin/users");
	exit;
	
});

$app->get("/admin/forgot", function(){
	User::logout();
	$page = new pageadmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

$app->post("/admin/forgot", function(){
	User::logout();
	$user = User::getforgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;
});

 $app->get("/admin/forgot/sent", function(){
    User::logout();
	$page = new pageadmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-sent");

});

$app->get("/admin/forgot/reset", function(){
    User::logout();
	$user = User::validForgotDecrypt($_GET["code"]);
	$page = new pageadmin([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["nome"],
		"code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){
    User::logout();
	$user = User::validForgotDecrypt($_POST["code"]);
	User::setForgotUser($user["id_login"], $_POST["password"]);
	header("Location: /admin/login");
	exit;
});
//b2w stilo
$app->get("/admin/product", function(){
    
	User::verifyLogin();
	$page = new pageadmin();
	$pages = (isset($_GET['page'])) ? (int)$_GET['page']:1;
	$product = product::apiB2wStilo($pages);
	$pagess = [];
	for ($i = 1; $i<= 59; $i++){
		array_push($pagess, [
			'link'=>'/admin/product'.'?page='.$i,
			'page'=>$i
		]);
	}
	$date = date('d/m/y H:i:s');
	$page->setTpl("product_b2w_stilo",[
		'page1'=>$product[0],
		'pages'=>$pagess,
		'datenow'=>$date

	]);

});
//b2w click
$app->get("/admin/product/b2wclick", function(){
    
	User::verifyLogin();
	$page = new pageadmin();
	$pages = (isset($_GET['page'])) ? (int)$_GET['page']:1;
	$product = product::apiB2wClick($pages);
	$pagess = [];
	for ($i = 1; $i<= 59; $i++){
		array_push($pagess, [
			'link'=>'/admin/product/b2wclick'.'?page='.$i,
			'page'=>$i
		]);
	}
	$date = date('d/m/y H:i:s');
	$page->setTpl("product_b2w_click",[
		'page1'=>$product[0],
		'pages'=>$pagess,
		'datenow'=>$date

	]);

});

$app->get("/admin/product/magalustilo", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pg = (isset($_GET['page'])) ? (int)$_GET['page']:1;
	$pages = product::apimagalustilo($pg);
	$pagess = [];
	for ($i = 1; $i<= 59; $i++){
		array_push($pagess, [
			'link'=>'/admin/product/magalustilo'.'?page='.$i,
			'page'=>$i
		]);
	}
	$date = date('d/m/y H:i:s');
	$page->setTpl("product_magalu_stilo",[
		'page1'=>$pages,
		'page2'=>$pages,
		'datenow'=>$date,
		'pages'=>$pagess,
	]);


});

$app->get("/admin/product/magaluclick", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pg = (isset($_GET['page'])) ? (int)$_GET['page']:1;
	$pages = product::apimagaluclick($pg);
	$pagess = [];
	for ($i = 1; $i<= 59; $i++){
		array_push($pagess, [
			'link'=>'/admin/product/magaluclick'.'?page='.$i,
			'page'=>$i
		]);
	}
	$date = date('d/m/y H:i:s');
	$page->setTpl("product_magalu_click",[
		'page1'=>$pages,
		'page2'=>$pages,
		'datenow'=>$date,
		'pages'=>$pagess,
	]);


});

$app->get("/admin/product/mlclick/importar", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	/* $pages = product::apimlclick(); */

	$page->setTpl("product_ml_click_importar",[
		/* 'page1'=>$pages, */
		
		
	]);
});

$app->post("/admin/product/mlclick/importar", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pages = product::uploadMlclick($_FILES['arquivo']);
	header("Location: /admin/product/mlclick");
	exit;
});

$app->get("/admin/product/mlclick", function(){
	
	User::verifyLogin();
	$page = new pageadmin();

	$page->setTpl("product_ml_click");
});

$app->post("/admin/product/mlclick", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	
	header("Location: /admin/product/mlclick");
	exit;
	
});

$app->get("/admin/product/mlclick/update", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pages = product::updateMlclick();

	$page->setTpl("product_ml_click_update");
});

$app->get("/admin/product/b2wstilo/update", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$product = product::updateb2wStilo();
	$page->setTpl("product_b2w_atualizado" );
});

$app->get("/admin/product/b2wclick/update", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$product = product::updateb2wClick();
	$page->setTpl("product_b2w_atualizado");
});

$app->get("/admin/product/magalustilo/update", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pages = product::updatemagalustilo();
	$page->setTpl("product_magalu_atualizado");


});

$app->get("/admin/product/magaluclick/update", function(){
	
	User::verifyLogin();
	$page = new pageadmin();

	$pages = product::updatemagaluclick();
	$page->setTpl("product_magalu_atualizado");


});

$app->get("/admin/estoquestilo", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$page->setTpl("estoque_aton_importar_stilo");

});

$app->post("/admin/estoquestilo", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pages = estoque::uploadEstoqueStilo($_FILES['arquivo']);
	header("Location: /admin/estoquestilo");
	exit;
});

$app->get("/admin/estoqueclick", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$page->setTpl("estoque_aton_importar_click");

});

$app->post("/admin/estoqueclick", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pages = estoque::uploadEstoqueClick($_FILES['arquivo']);
	header("Location: /admin/estoqueclick");
	exit;
});


$app->get("/admin/estoque/b2wstilo", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$pages = estoque::estoqueb2wstilo($pg);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/estoque/b2wstilo'.'?page='.$i,
			'page'=>$i
		]);
	}
	$page->setTpl("estoque_b2w_stilo",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas
	]);

});

$app->get("/admin/estoque/b2wclick", function(){
	
	User::verifyLogin();
	$page = new pageadmin();
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$pages = estoque::estoqueb2wclick($pg);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/estoque/b2wclick'.'?page='.$i,
			'page'=>$i
		]);
	}
	$page->setTpl("estoque_b2w_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas
	]);

});

$app->get("/admin/estoque/b2wstilo/gerar_planilha_b2w_stilo", function(){
	
	User::verifyLogin();
	$excel = estoque::gerarPlanilhab2wstilo();

});

$app->get("/admin/estoque/b2wstilo/gerar_planilha_b2w_click", function(){
	
	User::verifyLogin();
	$excel = estoque::gerarPlanilhab2wclick();

});

$app->run();

 ?>
<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \controle\pageadmin;
use \controle\login;
use controle\model\Dashboard;
use \controle\model\User;
use \controle\model\product;
use \controle\model\estoque;
use \controle\model\Preco;

$app = new Slim();
$value = [];
$app->config('debug', true);

$app->get('/admin', function() {

	User::verifyLogin();
	$User = new User();
	$dash = new Dashboard();
	/* $t = $dash->getAllmlClickDash(); */
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$value1 = $dash->getB2wStiloDash();
	$page->setTpl("index",[
		"Name"=>$User->getName(),
		"b2wStilo"=>$dash->getB2wStiloDash()[0]["count(*)"],
		"b2wStilot"=>$dash->getAllB2wStiloDash()[0]["count(sku)"],
		"b2wClick"=>$dash->getB2wClickDash()[0]["count(*)"],
		"b2wClickt"=>$dash->getAllB2wClickDash()[0]["count(sku)"],
		"MagaluStilo"=>$dash->getMagaluStiloDash()[0]["count(*)"],
		"MagaluStilot"=>$dash->getAllMagaluStiloDash()[0]["count(sku)"],
		"MagaluClick"=>$dash->getMagaluClickDash()[0]["count(*)"],
		"MagaluClickt"=>$dash->getAllMagaluClickDash()[0]["count(sku)"],
		"mlClick"=>$dash->getmlClickDash()[0]["count(*)"],
		"mlClickt"=>$dash->getAllmlClickDash()[0]["count(mlb)"]
	]);
	
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
	$User = new User();
    $teste = $User->login($_POST["login"], $_POST["password"]);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$page->setTpl("users", array(
		"users"=>$users
	));

});

$app->get("/admin/users/create", function(){
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
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
	$uses = $user->update($id_login, $_POST);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
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
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$page->setTpl("product_ml_click_importar");
});

$app->post("/admin/product/mlclick/importar", function(){
	
	User::verifyLogin();
	$User = new User();
	$product = new product();
	$product->uploadMlclick($_FILES['arquivo']);
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	header("Location: /admin/product/mlclick");
	exit;
});

$app->get("/admin/product/mlclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);

	$page->setTpl("product_ml_click");
});

$app->post("/admin/product/mlclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	
	header("Location: /admin/product/mlclick");
	exit;
	
});

$app->get("/admin/product/mlclick/update", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pages = product::updateMlclick();

	$page->setTpl("product_ml_click_update");
});

$app->get("/admin/product/b2wstilo/update", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$product = product::updateb2wStilo();
	$page->setTpl("product_b2w_atualizado" );
});

$app->get("/admin/product/b2wclick/update", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$product = product::updateb2wClick();
	$page->setTpl("product_b2w_atualizado");
});

$app->get("/admin/product/magalustilo/update", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pages = product::updatemagalustilo();
	$page->setTpl("product_magalu_atualizado");


});

$app->get("/admin/product/magaluclick/update", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);

	$pages = product::updatemagaluclick();
	$page->setTpl("product_magalu_atualizado");


});

$app->get("/admin/estoquestilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$page->setTpl("estoque_aton_importar_stilo");

});

$app->post("/admin/estoquestilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pages = estoque::uploadEstoqueStilo($_FILES['arquivo']);
	
	header("Location: /admin/estoquestilo");
	exit;
});

$app->get("/admin/estoqueclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$page->setTpl("estoque_aton_importar_click");

});

$app->post("/admin/estoqueclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pages = estoque::uploadEstoqueClick($_FILES['arquivo']);
	header("Location: /admin/estoqueclick");
	exit;
});

$app->get("/admin/estoque/b2wstilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = estoque::estoqueB2wstilo($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/estoque/b2wstilo?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("estoque_b2w_stilo",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/estoque/b2wclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = estoque::estoqueB2wclick($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/estoque/b2wclick?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("estoque_b2w_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
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

$app->get("/admin/estoque/magalustilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = estoque::estoqueMagalustilo($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/estoque/magalustilo?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
    
	$page->setTpl("estoque_magalu_stilo",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/estoque/magaluclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = estoque::estoqueMagaluclick($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/estoque/magaluclick?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("estoque_magalu_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/estoque/magalustilo/gerar_planilha_magalu_stilo", function(){
	
	User::verifyLogin();
	$excel = estoque::gerarPlanilhaMaglustilo();

});

$app->get("/admin/estoque/magalustilo/gerar_planilha_magalu_click", function(){
	
	User::verifyLogin();
	$excel = estoque::gerarPlanilhaMagluclick();

});

$app->get("/admin/estoque/mlclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = estoque::estoqueMlClickFilter($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/preco/mlclick?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("estoque_ml_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/estoque/mlclick/gerar_planilha_ml_click", function(){
	
	User::verifyLogin();
	$excel = estoque::gerarPlanilhaMlclick();

});

$app->get("/admin/precostilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$page->setTpl("preco_aton_importar_stilo");

});

$app->post("/admin/precostilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pages = Preco::uploadPrecoStilo($_FILES['arquivo']);
	
	header("Location: /admin/precostilo");
	exit;
});

$app->get("/admin/precoclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$page->setTpl("preco_aton_importar_click");

});

$app->post("/admin/precoclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pages = Preco::uploadPrecoClick($_FILES['arquivo']);
	
	header("Location: /admin/precoclick");
	exit;
});

$app->get("/admin/preco/b2wstilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = Preco::precob2wstilo($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/preco/b2wstilo?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("preco_b2w_stilo",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/preco/b2wclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = Preco::precob2wclick($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/preco/b2wclick	?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}

	$page->setTpl("preco_b2w_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/preco/b2wstilo/gerar_planilha_b2w_stilo", function(){
	
	User::verifyLogin();
	$excel = Preco::gerarPlanilhaPrecob2wstilo();

});

$app->get("/admin/preco/b2wclick/gerar_planilha_b2w_click", function(){
	
	User::verifyLogin();
	$excel = Preco::gerarPlanilhaPrecob2wclick();

});

$app->get("/admin/preco/magalustilo", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = Preco::precoMagalustilo($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/preco/magalustilo?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("preco_magalu_stilo",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/preco/magaluclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = Preco::precoMagaluclick($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/preco/magaluclick?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("preco_magalu_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		'search'=>$search
	]);

});

$app->get("/admin/preco/magalustilo/gerar_planilha_magalu_stilo", function(){
	
	User::verifyLogin();
	$excel = Preco::gerarPlanilhaPrecoMaglustilo();

});

$app->get("/admin/preco/magalustilo/gerar_planilha_magalu_click", function(){
	
	User::verifyLogin();
	$excel = Preco::gerarPlanilhaPrecoMagluclick();

});

$app->get("/admin/preco/mlclick", function(){
	
	User::verifyLogin();
	$User = new User();
	$pg = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$pages = preco::precoMlClickFilter($pg, $search);
    $paginas = [];
	for($i=1; $i<$pages['pages']; $i++){
		array_push($paginas,  [
			'link'=>'/admin/preco/mlclick?'.http_build_query([
				'page'=>$i,
				'search'=>$search
			]),
			'text'=>$i
		]);
	}
	$page->setTpl("preco_ml_click",[
		'page'=>$pages['data'],
		'pages'=>$pages['total'],
		'pg'=>$paginas,
		"search"=>$search
	]);

});

$app->post("/admin/preco/mlclick", function(){
	
	User::verifyLogin();
	$User = new User();
	var_dump($_POST);
	exit;
	$page = new pageadmin([
		"header"=>false,
	]);
	$page->setTpl("header",[
		"Name"=>$User->getName()
	]);
	header("Location: /admin/precostilo");
	exit;

});

$app->get("/admin/preco/mlclick/gerar_planilha_ml_click", function(){
	
	User::verifyLogin();
	$excel = preco::gerarPlanilhaPrecoMlclick();

});

$app->run();

 ?>
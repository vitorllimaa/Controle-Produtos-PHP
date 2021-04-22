<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$sql = new controle\DB\Sql();
	$result = $sql->select("SELECT * FROM tb_login");
	echo json_encode($result);


});

$app->run();

 ?>
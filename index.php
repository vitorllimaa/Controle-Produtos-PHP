<?php 
require_once("vendor/autoload.php");

use \Slim\Slim;
use \controle\page;
$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new page();

	$page->setTpl("index"); 


});

$app->run();

 ?>
<?php
error_reporting(E_ERROR | E_PARSE);
require 'bootstrap.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use lithium\net\http\Router;
use lithium\action\Request as Li3Request;


$c = new Pimple();

$c['connection'] = $c->share(function(){
	$dsn = 'sqlite:' . __DIR__ . '/data/database.sqlite';
	return new PDO($dsn);
});


function homepage(Request $request){
	$content = '<h1>Welcome to PHP Santa</h1>';
	$content .= '<a href="/letters">Read the letters</a>';
	if ($name = $request->query->get('name')) {
		echo sprintf('<p>Oh, and hello %s!</p>', $name);
	}
	
	return new Response($content);
}

function letters(Request $request, Pimple $c)
{

	$dbh = $c['connection'] ;
	
	$sql = 'SELECT * FROM php_santa_letters';
	$content =  '<h1>Read the letters to PHP Santa</h1>';
	$content .=  '<ul>';
	foreach ($dbh->query($sql) as $row) {
		$content .=  sprintf('<li>%s - dated %s</li>', $row['content'], $row['received_at']);
	}
	$content .=  '</ul>';
	
	return new Response($content);
}

function error404(Request $request)
{
	$content =  '<h1>404 Page not Found</h1>';
	$content .=  '<p>This is most certainly *not* an xmas miracle</p>';

	$response = new Response($content);
	$response->setStatusCode(404);
	return $response;
}


$request = Request::createFromGlobals();
$li3Request = new Li3Request();


$li3Request->url = $request->getPathInfo();

//create a router, build the routes, and then execute it
$router = new Router();
$router->connect('/letters', array('controller'=>'letters'));
$router->connect('/', array('controller'=>'homepage'));
$router->parse($li3Request);


if (isset($li3Request->params['controller'])){
	$controller = $li3Request->params['controller'];
} else{
	$controller = 'error404';
}


$response = call_user_func_array($controller, array($request, $c));
if(!$response instanceof Response){
	throw new Exception(sprintf('Controller "%s" didn\'t return a response', $controller));
}

$response->send();


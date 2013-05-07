<?php
error_reporting(E_ERROR | E_PARSE);
require 'bootstrap.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use lithium\net\http\Router;
use lithium\action\Request as Li3Request;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;


$c = new Pimple();
$c['dsn'] = 'sqlite:' . __DIR__ . '/data/database.sqlite'; 

$c['connection'] = $c->share(function(Pimple $c){
	return new PDO($c['dsn']);
});

$c['request'] = $c->share(function(){
    return Request::createFromGlobals();
});

$c['li3_request'] = $c->share( function(Pimple $c){
    $li3Request = new Li3Request();
    $li3Request->url = $c['request']->getPathInfo();
    return $li3Request;
} );

//create teh logger
$c['log_path'] = __DIR__ . '/data/web.log';
$c['logger_writer'] = $c->share(function(Pimple $c){
    return new Stream($c['log_path']);
});
$c['logger'] = $c->share(function(Pimple $c){
	$logger =  new Logger();
	$logger->addWriter( $c['logger_writer']);
    return $logger;
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

function error404(Request $request, Pimple $c)
{
	$c['logger']->log(Logger::ERR, '404 for '. $request->getPathInfo());
	
	$content =  '<h1>404 Page not Found</h1>';
	$content .=  '<p>This is most certainly *not* an xmas miracle</p>';

	$response = new Response($content);
	$response->setStatusCode(404);
	return $response;
}

//create a router, build the routes, and then execute it
$c['router'] = $c->share(function(){
    return  new Router();
});
$c['router']->connect('/letters', array('controller'=>'letters'));
$c['router']->connect('/', array('controller'=>'homepage'));


$response = _run_application($c);

$response->send();


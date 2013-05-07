<?php 

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zend\Log\Logger;

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

	$dbh = $c['connection'];
	
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

<?php 

require __DIR__ . '/vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__ . '/vendors/Pimple/lib/Pimple.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', __DIR__ . '/vendors');
$loader->registerNamespace('lithium', __DIR__ . '/vendors');
$loader->registerNamespace('Zend', __DIR__ . '/vendors/zf2/library');
$loader->register();

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

//create a router, build the routes, and then execute it
$c['router'] = $c->share(function(){
    return  new Router();
});
$c['router']->connect('/letters', array('controller'=>'letters'));
$c['router']->connect('/', array('controller'=>'homepage'));

function _run_application(Pimple $c)
{
    $c['router']->parse($c['li3_request']);
    $c['request']->attributes->add($c['li3_request']->params);
    $controller = $c['request']->attributes->get('controller', 'error404');
    
    
    return call_user_func_array($controller, array($c['request'], $c));
}

return $c;
<?php 

require __DIR__ . '/vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__ . '/vendors/Pimple/lib/Pimple.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', __DIR__ . '/vendors');
$loader->registerNamespace('lithium', __DIR__ . '/vendors');
$loader->registerNamespace('Zend', __DIR__ . '/vendors/zf2/library');

$loader->register();

function _run_application(Pimple $c)
{
    $c['router']->parse($c['li3_request']);
    $c['request']->attributes->add($c['li3_request']->params);
    $controller = $c['request']->attributes->get('controller', 'error404');
    
    
    return call_user_func_array($controller, array($c['request'], $c));
}
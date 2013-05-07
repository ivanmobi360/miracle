<?php 

require __DIR__ . '/vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__ . '/vendors/Pimple/lib/Pimple.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', __DIR__ . '/vendors');
$loader->registerNamespace('lithium', __DIR__ . '/vendors');
$loader->registerNamespace('Zend', __DIR__ . '/vendors/zf2/library');

$loader->register();
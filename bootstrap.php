<?php 

require __DIR__ . '/vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace(
		'Symfony', __DIR__ . '/vendors'
		);
$loader->register();
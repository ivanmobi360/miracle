<?php
require 'bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$request = Request::createFromGlobals();


try {
    $dbPath = __DIR__.'/data/database.sqlite';
    $dbh = new PDO('sqlite:'.$dbPath);
} catch(PDOException $e) {
    die('Panic! '.$e->getMessage());
}

$uri = $request->getPathInfo();

if ($uri == '/' || $uri == '') {

    echo '<h1>Welcome to PHP Santa</h1>';
    echo '<a href="/letters">Read the letters</a>';
    if ($name = $request->query->get('name')) {
        echo sprintf('<p>Oh, and hello %s!</p>', $name);
    }

} elseif ($uri == '/letters') {

    $sql = 'SELECT * FROM php_santa_letters';
    echo '<h1>Read the letters to PHP Santa</h1>';
    echo '<ul>';
    foreach ($dbh->query($sql) as $row) {
        echo sprintf('<li>%s - dated %s</li>', $row['content'], $row['received_at']);
    }
    echo '</ul>';

} else {
    $content =  '<h1>404 Page not Found</h1>';
    $content .=  '<p>This is most certainly *not* an xmas miracle</p>';
    
    $response = new Response($content);
    $response->setStatusCode(404);
    $response->send();
}
<?php
error_reporting(E_ERROR | E_PARSE);
$c = require 'bootstrap.php';
require 'controllers.php';
require 'routing.php';

$response = _run_application($c);
$response->send();


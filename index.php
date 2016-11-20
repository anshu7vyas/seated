<?php

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

require 'routes/static-routes.php';
require 'routes/api-routes.php';

$app->run();

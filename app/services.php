<?php

use SaS\ServiceProvider\APIServiceProvider;
use SaS\ServiceProvider\LoggerProvider;
use SaS\ServiceProvider\DoctrineProvider;

$app->register(new LoggerProvider());
$app->register(new APIServiceProvider());
$app->register(new DoctrineProvider())



?>

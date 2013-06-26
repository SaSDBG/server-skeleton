<?php

use SaS\ServiceProvider\APIServiceProvider;
use SaS\ServiceProvider\LoggerServiceProvider;
use SaS\ServiceProvider\DoctrineProvider;

$app->register(new LoggerServiceProvider());
$app->register(new APIServiceProvider());
$app->register(new DoctrineProvider())



?>

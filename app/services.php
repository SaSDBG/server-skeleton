<?php

use SaS\ServiceProvider\APIServiceProvider;
use SaS\ServiceProvider\LoggerServiceProvider;

$app->register(new LoggerServiceProvider());
$app->register(new APIServiceProvider());



?>

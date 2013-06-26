<?php

use Silex\Application;

require(__DIR__.'/../vendor/autoload.php');

$app = new Application;

require_once('configure_app.php');

return $app;

?>

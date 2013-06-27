<?php

use Silex\Application;
define('APP_BASE_DIR', __DIR__.'/../');

require(APP_BASE_DIR.'vendor/autoload.php');

$app = new Application;

require_once(APP_BASE_DIR.'app/configure_app.php');

return $app;

?>

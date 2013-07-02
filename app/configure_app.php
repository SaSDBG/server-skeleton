<?php

namespace SaS;

include_conf($app);

require 'services.php';

$app['api.controllers'] = require('controllers.php');

function include_conf(\Silex\Application $app) {
    $conf = $app;
    require APP_BASE_DIR.'config/domain.conf.php';
    require APP_BASE_DIR.'config/app.conf.php';
}

?>

<?php

//put app specific configuration here

$conf['debug'] = false;

$conf['token.file'] = '';

$conf['security.secret'] = ''; //use a randomly generated secret here

$conf['monolog.logfile'] = '../log/app.log';

$conf['security.authenticator'] = function() use ($conf) {
    return $conf['security.authenticator.null']; //preconfigured to NullAuthenticator
};

?>

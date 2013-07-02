<?php

//put app specific configuration here

$conf['debug'] = false;

$conf['token.token_file'] = '';

$conf['security.secret'] = ''; //use a randomly generated secret here

$conf['monolog.logfile'] = '../log/app.log';

$conf['security.authenticator'] = function() use ($conf) {
    return $conf['security.authenticator.null']; //preconfigured to NullAuthenticator
};

$conf['db.params'] = [
    'dbname' => 'sas',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

?>

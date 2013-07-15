<?php

//put app specific configuration here

$conf['debug'] = true;

$conf['token.token_file'] = __DIR__.'/tokens.php';

$conf['security.secret'] = 'sdfsdfwerwerwfsdgfrgg'; //use a randomly generated secret here

$conf['monolog.logfile'] = __DIR__.'/../log/app.log';

$conf['security.authenticator'] = function() use ($conf) {
    return $conf['security.authenticator.null']; //preconfigured to NullAuthenticator
};

$conf['db.params'] = [
    'dbname' => 'sas-test',
    'user' => 'root',
    'password' => 'foobar',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
];

?>

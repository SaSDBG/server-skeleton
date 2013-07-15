#!/usr/bin/env php
<?php
require_once __DIR__.'/../vendor/autoload.php';
echo password_hash($argv[1], PASSWORD_DEFAULT).PHP_EOL;
?>

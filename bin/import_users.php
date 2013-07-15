<?php
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../app/app.php';
$passes = [];
$h = fopen(__DIR__.'/passes.csv', "w");
while($user = fgetcsv(STDIN, ";")) {
  $id = $user[0];
  $lastName = $user[1];
  $firstName = $user[2];
  $class = $user[3];
  $companyID = isset($user[4]) ? $user[4] : NULL;

  $pass = create_pass();
  
  $passes = [$lastName, $firstName, $class, $pass];

  $roles = $companyID !== NULL ? 'betriebsleiter' : '';
  $isChief = $companyID == NULL ? false : true;

  $chiefOf_ID = $companyID; 
	
  insert_user($id, $firstName, $lastName, password_hash($pass, PASSWORD_DEFAULT),  $roles, $isChief, $chiefOf_ID, $app['db.connection']);
  fputcsv($h, $passes, ";");
}

function create_pass($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}


function insert_user($id, $firstName, $lastName, $pass, $roles, $isChief, $chiefOf_ID, $c) {
  $c->insert('users', [
	'id' => $id,
	'firstName' => $firstName,
	'lastName' => $lastName,
	'pass' => $pass,
	'roles' => $roles,
	'isChief' => $isChief,
	'chiefOf_ID' => $chiefOf_ID,
  ]);
}

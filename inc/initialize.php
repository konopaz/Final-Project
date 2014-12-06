<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
define('DB_HOST', 'oniddb.cws.oregonstate.edu');
define('DB_NAME', 'konopaz-db');
define('DB_USER', 'konopaz-db');
define('DB_PASS', 'O3NmYoML07wkzwGD');
define('DB_PREF', 'cs494_');

try {

  $pdo = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
}
catch(PDOException $e) {

  header('HTTP/1.1 500 Internal Server Error');
  header('Content-Type: application/json');

  $error = new stdClass;
  $error->error = 'Connect Error:'. $e->getMessage();
  echo json_encode($error);
  exit;
}

session_start();

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/lib');

function h($v) { 
  return htmlspecialchars($v); 
}
function x($v) { 
  return str_replace(array('&', '<', '>', '"', "'"), array('&amp;', '&gt;', '&lt;', '&quot;'), $v); 
}
function pr($v) {

  echo "<pre>\n";
  print_r($v);
  echo "</pre>\n";
}

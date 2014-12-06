<?php
require_once 'inc/initialize.php';

if(!$_SESSION['userId']) {

  header('HTTP/1.1 403 Forbidden');
  exit;
}

require_once 'MoviesDAO.php';

list($table, $id) =  explode('/', substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']) + 1));

switch($table) {

  case 'movies':
    $dao = new MoviesDAO($pdo, $_SESSION['userId'], DB_PREF);
    break;

  default:
    throw new Exception("Unknow entity: ".$table);
}

switch($_SERVER['REQUEST_METHOD']) {

  case 'GET':

    if($id) {

      $data = $dao->find($id);
    }
    else {

      $data = $dao->findAll();
    }
    break;

  case 'POST':

    $data = $dao->find($id);
    if(!$data) {
      $data = $dao->create();
    }

    $data->assignFrom($_POST);
    $dao->save($data);
    break;

  case 'DELETE':

    $dao->delete($id);
    break;

  default:
    throw new Exception("Unsupported method: ".$_SERVER['REQUEST_METHOD']);
}

header('Content-Type: application/json');
echo json_encode($data);
//list($table, $id) = explode('/', substr($_SERVER['REQUEST_URI']));
//echo $table;
//print_r($moviesDao->find(2));
//print_r($moviesDao->findAll());

//$movie = new Movie();
//$movie->setTitle("Test Movie 2");
//$moviesDao->save($movie);

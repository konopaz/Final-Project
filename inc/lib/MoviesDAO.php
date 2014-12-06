<?php
require_once 'DAO.php';
require_once 'Movie.php';

class MoviesDAO implements DAO {

  private $_dbConn;
  private $_table;
  private $_userId;

  private $_insertStmt;
  private $_updateStmt;
  private $_selectStmt;
  private $_selectAllStmt;
  private $_deleteStmt;

  public function __construct(PDO $dbConn, $userId, $prefix = '') {
    $this->_dbConn = $dbConn;
    $this->_table = $prefix . 'movies';
    $this->_userId = $userId;
  }

  public function delete($id) {

    $delete = $this->prepareDelete();
    $delete->bindParam('id', $id);
    $delete->execute();
  }

  public function create() {

    return new Movie();
  }

  public function findAll() {

    $data = array();

    $select = $this->prepareSelectAll();

    if($select->execute()) {

      while($row = $select->fetch()) {

        $movie = new Movie();
        $movie->assignFrom($row);
        $data[] = $movie;
      }
    }

    return $data;
  }

  private function prepareSelectAll() {

    if($this->_selectAllStmt == null) {

      $sql = 
        'SELECT '.
          '* '.
        'FROM '.
          $this->_table.' '.
        'WHERE '.
          'user_id = :userId';

      $this->_selectAllStmt = $this->_dbConn->prepare($sql);
      $this->_selectAllStmt->bindParam(':userId', $this->_userId);
    }

    return $this->_selectAllStmt;
  }

  public function find($id) {

    $select = $this->prepareSelect();
    $select->bindParam('id', $id);

    if($select->execute() && ($row = $select->fetch(PDO::FETCH_ASSOC))) {

      $movie = new Movie();
      $movie->assignFrom($row);

      return $movie;
    }

    return null;
  }

  private function prepareDelete() {

    if($this->_deleteStmt == null) {

      $sql = 
        'DELETE FROM '.
          $this->_table.' '.
        'WHERE '.
          'id = :id '.
          'AND user_id = :user_id';
      $this->_deleteStmt = $this->_dbConn->prepare($sql);
      $this->_deleteStmt->bindParam(':user_id', $this->_userId);
    }

    return $this->_deleteStmt;
  }

  private function prepareSelect() {

    if($this->_selectStmt == null) {

      $sql = 
        'SELECT '.
          '* '.
        'FROM '.
          $this->_table.' '.
        'WHERE '.
          'id = :id '.
          'AND user_id = :user_id';
      $this->_selectStmt = $this->_dbConn->prepare($sql);
      $this->_selectStmt->bindParam(':user_id', $this->_userId);
    }

    return $this->_selectStmt;
  }

  public function save($movie) {

    if($movie->id) {

      $this->update($movie);
    }
    else {

      $this->insert($movie);
    }
  }

  private function insert(Movie $movie) {

    $title = $movie->title;

    $insert = $this->prepareInsert();
    $insert->bindParam(':title', $title);
    $insert->execute();

    $movie->id = $this->_dbConn->lastInsertId();
  }
  
  private function prepareInsert() {

    if($this->_insertStmt == null) {

      $sql = 
        'INSERT INTO '.$this->_table.' ('.
          'title, '.
          'user_id, '.
          'created, '.
          'modified '.
        ') VALUES ('.
          ':title, '.
          ':user_id, '.
          'NOW(), '.
          'NOW() '.
        ')';

      $this->_insertStmt = $this->_dbConn->prepare($sql);
      $this->_insertStmt->bindParam(':user_id', $this->_userId);
    }

    return $this->_insertStmt;
  }

  private function update(Movie $movie) {

    $title = $movie->title;
    $id = $movie->id;

    $update = $this->prepareUpdate();
    $update->bindParam(':title', $title);
    $update->bindParam(':id', $id);
    $update->execute();
  }

  private function prepareUpdate() {

    if($this->_updateStmt == null) {

      $sql = 
        'UPDATE '.$this->_table.' SET '.
          'title = :title, '.
          'modified = NOW() '.
        'WHERE '.
          'id = :id '.
          'AND user_id = :user_id';

      $this->_updateStmt = $this->_dbConn->prepare($sql);
      $this->_updateStmt->bindParam(':user_id', $this->_userId);
    }

    return $this->_updateStmt;
  }
}

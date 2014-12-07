<?php
require_once 'Bean.php';

class Movie extends Bean {

  public $id;
  public $title;
  public $released;

  public function __get($name) {

    if($name == 'released') {

      return $this->released;
    }
  }

  public function __set($name, $value) {

    if($name == 'released') {

      $this->setReleased($value);
    }
  }

  public function setReleased($v) {

    $this->released = null;

    if(strlen($v) > 0) {

      $tmp_ts = strtotime($v);

      if($tmp_ts) {

        $this->released = date('Y-m-d', $tmp_ts);
      }
    }
  }
}

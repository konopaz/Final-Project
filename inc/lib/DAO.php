<?php
interface DAO {
  public function create();
  public function save($bean);
  public function find($id);
  public function findAll();
}

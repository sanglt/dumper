<?php

abstract class Dumper_Content_Base_Controller implements Dumper_Content_Interface {
  public $school;
  public $entity;
  public $storage;

  public function __construct($school, $entity_type, $entity = NULL, $storage = NULL) {
    $this->school = $school;
    $this->entity = $entity;
    if (!$storage) {
      $this->storage = $storage;
    }
    else {
      $path  = $this->school>identifier() . '/';
      $path .= $this->entity->entityType() . '/';
      $path .= $this->entity->identifier();
      $this->storage = new DumperFileStorage($path);
    }
  }

  public function process() {
    try {
      return $this->_process();
    }
    catch (Exception $e) {
      $this->rollback();
      return FALSE;
    }
  }

  public function _process() {
    if ($data = $this->getData()) {
      if ($this->storage->write($data)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  public function rollback() {
    $this->storage->delete();
  }
}

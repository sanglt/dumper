<?php

abstract class Dumper_Content_Base_Controller implements Dumper_Content_Interface {
  /**
   * OG node object.
   *
   * @var stdClass
   */
  public $school;

  /**
   *
   * @var Entity
   */
  public $entity;

  /**
   *
   * @var type
   */
  public $storage;

  /**
   * Entity type.
   *
   * @var string
   */
  public $entity_type;

  public function __construct($school, $entity_type, $entity = NULL, $storage = NULL) {
    $this->school = $school;

    if (!is_null($entity)) {
      $this->entity = $entity;
    }

    if (!$storage) {
      $this->storage = $storage;
    }
    else {
      $path  = $this->school>identifier() . '/';
      $path .= $this->entity->entityType() . '/';
      $path .= $this->entity->identifier();
      $this->storage = new Dumper_Controller_Filestorage($path);
    }
  }

  /**
   * Processs an entity.
   *
   * @return boolean
   */
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

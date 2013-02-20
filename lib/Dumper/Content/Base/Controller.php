<?php

class Dumper_Content_Base_Controller implements Dumper_Content_Base_Interface {
  /**
   * OG node object.
   *
   * @var stdClass
   */
  public $og;

  /**
   *
   * @var Entity
   */
  public $entity;

  public $queue_table = 'dumper_content_queue';

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

  public function __construct($og, $entity_type, $entity = NULL, $storage = NULL) {
    $this->og = $og;
    $this->entity_type = $entity_type;

    if (!is_null($entity)) {
      $this->entity = $entity;
    }

    if (!$storage) {
      $this->storage = $storage;
    }
    else {
      $path  = $this->og->identifier() . '/';
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

  public function processQueuedItems() {
  }

  public function processQueuedItem($entity_id) {
  }

  public function queueItems() {
  }

  public function queueItem($entity_id) {
  }

  public function setEntity($entity) {
    $this->entity = $entity;
  }
}

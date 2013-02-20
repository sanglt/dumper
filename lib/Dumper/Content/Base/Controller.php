<?php

class Dumper_Content_Base_Controller implements Dumper_Content_Base_Interface {
  /**
   * OG node object.
   *
   * @var Dumper_Controller_Og
   */
  public $og_controller;

  /**
   *
   * @var Entity
   */
  public $entity;

  public $queue_table = 'dumper_content_queue';

  /**
   * Storage controller.
   *
   * @var Dumper_Controller_Filestorage
   */
  public $storage;

  /**
   * Entity type.
   *
   * @var string
   */
  public $entity_type;

  public function __construct($og_controller, $entity_type, $entity = NULL, $storage = NULL) {
    $this->og_controller = $og_controller;
    $this->entity_type = $entity_type;

    if (!is_null($entity)) {
      $this->setEntity($entity);
    }

    $this->setStorage($storage);
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

  public function processQueuedItem(Dumper_Data_QueueItem $queue_item) {
  }

  public function queueItems() {
  }

  public function queueItem($entity_id) {
  }

  public function setEntity($entity) {
    $this->entity = $entity;
  }

  public function setStorage($storage = NULL) {
    if (!$storage) {
      $storage = new Dumper_Controller_Filestorage();
    }
    $this->storage = $storage;
  }
}

<?php

abstract class Dumper_Content_Base_Entity extends Dumper_Content_Controller {
  /**
   * Entity type.
   *
   * @var string
   */
  public $entity_type;

  /**
   * Get fields of the entity.bundle.
   */
  public function getFields() {}

  /**
   * @task fetch
   */
  public function getItemIds() {
  }

  public function queueItems() {
    foreach ($this->getItemIds() as $entity_id) {
      $this->queueItem($entity_id);
    }
  }

  public function queueItem($entity_id) {
    return db_insert($this->queue_table, array(
      'og_id' => $this->school->identifier(),
      'entity_type' => $this->entity_type,
      'entity_id' => $nid,
    ))->execute();
  }

  /**
   * Wrapper function to process the queues.
   *
   * @task process
   */
  public function getNextQueueItems() {
  }

  public function processQueueItem($entity_id) {
  }
}

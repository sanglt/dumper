<?php

class Dumper_Content_Base_Entity extends Dumper_Content_Base_Controller {
  /**
   * Get fields of the entity.bundle.
   */
  public function getFields() {}

  /**
   * Get items IDs.
   *
   * @task fetch
   */
  public function getItemIds() {
    $select = db_select('og_membership', 'ogm');
    $select->condition('ogm.gid', $this->og->nid);
    $select->condition('ogm.group_type', 'node');
    $select->condition('ogm.entity_type', $this->entity_type);
    $select->fields('ogm', array('etid'));
    $ids = $select->execute()->fetchAllKeyed();
    $ids = array_keys($ids);
    return $ids;
  }

  public function queueItems() {
    // Delete the current queue
    $query = db_delete($this->queue_table)
      ->condition('gid', $this->og->nid)
      ->condition('entity_type', $this->entity_type)
      ->execute();

    foreach ($this->getItemIds() as $entity_id) {
      $this->queueItem($entity_id);
    }
  }

  /**
   * Write an entity to queue by ID.
   *
   * @param int $entity_id
   */
  public function queueItem($entity_id) {
    return db_insert($this->queue_table)
      ->fields(array(
        'gid' => $this->og->nid,
        'entity_type' => $this->entity_type,
        'entity_id' => $entity_id,
        'processed' => 0
      ))
      ->execute();
  }

  /**
   * Wrapper function to process the queues.
   *
   * @task process
   */
  public function getNextQueueItems() {
  }

  /**
   * Process a queued entity by ID.
   *
   * @param int $entity_id
   */
  public function processQueueItem($entity_id) {
  }
}

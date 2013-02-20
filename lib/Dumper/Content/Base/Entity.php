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
    $select->condition('ogm.gid', $this->og_controller->og_node->nid);
    $select->condition('ogm.group_type', 'node');
    $select->condition('ogm.entity_type', $this->entity_type);
    $select->fields('ogm', array('etid'));
    $ids = $select->execute()->fetchAllKeyed();
    $ids = array_keys($ids);
    return $ids;
  }

  /**
   * Fetch IDs and add them to queue.
   */
  public function queueItems() {
    // Delete the current queue
    db_delete($this->queue_table)
      ->condition('gid', $this->og_controller->og_node->nid)
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
        'gid' => $this->og_controller->og_node->nid,
        'entity_type' => $this->entity_type,
        'entity_id' => $entity_id,
        'processed' => 0
      ))
      ->execute();
  }

  /**
   * Process a queued entity item.
   *
   * @param Dumper_Data_QueueItem $queue_item
   */
  public function processQueueItem(Dumper_Data_QueueItem $queue_item) {
    if (function_exists('drush_log')) {
      drush_log(" › Processing {$queue_item->entity_type}#{$queue_item->entity_id}");
    }

    $entity = entity_load_single($queue_item->entity_type, $queue_item->entity_id);
    $path  = "private://dumper/{$this->og_controller->og_node->nid}";
    $path .= "/{$queue_item->entity_type}/{$queue_item->entity_id}.json";
    $this->storage->setPath($path);
    
    return $this->storage->write($entity);
  }
}

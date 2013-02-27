<?php

class Dumper_Content_Node extends Dumper_Content_Base_Entity {
  /**
   * Node entity has comment, we need add related comment to the queue.
   *
   * @param Dumper_Data_QueueItem $queue_item
   * @return boolean
   */
  public function preprocessQueueItem(Dumper_Data_QueueItem $queue_item) {
    if (FALSE === parent::preprocessQueueItem($queue_item)) {
      return FALSE;
    }

    // Queue related comments
    $select = db_select('comment', 'c');
    $select->condition('c.nid', $queue_item->entity_id);
    $select->fields('c', array('cid'));
    foreach ($select->execute()->fetchAll() as $row) {
      if (!isset($controller)) {
        $controller = $this->og_controller->getDataController('comment');
      }
      $controller->queueItem($row->cid);
    }
  }
}

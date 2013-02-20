<?php

class Dumper_Controller_Og_Process {
  /**
   * Og Controller.
   *
   * @var Dumper_Controller_Og
   */
  public $og;

  public function __construct($og) {
    $this->og = $og;
  }

  /**
   * Get next item in queue.
   *
   * @task fetch
   * @return QueueItem
   */
  public function getNextQueueItem() {
    $select = db_select($this->og->queue_table, 'queue');
    $select->condition('queue.entity_type', $this->og->og_node->entityType());
    $select->condition('queue.processed', 0);
    $item = $select->execute()->fetchObject();
    return new Dumper_Data_QueueItem($this->school, $item->entity_type, $item->entity_id);
  }

  /**
   * Wrapper function to process the queues.
   *
   * @task process
   */
  public function process() {
    $time_start = time();

    while (time() - $time_start < $this->timeout) {
      if ($queue_item = $this->getNextQueueItem()) {
        $this->processQueueItem($queue_item);
      }
    }
  }

  public function processQueueItem(QueueItem $queue_item) {
    $controller = $this->getDataController($queue_item->entity_type);
    return $controller->processQueueItem($queue_item->entity_id);
  }

  /**
   * @task fetch
   */
  protected function getNodeTypes() {}
}

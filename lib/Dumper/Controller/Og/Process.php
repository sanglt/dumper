<?php

/**
 * Class to process the queued items.
 *
 * @see Dumper_Controller_Og#process
 */
class Dumper_Controller_Og_Process {
  /**
   * Og Controller.
   *
   * @var Dumper_Controller_Og
   */
  public $og_controller;

  /**
   * Process timeout.
   *
   * @var int
   */
  public $timeout = 300; // 5 minutes

  public function __construct($og_controller) {
    $this->og_controller = $og_controller;
  }

  /**
   * Get next item in queue.
   *
   * @task fetch
   * @return Dumper_Data_QueueItem
   */
  public function getNextQueueItem() {
    $select = db_select($this->og_controller->queue_table, 'queue');
    $select->condition('queue.processed', 0);
    $select->fields('queue', array('entity_type', 'entity_id'));
    $select->range(0, 1);

    if ($item = $select->execute()->fetchObject()) {
      return new Dumper_Data_QueueItem($this->og_controller->og_node->nid, $item->entity_type, $item->entity_id);
    }

    return FALSE;
  }

  /**
   * Wrapper function to process the queues.
   *
   * @task process
   * @todo resume
   */
  public function process() {
    $time_start = time();

    // Place to dump data to
    $uri  = "private://dumper/{$this->og_controller->og_node->nid}";

    // Clean up the tmp directory
    file_unmanaged_delete_recursive($uri);

    while (TRUE) {
      if (time() - $time_start > $this->timeout) {
        throw new Dumper_Controller_Exception_Timeout();
      }

      if ($queue_item = $this->getNextQueueItem()) {
        $this->processQueueItem($queue_item);
      }
      else {
        break;
      }
    }

    $storage = new Dumper_Controller_Filestorage();
    $path = "private://dumper_compress/og.{$this->og_controller->og_node->nid}/". date('Y/m/d') ."/backup.tar";
    $storage->setPath($path);
    $storage->compress($uri);
  }

  /**
   * Process a queue item.
   *
   * @param Dumper_Data_QueueItem $queue_item
   * @return type
   */
  public function processQueueItem($queue_item) {
    $controller = $this->og_controller->getDataController($queue_item->entity_type);

    if (FALSE === $controller->processQueueItem($queue_item)) {
      if (function_exists('drush_set_error')) {
        drush_set_error("Failed on processing {$queue_item->entity_type}#{$queue_item->entity_id}");
      }
      return FALSE;
    }

    db_update($this->og_controller->queue_table)
      ->fields(array('processed' => 1))
      ->condition('entity_type', $queue_item->entity_type)
      ->condition('entity_id', $queue_item->entity_id)
      ->execute();

    return TRUE;
  }
}

<?php

interface Dumper_Content_Base_Interface {
  public function __construct($school, $entity_type, $entity = NULL, $storage = NULL);
  public function setEntity($entity);
  public function queueItems();
  public function queueItem($entity_id);
  public function processQueuedItems();
  public function processQueuedItem(Dumper_Data_QueueItem $queue_item);
}

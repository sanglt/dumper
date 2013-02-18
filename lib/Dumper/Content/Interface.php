<?php

interface Dumper_Content_Interface {
  public function __contruct($school, $entity_type, $entity = NULL, $storage = NULL);
  public function setEntity($entity);
  public function queueItems();
  public function queueItem($entity_id);
  public function processItems();
  public function processItem($entity_id);
}

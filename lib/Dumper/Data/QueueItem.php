<?php

class Dumper_Data_QueueItem {
  public $gid;
  public $entity_type;
  public $entity_id;

  public function __construct($gid, $entity_type, $entity_id) {
    $this->gid = $gid;
    $this->entity_type = $entity_type;
    $this->entity_id = $entity_id;
  }
}

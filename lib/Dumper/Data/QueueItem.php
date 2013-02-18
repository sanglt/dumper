<?php

class Dumper_Data_QueueItem {
  public $school;
  public $entity_type;
  public $entity_id;

  public function __construct($school, $entity_type, $entity_id) {
    $this->school = $school;
    $this->entity_type = $entity_type;
    $this->entity_id = $entity_id;
  }
}

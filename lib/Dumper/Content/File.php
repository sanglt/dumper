<?php

class Dumper_Content_File extends Dumper_Content_Base_Entity {
  public function preprocessQueueItem(Dumper_Data_QueueItem $queue_item) {
    if (!parent::preprocessQueueItem($queue_item)) {
      return FALSE;
    }

    drush_print_r($this->entity);
  }
}

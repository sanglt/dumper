<?php

class Dumper_Content_Taxonomy_Term extends Dumper_Content_Base_Entity {
  public function preprocessQueueItem(Dumper_Data_QueueItem $queue_item) {
    if (FALSE === parent::preprocessQueueItem($queue_item)) {
      return FALSE;
    }

    $controller = $this->og_controller->getDataController('taxonomy_vocabulary');
    $controller->queueItem($this->entity->vid);
  }
}

<?php

class Dumper_Content_File extends Dumper_Content_Base_Entity {
  public function preprocessQueueItem(Dumper_Data_QueueItem $queue_item) {
    if (FALSE === parent::preprocessQueueItem($queue_item)) {
      return FALSE;
    }

    $path  = "private://dumper/{$this->og_controller->og_node->nid}/" . $this->meta->type();
    $path .= "/media/" . date('Y/m', $this->entity->timestamp);
    $path .= "/". $this->meta->getIdentifier() .".file";
    $this->storage->setPath($path);
    $this->storage->copy($this->entity->uri);
  }
}

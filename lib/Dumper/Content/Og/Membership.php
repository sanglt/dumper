<?php

class Dumper_Content_Og_Membership extends Dumper_Content_Base_Entity {
  /**
   * @task fetch
   */
  public function getItemIds() {
    $query = new EntityFieldQuery();
    $query = $query->entityCondition('entity_type', $this->entity_type);
    $query->propertyCondition('gid', $this->og_controller->og_node->nid);
    $memberships = $query->execute();
    return array_keys($memberships[$this->entity_type]);
  }

  /**
   * Method called before an entity is indexed.
   *
   * @param Dumper_Data_QueueItem $queue_item
   */
  protected function preprocessQueueItem(Dumper_Data_QueueItem $queue_item) {
    parent::preprocessQueueItem($queue_item);

    // Queue entity
    $controller = $this->og_controller->getDataController($this->entity->entity_type);
    $controller->queueItem($this->entity->etid);
  }
}

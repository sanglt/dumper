<?php

class Dumper_Content_Base_Entity extends Dumper_Content_Base_Controller {
  /**
   * Entity wrapper
   *
   * @var EntityStructureWrapper
   */
  public $meta;

  /**
   * Get fields of the entity.bundle.
   */
  public function getFields() {}

  /**
   * Get items IDs.
   *
   * @task fetch
   */
  public function getItemIds() {}

  /**
   * Fetch IDs and add them to queue.
   */
  public function queueItems() {
    // Delete the current queue
    db_delete($this->queue_table)
      ->condition('gid', $this->og_controller->og_node->nid)
      ->condition('entity_type', $this->entity_type)
      ->execute();

    foreach ($this->getItemIds() as $entity_id) {
      $this->queueItem($entity_id);
    }
  }

  /**
   * Write an entity to queue by ID.
   *
   * @param int $entity_id
   */
  public function queueItem($entity_id) {
    // An item can not be queued twice
    $pk = array('gid' => $this->og_controller->og_node->nid,
                'entity_type' => $this->entity_type,
                'entity_id' => $entity_id);
    $counter = db_select($this->queue_table, 'og_queue');
    $counter->condition('og_queue.gid',         $pk['gid']);
    $counter->condition('og_queue.entity_type', $pk['entity_type']);
    $counter->condition('og_queue.entity_id',   $pk['entity_id']);
    $counter->addExpression('COUNT(*)', 'counter');
    if (!$counter = $counter->execute()->fetchColumn()) {
      if (function_exists('drush_log')) {
        drush_log("    » Queuing {$pk['entity_type']}#{$pk['entity_id']}");
      }

      return db_insert($this->queue_table)
              ->fields(array('processed' => 0) + $pk)
              ->execute();
    }
  }

  /**
   * Process a queued entity item.
   *
   * @param Dumper_Data_QueueItem $queue_item
   */
  public function processQueueItem(Dumper_Data_QueueItem $queue_item) {
    if (TRUE !== $this->loadEntity($queue_item)) return;
    $this->preprocessQueueItem($queue_item);
    $this->write();
  }

  /**
   * Method called before an entity is indexed.
   *
   * @param Dumper_Data_QueueItem $queue_item
   */
  protected function preprocessQueueItem(Dumper_Data_QueueItem $queue_item) {
    $this->meta = entity_metadata_wrapper($queue_item->entity_type, $this->entity);
    foreach ($this->meta->getPropertyInfo() as $field_name => $property_info) {
      if (!empty($property_info['field'])) {
        $this->preprocessQueueItemField($field_name, $property_info);
      }
    }
  }

  protected function preprocessQueueItemField($field_name, $property_info) {
    switch ($property_info['type']) {
      case 'field_item_file':
      case 'list<field_item_file>':
      case 'field_item_image':
      case 'list<field_item_image>':
        list($controller, $extended_entity_ids) = $this->preprocessQueueItemFieldExtendedEntity($field_name, $property_info, 'file');
        break;
      case 'taxonomy_term':
      case 'list<taxonomy_term>':
        list($controller, $extended_entity_ids) = $this->preprocessQueueItemFieldExtendedEntity($field_name, $property_info, 'taxonomy_term');
        break;
      default:
        # drush_print_r('    ' . $property_info['type']);
    }

    if (isset($controller) && !empty($extended_entity_ids)) {
      foreach ($extended_entity_ids as $extended_entity_id) {
        # drush_print_r('    ' . $property_info['type'] . ': ' . $extended_entity_id);
        $controller->queueItem($extended_entity_id);
      }
      unset($controller);
    }
  }

  protected function preprocessQueueItemFieldExtendedEntity($field_name, $property_info, $extended_entity_type) {
    if (empty($this->meta->{$field_name})) return;
    if (!$values = $this->meta->{$field_name}->value()) return;

    $extended_entity_ids = array();

    switch ($extended_entity_type) {
      case 'file':
        $key = 'fid';
        break;
      case 'taxonomy_term':
        $key = 'tid';
        break;
      default:
        throw new Exception('Unknown extended entity type.');
    }

    if (!$is_list = strpos($property_info['type'], 'list<') === 0) {
      $extended_entity_ids[] = is_object($values) ? $values->{$key} : $values[$key];
    }
    else {
      foreach ($values as $field_item) {
        $extended_entity_ids[] = is_object($field_item) ? $field_item->{$key} : $field_item[$key];
      }
    }

    return array(
      $this->og_controller->getDataController('file'),
      $extended_entity_ids
    );
  }

  /**
   * Load entity from QueueItem, throw exception if there is some unexpected
   * result.
   *
   * @param Dumper_Data_QueueItem $queue_item
   * @return Entity
   */
  protected function loadEntity(Dumper_Data_QueueItem $queue_item, $throw = TRUE) {
    if (function_exists('drush_log')) {
      drush_log(" › Processing {$queue_item->entity_type}#{$queue_item->entity_id}");
    }

    if ($this->entity = entity_load_single($queue_item->entity_type, $queue_item->entity_id)) {
      return TRUE;
    }

    if (!$this->entity && $throw) {
      throw new Exception('Entiy not found');
    }
  }

  protected function write() {
    $path  = "private://dumper/{$this->og_controller->og_node->nid}";
    $path .= "/". $this->meta->type() ."/". $this->meta->getIdentifier() .".json";
    $this->storage->setPath($path);
    return $this->storage->write($this->entity);
  }
}

<?php

abstract class Restorer_Content_Base_Entity implements Restorer_Content_Base_Interface {
  public $entity_type;

  /**
   * Main OG Restorer controller.
   *
   * @var Restorer_Controller_Og
   */
  public $og_controller;

  public $meta;

  public $entity;

  public function __construct($og_controller, $entity_type) {
    $this->og_controller = $og_controller;
    $this->entity_type = $entity_type;
  }

  /**
   * Get path to directory where store the dumped entities.
   */
  public function getPath() {
    $path = 'private://dumper/%d/%s/%s';
    $path = sprintf($path,
              $this->og_controller->og_node->nid,
              $this->og_controller->date->format('Y/m/d'),
              $this->entity_type);
    return $path;
  }

  public function setEntity($entity) {
    $this->entity = $entity;
  }

  public function setMeta($meta) {
    $this->meta = $meta;
  }

  /**
   * Wrapper function to restore all entities in this entity type.
   */
  public function restoreEntityType() {
    $nid = $this->og_controller->og_node->nid;
    $path = "private://dumper/restore.{$nid}/private://dumper/{$nid}/{$this->entity_type}";
    foreach (file_scan_directory($path, '/\.json/') as $file) {
      $this->restoreEntity($file->name);
    }
  }

  public function restoreEntity($entity_id) {
    $gid  = $this->og_controller->og_node->nid;
    $path = "private://dumper/restore.{$gid}/private://dumper/{$gid}/{$this->entity_type}/{$entity_id}.json";
    if ($entity = file_get_contents($path)) {
      if ($entity = unserialize($entity)) {
        if ($meta = entity_metadata_wrapper($this->entity_type, $entity)) {
          $this->setEntity($entity);
          $this->setMeta($meta);
          return $this->saveEntity();
        }

        throw new Exception('Can not create metadata wrapper: ' . filter_xss($file));
      }

      throw new Exception('Unserialize failed: ' . filter_xss($file));
    }
    return FALSE;
  }

  public function saveEntity() {
    $this->presaveEntity();
    entity_save($this->entity_type, $this->entity);
  }

  protected function presaveEntity() {
    $this->presaveCleanupEntity();
    $this->presaveChecKFields();
  }

  protected function getDummyEntity() {
    $_entity[$this->meta->entityKey('id')] = $this->meta->getIdentifier();
    $_entity[$this->meta->entityKey('bundle')] = $this->meta->type();
    if ($this->meta->entityKey('revision')) {
      $_entity[$this->meta->entityKey('revision')] = $this->entity->{$this->meta->entityKey('revision')};
    }
    $_entity = entity_create($this->meta->type(), $_entity);
    return $_entity;
  }

  protected function presaveCleanupEntity() {
    $this->presaveDeleteExisting();
    entity_save($this->meta->type(), $_entity = $this->getDummyEntity());
  }

  protected function presaveDeleteExisting() {
    entity_delete($this->meta->type(), $this->meta->getIdentifier());
  }

  protected function presaveChecKFields() {
    foreach ($this->meta->getPropertyInfo() as $field_name => $property_info) {
      if (!empty($property_info['field'])) {
        $this->presaveCheckField($field_name, $property_info);
      }
    }
  }

  protected function presaveCheckField($field_name, $property_info) {
    switch ($property_info['type']) {
      case 'list<node>':
        if (strpos($field_name, 'og_group_') === 0) {
          unset($this->entity->{$field_name});
        }
        break;
      case 'field_item_file':
      case 'list<field_item_file>':
      case 'field_item_image':
      case 'list<field_item_image>':
        $controller = $this->og_controller->getDataController('file');
        foreach ($this->entity->{$field_name} as $language => $items) {
          foreach ($items as $delta => $item) {
            $controller->restoreEntity($item['fid']);
            unset($this->entity->{$field_name}[$language][$delta]);
          }
        }
        break;
    }
  }
}

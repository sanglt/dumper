<?php

class Restorer_Content_Base_Entity {
  public $entity_type;

  public $og_controller;

  public function __construct($og_controller, $entity_type) {
    $this->og_controller = $og_controller;
    $this->entity_type = $entity_type;
  }

  /**
   * Get path to directory where store the dumped entities.
   */
  public function getPath() {
    $path = 'private://dumper/%d/%s/%s';
    $path = sprintf($path, $this->entity_type);
    return $path;
  }

  /**
   * Wrapper function to restore all entities in this entity type.
   */
  public function restore() {
  }
}

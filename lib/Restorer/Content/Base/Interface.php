<?php

interface Restorer_Content_Base_Interface {
  public function __construct($og_controller, $entity_type);
  public function getPath();
  public function setEntity($entity);
  public function setMeta($meta);
  public function restoreEntityType();
  public function restoreEntity($entity_id);
}

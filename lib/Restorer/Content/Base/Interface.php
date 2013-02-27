<?php

interface Restorer_Content_Base_Interface {
  public function __construct($og_controller, $entity_type);
  public function getPath();
  public function restore();
}

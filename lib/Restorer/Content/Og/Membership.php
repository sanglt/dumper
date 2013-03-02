<?php

class Restorer_Content_Og_Membership extends Restorer_Content_Base_Entity {
  public function save($og_membership) {
    return;
    if (!$entity = entity_load_single($og_membership->entity_type, $og_membership->etid)) {
      $controller = $this->og_controller->getDataController($og_membership->entity_type);
      $controller->restoreEntity($og_membership->etid);
    }

    $this->meta = entity_metadata_wrapper($this->entity_type, $og_membership);
    $this->meta->save();
  }
}

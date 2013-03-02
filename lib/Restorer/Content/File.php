<?php

class Restorer_Content_File extends Restorer_Content_Base_Entity {
  public function setEntity($entity) {
    /**
     * If there's a new file with same URI, we have to change URI of dumped file.
     */
    parent::setEntity($entity);

    // Find existing file by URI
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', $this->entity_type);
    $query->propertyCondition('uri', $this->entity->uri);
    if ($query = $query->execute()) {
      $fid = reset(array_keys($query[$this->entity_type]));
      if ($file = entity_load_single($this->entity_type, $fid)) {
        $name      = pathinfo($file->uri, PATHINFO_FILENAME);
        $extension = pathinfo($file->uri, PATHINFO_EXTENSION);
        $old = "{$name}.{$extension}";
        $new = "{$name}_". time() .".{$extension}";
        $this->entity->uri = preg_replace("/{$old}$/", $new, $this->entity->uri);
      }
    }
  }
}

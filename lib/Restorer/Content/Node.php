<?php

class Restorer_Content_Node extends Restorer_Content_Base_Entity {
  public function save($node) {
    db_merge('node')
      ->key(array('nid' => $node->nid))
      ->fields(array('nid'  => $node->nid, 'type' => $node->type, 'title' => $node->title))
      ->execute();
    db_merge('node_revision')
      ->key(array('vid' => $node->vid))
      ->fields(array('nid'  => $node->nid, 'vid'  => $node->vid, 'log'  => ''))
      ->execute();
    return parent::save($node);
  }
}

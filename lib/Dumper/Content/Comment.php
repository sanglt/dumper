<?php

class Dumper_Content_Comment extends Dumper_Content_Base_Entity {
  public function getItemIds() {
    $select = db_select('og_membership', 'ogm');
    $select->condition('ogm.gid', $this->og->nid);
    $select->condition('ogm.group_type', 'node');
    $select->condition('ogm.entity_type', 'node');
    $select->innerJoin('node', 'node', 'ogm.etid = node.nid');
    $select->innerJoin('comment', 'comment', 'node.nid = comment.nid');
    $select->fields('comment', array('cid'));
    $ids = $select->execute()->fetchAllKeyed();
    $ids = array_keys($ids);
    return $ids;
  }
}

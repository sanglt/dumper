<?php

class Dumper_Tests_Queue  extends Dumper_Tests_Base {
  /**
   * @covers Dumper_Controller_Og::queue()
   */
  public function testQueue() {
    // Queue all contents for dumping
    $this->og_controller->queue();

    // …
    $this->assertIdentical(TRUE, $this->isInQueue('user', $this->user_1->uid));
    $this->assertIdentical(TRUE, $this->isInQueue('user', $this->user_2->uid));
    $this->assertIdentical(TRUE, $this->isInQueue('node', $this->node_1->nid));
    $this->assertIdentical(TRUE, $this->isInQueue('comment', $this->comment_1->cid));
    $this->assertIdentical(TRUE, $this->isInQueue('comment', $this->comment_2->cid));
  }

  /**
   * Chech is an item in queue.
   *
   * @param type $entity_type
   * @param type $entity_id
   * @return type
   */
  public function isInQueue($entity_type, $entity_id) {
    $select = db_select($this->og_controller->queue_table, 'queue');
    $select->addExpression('COUNT(*)', 'counter');
    $select->condition('queue.gid', $this->og->nid);
    $select->condition('queue.entity_type', $entity_type);
    $select->condition('queue.entity_id', $entity_id);
    return $select->execute()->fetchColumn() == 1;
  }
}

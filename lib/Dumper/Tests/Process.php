<?php

/**
 * @see Dumper_Controller_Og
 */
class Dumper_Tests_Process  extends Dumper_Tests_Base {
  /**
   * @covers Dumper_Controller_Og::__construct().
   */
  public function testBackupNoneExistingOgNode() {
    try {
      // Execute the backup process with node-NID is an NID of non-object node.
      $dumper = new Dumper_Controller_Og(9999999);

      // If this line is run, the exception is not throw.
      $this->assertTrue(FALSE);
    }
    catch (Dumper_Controller_Exception_Not_Found_Og $e) {
      $this->assertTrue(TRUE);
    }
  }

  public function testBackupInvalidOgNode() {
    try {
      // Execute the backup process with node-NID is an NID of object-node, but
      // this node is not an og-node.
      $dumper = new Dumper_Controller_Og($this->node_1);

      // If this line is run, the exception is not throw.
      $this->assertTrue(FALSE);
    }
    catch (Dumper_Controller_Exception_Invalid_Og $e) {
      $this->assertTrue(TRUE);
    }
  }

  public function testBackupValidOgNode() {
    $dumper = new Dumper_Controller_Og($this->og);
    $dumper->queue();
    $dumper->process();
  }
}

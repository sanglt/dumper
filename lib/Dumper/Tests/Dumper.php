<?php

class Dumper_Tests_Dumper {
  /**
   * Organic Group node
   *
   * @var stdClass
   */
  public $og;

  /**
   * User 1.
   *
   * @var stdClass
   */
  public $user_1;

  /**
   * User 2.
   *
   * @var stdClass
   */
  public $user_2;

  /**
   * File 1.
   *
   * @var stdClass
   */
  public $file_1;

  /**
   * Node 1.
   *
   * @var stdClass
   */
  public $node_1;

  /**
   * Comment 1.
   *
   * @var stdClass
   */
  public $comment_1;

  /**
   * Comment 2.
   *
   * @var stdClass
   */
  public $comment_2;

  /**
   * Taxonomy term 1.
   *
   * @var stdClass
   */
  public $term_1;

  /**
   * Implements hook#setUp().
   *
   * @TODO: Make sure the modules are enabled: og/comment/taxonomy
   */
  public function setUp() {
    $this->configStructure();
    $this->createDummyData();
  }

  /**
   * Implements hook#tearDown().
   */
  public function tearDown() {
    $this->clearDummyData();
  }

  /**
   * Configure site structure.
   */
  protected function configStructure() {
    // Create an og-group node type

    // Create an og-content node type, support comment, tagging
  }

  protected function createDummyData() {
    // Create user-1 (admin), user-2

    // User-1 creates og-node

    // User-2 join og-node

    // User 1 create a file, this will be attached to og-content

    // Create new taxonomy term, this will be attached to og-content.

    // User-1 creates a new og-content

    // User-1, User-2 comment on og-content
  }

  protected function clearDummyData() {
    // Delete og-content, comment and media is automatically deleted.

    // Delete user

    // Delete og-node
  }

  /**
   * Test case to make sure that the setup is ok.
   *
   * @see entity_get_info().
   */
  public function testSetUpOk() {
    // Modules should be enabled
    // …

    // Objects are created
    $this->assertEqual(get_class($this->og), 'stdClass');
    $this->assertEqual(get_class($this->user_1), 'stdClass');
    $this->assertEqual(get_class($this->user_2), 'stdClass');
    $this->assertEqual(get_class($this->node_1), 'stdClass');
    $this->assertEqual(get_class($this->file_1), 'stdClass');
    $this->assertEqual(get_class($this->term_1), 'stdClass');
    $this->assertEqual(get_class($this->comment_1), 'stdClass');
    $this->assertEqual(get_class($this->comment_2), 'stdClass');
  }

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

  public function testBackupValidOgNode() {}
}

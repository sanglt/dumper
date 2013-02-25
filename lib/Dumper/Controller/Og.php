<?php

/**
 * Wrapper class to queue and process the OG content.
 *
 * @task queue   Queue content
 * @task process Process queued content
 */
class Dumper_Controller_Og {
  /**
   * Node object.
   *
   * @var stdClass
   */
  public $og_node;

  /**
   * Table name
   *
   * @var string
   */
  public $queue_table = 'dumper_content_queue';

  /**
   * Supported entity types.
   *
   * @var array
   */
  public $entity_types = array(
    'user', 'node', 'comment', 'taxonomy_vocabulary', 'taxonomy_term',
  );

  /**
   * @task config
   */
  public function __construct($og_node) {
    if ($this->validateOgNode($og_node)) {
      $this->setOg($og_node);
    }
  }

  /**
   * Set value og_node property.
   *
   * @param stdClass $og_node
   */
  public function setOg($og_node) {
    $this->og_node = $og_node;
  }

  /**
   * Validate is a node a valid organic group.
   *
   * @param stdClass $og_node
   * @return boolean
   * @throws Dumper_Controller_Exception_Invalid_Og
   */
  public static function validateOgNode($og_node) {
    if (!is_object($og_node)) {
      throw new Dumper_Controller_Exception_Invalid_Og('Is not an object.');
    }

    if (!isset($og_node->nid) || !isset($og_node->type) || !isset($og_node->vid)) {
      throw new Dumper_Controller_Exception_Invalid_Og('Object is not a node.');
    }

    if (!og_is_group('node', $og_node)) {
      $wrapper = entity_metadata_wrapper('node', $og_node);
      throw new Dumper_Controller_Exception_Invalid_Og('Node is not an organic group.');
    }

    return TRUE;
  }

  /**
   * Get entity-controller by entity type.
   *
   * @param string $entity_type
   * @return Dumper_Content_Base_Entity
   * @throws Dump_Controller_Exception_Not_Found_Controller
   * @throws Dump_Controller_Exception_Malware_Interface
   */
  public function getDataController($entity_type) {
    switch ($entity_type) {
      case 'comment':
        $class = 'Dumper_Content_Comment';
        break;
      case 'file':
        $class = 'Dumper_Content_File';
        break;
      case 'node':
        $class = 'Dumper_Content_Node';
        break;
      case 'og_membership_type':
        $class = 'Dumper_Content_Og_Membership_Type';
        break;
      case 'og_membership':
        $class = 'Dumper_Content_Og_Membership';
        break;
      case 'taxonomy_vocabulary':
        $class = 'Dumper_Content_Taxonomy_Vocabulary';
        break;
      case 'taxonomy_term':
        $class = 'Dumper_Content_Taxonomy_Term';
        break;
      case 'user':
        $class = 'Dumper_Content_User';
        break;
      default:
        throw new Dump_Controller_Exception_Not_Found_Controller();
    }

    if (!empty($class)) {
      $controller = new $class($this, $entity_type);
      if (class_implements($controller, 'Dumper_Content_Base_Interface')) {
        return $controller;
      }

      throw new Dump_Controller_Exception_Malware_Interface();
    }

    return FALSE;
  }

  /**
   * Wrapper function to queue the og's data.
   *
   * @task queue
   */
  public function queue() {
    if (function_exists('drush_log')) {
      drush_log("Queue contents before dumping for group: {$this->og_node->title}");
    }

    foreach ($this->entity_types as $entity_type) {
      $controller = $this->getDataController($entity_type);
      $controller->queueItems();
    }
  }

  /**
   * Wrapper method to start the process
   *
   * @task process
   * @see Dumper_Controller_Og_Process
   */
  public function process() {
    $process = new Dumper_Controller_Og_Process($this);
    return $process->process();
  }
}

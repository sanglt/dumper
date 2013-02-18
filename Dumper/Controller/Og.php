<?php

class Dumper_Controller_Og {
  public $school;
  public $queue_table = 'aio_dumper_queue';
  public $timeout = 300;
  public $entity_types = array(
    'comment', 'node',
    'og_membership_type', 'og_membership',
    'taxonomy_vocabulary', 'taxonomy_term',
    'user'
  );

  /**
   * @task config
   */
  public function __construct($school) {
    $this->schoold = $school;
  }

  public function getDataController($entity_type) {
    switch ($entity_type) {
      case 'comment':
        $class = 'Dumper_Content_Comment';
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
      $controller = new $class($this->school, $entity_type);
      if (class_implements($controller, 'Dumper_Content_Interface')) {
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
    foreach ($this->entity_types as $entity_type) {
      $controller = $this->getDataController($entity_type);
      $controller->queue();
    }
  }

  public function process() {
    $process = new Dumper_Controller_Og_Process($this);
    $process->process();
  }
}

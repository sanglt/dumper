<?php

/**
 * Wrapper class to restore the dumped og contents.
 */
class Restorer_Controller_Og {
  /**
   * Organic Group node.
   *
   * @var stdClass
   */
  public $og_node;

  /**
   * Entity types ordered by priority.
   *
   * @var array
   */
  public $entity_types = array(
    'taxonomy_vocabulary', 'taxonomy_term',
    'file', 'node', 'comment',
    'og_membership_type', 'og_membership',
    'user');

  /**
   * Date when data was backed up.
   *
   * @var DateTime
   */
  public $date;

  public function __construct($og, \DateTime $date) {
    $this->og_node = $og;
    $this->date = $date;
    $this->validate();
  }

  public function validate() {
    $ok = file_exists($this->getBackupURI()) && is_readable($this->getBackupURI());

    if (!$ok) {
      throw new Exception('Backup file not found: ' . $this->getBackupURI());
    }

    return $ok;
  }

  /**
   * Path to backup file -- tar.
   *
   * @return string
   */
  public function getBackupURI() {
    $path = 'private://dumper_compress/og.%d/%s/backup.tar';
    $path = sprintf($path, $this->og_node->nid, $this->date->format('Y/m/d'));
    if (file_prepare_directory($dir = dirname($path), FILE_CREATE_DIRECTORY)) {
      return $this->getRealPath($path);
    }
    return FALSE;
  }

  /**
   * Directory to extract the backed up tar file to.
   *
   * @return string
   */
  public function getRestoreURI() {
    $path = sprintf('private://dumper/restore.%d', $this->og_node->nid);
    if (file_prepare_directory($dir = dirname($path), FILE_CREATE_DIRECTORY)) {
      drush_print_r(array($path));
      return $this->getRealPath($path);
    }
    return FALSE;
  }

  /**
   * Get real local path.
   *
   * @param string $path
   * @return string
   */
  public function getRealPath($path) {
    $wrapper = file_stream_wrapper_get_instance_by_uri($path);
    $wrapper->setUri($path);
    return $wrapper->realpath();
  }

  /**
   * Get entity-controller by entity type.
   *
   * @param string $entity_type
   * @return Restorer_Content_Base_Entity
   * @throws Dump_Controller_Exception_Not_Found_Controller
   * @throws Dump_Controller_Exception_Malware_Interface
   */
  public function getDataController($entity_type) {
    switch ($entity_type) {
      case 'comment':
        $class = 'Restorer_Content_Comment';
        break;
      case 'file':
        $class = 'Restorer_Content_File';
        break;
      case 'node':
        $class = 'Restorer_Content_Node';
        break;
      case 'og_membership_type':
        $class = 'Restorer_Content_Og_Membership_Type';
        break;
      case 'og_membership':
        $class = 'Restorer_Content_Og_Membership';
        break;
      case 'taxonomy_vocabulary':
        $class = 'Restorer_Content_Taxonomy_Vocabulary';
        break;
      case 'taxonomy_term':
        $class = 'Restorer_Content_Taxonomy_Term';
        break;
      case 'user':
        $class = 'Restorer_Content_User';
        break;
      default:
        throw new Dumper_Controller_Exception_Not_Found_Controller();
    }

    if (!empty($class)) {
      $controller = new $class($this, $entity_type);
      if (class_implements($controller, 'Restorer_Content_Base_Interface')) {
        return $controller;
      }
      throw new Dumper_Controller_Exception_Malware_Interface();
    }

    return FALSE;
  }

  /**
   * Method to restore.
   */
  public function restore() {
    $source = $this->getBackupURI();
    $dest = $this->getRestoreURI();

    file_unmanaged_delete_recursive($dest);

    // uncompress the tar file
    $tar = new ArchiverTar($source);
    $tar->extract($dest);

    // loops and restore the entities
    foreach ($this->entity_types as $entity_type) {
      $this->restoreEntityType($entity_type);
    }
  }

  /**
   * Restore backed-up entities with specified entity type.
   *
   * @param string $entity_type
   */
  public function restoreEntityType($entity_type) {
    $controller = $this->getDataController($entity_type);
    $controller->restore();
  }
}

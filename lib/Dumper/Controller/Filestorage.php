<?php

class Dumper_Controller_Filestorage {
  public $base_path;

  public function __construct($base_path) {
    $this->base_path = "private://{$base_path}";
  }

  public function getPath() {
    if (!$this->isWritable()) {
      throw new DumperFileStorageException("File is not writable: {$path}");
    }
    return $this->path;
  }

  public function isWritable() {
    return is_writable($this->path);
  }

  /**
   * Write data to file stystem.
   *
   * @param mixed $data
   * @param int $replace
   * @return boolean
   */
  public function write($data, $replace = FILE_EXISTS_REPLACE) {
    if (!is_string($data)) {
      $data = json_encode($data);
    }

    return file_unmanaged_save_data($data, $this->getPath(), $mode);
  }

  public function delete() {
    return file_unmanaged_delete($this->getPath());
  }
}

/**
 * Exception raised by storage class.
 */
class DumperFileStorageException extends Exception {}

<?php

class Dumper_Controller_Filestorage {
  public $path;

  public function setPath($path) {
    $this->path = $path;
  }

  public function getPath() {
    if (!$this->isWritable()) {
      throw new DumperFileStorageException("File is not writable: `{$this->path}`");
    }
    return $this->path;
  }

  public function isWritable() {
    if (file_exists($this->path)) {
      return is_writable($this->path);
    }
    return file_prepare_directory($dir = dirname($this->path), FILE_CREATE_DIRECTORY);
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
    return file_unmanaged_save_data($data, $this->getPath(), $replace);
  }

  public function delete() {
    return file_unmanaged_delete($this->getPath());
  }
}

/**
 * Exception raised by storage class.
 */
class DumperFileStorageException extends Exception {}

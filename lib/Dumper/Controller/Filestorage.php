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

  /**
   * Copy a file to backup area.
   */
  public function copy($source_uri, $replace = FILE_EXISTS_REPLACE) {
    return file_unmanaged_copy($source_uri, $this->getPath(), $replace);
  }

  /**
   * Delete a file -- a wrapper for file_unmanaged_delete().
   */
  public function delete() {
    return file_unmanaged_delete($this->getPath());
  }

  /**
   * Compess a file or a directory.
   */
  public function compress($uri, $delete_old = TRUE) {
    $uri = $this->getRealPath($uri);
    $tar = new ArchiverTar($path = $this->getRealPath($this->getPath()));

    foreach (file_scan_directory($uri, '/.*/') as $file) {
      $tar->add($file->uri);
    }

    if ($delete_old) {
      file_unmanaged_delete_recursive($uri);
    }
  }
}

/**
 * Exception raised by storage class.
 */
class DumperFileStorageException extends Exception {}

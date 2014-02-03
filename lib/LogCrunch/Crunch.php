<?php

namespace LogCrunch;

class Crunch {
  private $log = "";
  const SLICE = 1024;
  const MAX_SLICE = 1048576; // 1MB

  public function __construct($file) {

    if (!file_exists($file))
      throw new \InvalidArgumentException('Problem reading file');

    $this->log = $file;
  }

  public function rawRead($byteToRead) {

    if ($byteToRead > self::MAX_SLICE)
      throw new \Exception('Slice too big!');

    if ($byteToRead < self::SLICE)
      $byteToRead = self::SLICE;

    $fSiz = filesize($this->log);

    /* Do not bother to do anything */
    if ($fSiz == 0)
      return;

    $fp = fopen($this->log, 'r');
    fseek($fp, -$byteToRead, SEEK_END);
    $data = fread($fp, $byteToRead);

    return $data;
  }

  /**
   * Return an array of lines
   */
  public function toArray($data) {
    if (empty($data))
      throw new \InvalidArgumentException('Empty data.');

    return explode("\n", $data);
  }

  /**
   * Tail a file
   */
  public function tail() {
      $inotify = inotify_init();
      if ($inotify == false) {
          throw new Exception('Unable to initialize inotify.');
      }

      $watch = inotify_add_watch($inotify, $this->log, IN_MODIFY);
      if ($watch == false) {
          throw new Exception('Unable to watch file.');
      }

      $fd = fopen($this->log, "r");
      fseek($fd, 0, SEEK_END);

      while(($events = inotify_read($inotify)) !== false) {
          foreach($events as $event) {
              if (!($event['mask'] & IN_MODIFY)) continue;
              $data = stream_get_contents($fd);
              return $data;
          }
      }
  }
}

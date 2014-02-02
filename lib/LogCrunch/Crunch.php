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
  public function tail(&$pos) {
    $fd = inotify_init();
    $watch = inotify_add_watch($fd, $this->log, IN_ALL_EVENTS);

    while(true) {
      $events = inotify_read($fd);
      foreach($events as $e => $ev) {
        switch(true) {
        case ($ev['mask'] & IN_MODIFY):
          inotify_rm_watch($fd, $watch);
          fclose($fd);
          $rv = $this->rawRead(self::SLICE);
          $pos += strlen($rv);
          return $rv;
          break;
        case ($ev['mask'] & IN_DELETE):
        case ($ev['mask'] & IN_DELETE_SELF):
        case ($ev['mask'] & IN_MOVE):
        case ($ev['mask'] & IN_MOVE_SELF):
          inotify_rm_watch($fd, $watch);
          fclose($fd);
          return false;
          break;
        }
      }
    }
  }
}

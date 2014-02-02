<?php
require_once dirname(__FILE__)  . '/../vendor/autoload.php';

$foo = "/tmp/foo.log";
$c = new \LogCrunch\Crunch($foo);
$pos = 0;
while(true) {
  echo $c->tail($pos);
}


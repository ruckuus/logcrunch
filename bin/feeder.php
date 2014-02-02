<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

function doFeed() {
  $esOpts = array(
    'hosts' =>  array ('127.0.0.1:9200'),
  );

  $docOpts = array(
    'index' => 'logcrunch',
    'type' => 'access',
  );

  $log = "/var/log/nginx/logcrunch/access.log";

  $feeder = new \LogCrunch\Feeder($esOpts);
  $crunch = new \LogCrunch\Crunch($log);

  $pos = 0;

  while(true) {
    $doc = new \LogCrunch\Document($docOpts);
    $doc->setContent($crunch->tail($pos));
    $feeder->index($doc);
  }
}

doFeed();

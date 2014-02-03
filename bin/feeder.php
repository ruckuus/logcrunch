<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';

function doFeed() {
  $esOpts = array(
    'hosts' =>  array ('127.0.0.1:9200'),
  );

  $docOpts = array(
    'index' => 'logcrunch',
    'type' => 'access',
    'body' => '',
    'meta' => ''
  );

  $log = "/var/log/nginx/logcrunch/access.log";

  $feeder = new \LogCrunch\Feeder($esOpts);
  $crunch = new \LogCrunch\Crunch($log);
  $doc = new \LogCrunch\Document();

  while(true) {
    $doc->setOptions($docOpts);
    $doc->setContent('message', $crunch->tail());
    $feeder->index($doc);
  }
}

doFeed();

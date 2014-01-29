<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'Document.php';
require_once 'Feeder.php';

$options = array(
    'index' => 'logger',
    'type' => 'access',
);

$content = array(
    'cat' => 'access',
    'status' => '200',
    'content' => 'foo bar blah'
);

$d = new \LogCrunch\Document();
$d->setOptions($options);
$d->setContent($content);

print_r($d);

$es = array(
    'hosts' => array(
        'mongo-staging.guruestate.com:9200'
    )
);

$client = new \LogCrunch\Feeder($es);
$client->index($d);

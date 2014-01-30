<?php

namespace LogCrunch;

class Feeder {
    protected $client;
    protected $config;

    /**
     * $options = array(
     *  'host' => array(
     *          '127.0.0.1:9200'
     *          '127.0.0.1:9300'
     *      ),
     * );
     */

    public function __construct(array $option = array()) {
        $this->client = new \Elasticsearch\Client($option);
    }

    public function index(\LogCrunch\Document $doc) {
        $data = $this->parse($doc);
        $this->client->index($data);
    }

    protected function parse(\LogCrunch\Document $doc) {
        $data['index'] = $doc->getIndex();
        $data['type'] = $doc->getType();
        $data['body'] = $doc->getBody();
        return $data;
    }
}



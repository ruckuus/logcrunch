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
        $this->client = new Elasticsearch\Client($option);
    }

    public function index(Document $doc) {
        $data = $this->parse($doc);
        try {
            $this->client->index($data);
        } catch (Exception $e) {
        }
    }

    protected function parse(Document $doc) {
        $data['index'] = $doc->getIndex();
        $data['type'] = $doc->getType();
        $data['body'] = $doc->getBody();
        return $data;
    }
}



<?php
namespace LogCrunch;

class Document {
    protected $options = array(
        'index' => '',
        'type' => '',
        'body' => '',
        'meta' => '',
    );

    public function setContent($key, $content) {
        try {
            if (empty($content))
                throw new InvalidArgumentException('Could not set empty content');

            $this->options['body'] = is_array($content) ?  $content : array($key => $content);
        } catch(Exception $e) {
        }
    }

    public function setOptions(array $options = array()) {
        /* Validation Please */
        foreach($this->options as $k => $v) {
            $this->options[$k] = $options[$k];
        }
    }

    public function setMeta($key, $val) {
        $this->options['meta'][$key] = $val;
    }

    public function getIndex() {
        return $this->options['index'];
    }

    public function getType() {
        return $this->options['type'];
    }

    public function getBody() {
        return $this->options['body'];
    }

}

<?php

$version = '0.0.0-automated';
$DEFAULT_HEADERS = array('X-Client-Info' => 'postgrest-js/' . $version);

use Spatie\Url\Url;
/**
 * 'hola test'
 */

class PostgrestClient {
    public function __construct($url, $opts = []) {
        $this->url = Url::fromString($url);
        $this->headers = isset($opts) && isset($opts->headers) && array_merge($opts->headers, $DEFAULT_HEADERS);
        $this->schema =  isset($opts) && isset($opts->schema) && $opts->schema;
        $this->fetch =  isset($opts) && isset($opts->fetch) && $opts->fetch;
    }

    public function from($relation) {
        $url = $this->url->withPath($relation);

        return new PostgrestQuery($url, array(
            'headers' => $this->headers,
            'schema' => $this->schema,
            'fetch' => $this->fetch
        ));
    }

    public function rpc($fn, $args = [], $opts = []) {
        $method;
        $url = $this->url->withPath('/rpc/' . $fn);
        $body;

        if(isset($opts->head) && $opts->head) {
            $method = 'HEAD';
            foreach($args as $name => $value){
                $url->withQueryParameters([$name => strvar($value)]);
            }
        } else {
            $method = 'POST';
            $body = $args;
        }

        if(isset($opts->count) && $opts->count) {
            $this->headers['Prefer'] = 'count=' . $opts->count;
        }

        return new PostgrestFilter(array(
            'url' => $url,
            'headers' => $this->headers,
            'schema' => $this->schema,
            'fetch' => $this->fetch,
            'method' => $method,
            'body' => $body,
            'allowEmpty' => false
        ));
    }
}
<?php

use Phalcon\Cache;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Storage\SerializerFactory;

class CacheManager {    
    public function init() {
        $serializerFactory = new SerializerFactory();
        $adapterFactory    = new AdapterFactory($serializerFactory);
        
        $options = [
            'defaultSerializer' => 'Json',
            'lifetime'          => 7200
        ];
        
        $adapter = $adapterFactory->newInstance('apcu', $options);
        
        return new Cache($adapter);
    }
}
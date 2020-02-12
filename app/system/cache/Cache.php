<?php

//namespace System\Cache;
//
//use System\Config;
//use System\Cache\Service;
//use System\Cache\LocalCache;
//use System\Cache\Memcached;
//
//class Cache {
//
//
//    public function __construct(Service $cacheService) {
//        parent::__construct();
//        if (empty($this->connection)) {
//            if (Config::getInstance()->isLocalFileCacheEnable()) {
//                return LocalCache();
//            } else {
//                return Memcached();
//            }
//        }
//    }
//
//}

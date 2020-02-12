<?php

//declare(strict_types=1);
//
//namespace API\Filter;
//
//class Request {
//
//    private $flags;
//
//    public function __construct(array $flagArray) {
//        $this->flags = new ArrayObject($flagArray);
//    }
//
//    public function inspect() {
//        $filters = $this->flags->getIterator();
//
//        while ($filters->valid()) {
//            $filters->current()->check();
//            $filters->next();
//        }
//    }
//
//}

<?php

namespace API\Filter;

use System\Exception\AppException;
use System\Config;

class Authorization implements MethodTemplate  {
    /**
     *  0 = do not check request token
     *  1 = check and validate request token
     *
     * @property int $mode
     */
    private $mode;

    /**
     * Check server maintenance status
     */
    public function check() {
        $this->mode = Config::getInstance()->checkRequestTokenFlag();

        $this->validate();
    }

    /**
     * Validate and throw message exception on maintenance
     *
     * @throws AppException
     */
    public function validate() {
        if ($this->mode > 0) {
//            throw new AppException(ResultCode::UNDER_MAINTENANCE, 'system under maintenance');
        }
        return;
    }

}

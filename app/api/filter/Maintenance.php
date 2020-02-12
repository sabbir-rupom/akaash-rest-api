<?php

namespace API\Filter;

use System\Exception\AppException;
use System\Message\ResultCode;
use System\Config;

class Maintenance implements MethodTemplate  {
    /**
     * false = not in maintenance , true = in maintenance
     *
     * @property int $mode
     */
    private $mode; 

    /**
     * Check server maintenance status
     */
    public function check() {
        $this->mode = Config::getInstance()->checkMaintenance();

        $this->validate();
    }

    /**
     * Validate and throw message exception on maintenance
     * 
     * @throws AppException
     */
    public function validate() {
        if ($this->mode) {
            throw new AppException(ResultCode::UNDER_MAINTENANCE, 'system under maintenance');
        }
    }

}

<?php

(defined('APP_NAME')) or exit('Forbidden 403');

class Test_Sabbir extends BaseClass
{

    /**
     * Processing API script execution.
     */
    public function action()
    {
        return [
          'hello' => 123
        ];
    }
}

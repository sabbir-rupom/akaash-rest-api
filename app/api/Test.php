<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Config;
use System\Core\Model\Cache as CacheModel;

/**
 * Description of Test.
 *
 * @author sabbir-hossain
 *
 * @internal
 * @coversNothing
 */
class Test extends BaseClass
{

    /**
     * Processing API script execution.
     */
    public function action()
    {
        print_r($this->value);
        exit;


//        $cache = $this->config->cacheService();
//
//        $tmp_object = new stdClass;
//        $tmp_object->str_attr = 'test';
//        $tmp_object->int_attr = 123;
//
//        CacheModel::addCache($cache, 'okata', $tmp_object, 100);
//        $get_result = CacheModel::getCache($cache, 'okata');

//        Model\LogAPI::startTransaction($this->pdo);
//
//        $apiLogObj = new Model\LogAPI();
//
//        $apiLogObj->api_name = 'Test';
//        $apiLogObj->request_data = !empty($this->data) ? json_encode($this->data) : json_encode($this->get);
//        $apiLogObj->response = 123;
//        $apiLogObj->method = $this->method;
//        $id = $apiLogObj->create($this->pdo);
//
//        $result = [];
//        if ($id > 0) {
//            $result = Model\LogAPI::find($id, $this->pdo, true);
//
//            $result->delete($this->pdo);
//        }
//
//        Model\LogAPI::commit($this->pdo);

//        if (!empty($result)) {
//            $result = Model\LogAPI::findAllBy([Model\LogAPI::PRIMARY_KEY => $id], $this->pdo);
//        }

        return [
          'data' => 'found'
        ];
    }
}

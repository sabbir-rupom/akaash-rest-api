<?php

/*
 * RESTful API Template
 * 
 * A RESTful API template based on flight-PHP framework
 * This software project is based on my recent REST-API development experiences. 
 * 
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT 
 * 
 * @author	Sabbir Hossain Rupom
 * @since	Version 1.0.0
 * @filesource
 */

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Mission Status API.
 */
class ShowImage extends BaseClass {

    private $_image_type = null;

    public function __construct() {
        
    }

    public static function index($type, $id) {
        $imageObj = new ShowImage();

        $imgURL = '';
        switch ($type) {
            case 'user-profile':
                $userInfo = Model_User::find($id);
                if (empty($userInfo)) {
                    header("HTTP/1.1 400 OK");
                    exit;
                }

                $mobileDevice = FALSE;
                if (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
                    $mobileDevice = TRUE;
                } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') || strstr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
                    $mobileDevice = TRUE;
                } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'blackberry')) {
                    $mobileDevice = TRUE;
                } else if (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
                    $mobileDevice = TRUE;
                }

                $imgPath = ($mobileDevice ? System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE : System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE) . $userInfo->profile_image;
                break;

            default:
                break;
        }
        $imageObj->show_image($imgPath);
    }

    /**
     * Process execution
     */
    protected function show_image($filePath) {
        if ($filePath != '') {
            header("Content-Type: image/jpeg");
            $res = file_get_contents($filePath);
            echo $res;
        } else {
            header("HTTP/1.1 400 OK");
        }
        exit();
    }

}

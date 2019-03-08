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

class Model_FileUpload extends Model_BaseModel {

    /**
     * Process base64 formatted image upload
     * @param int $ID DB insert ID
     * @param string $binaryImage Base64 Encoded String
     * @param bool $oldImageToDelete If any existing image needed to be deleted or kept
     * @return string $imageName Return uploaded image name
     */
    public static function processBinaryImage($ID, $binaryImage, $type = '', $deleteOld = FALSE) {
        $base64_string = "data:image/png;base64," . $binaryImage;

        $curr_time = time();
        $image_prefix = $type . $ID . '_';
        $imageName = $image_prefix . $curr_time . '.png';
        $mask = $image_prefix . '*.*';

        if (!file_exists(System_Constant::UPLOAD_PROFILE_IMAGE_PATH)) {
            /*
             * If upload directory not exist, create
             */
            mkdir(System_Constant::UPLOAD_PROFILE_IMAGE_PATH, 0777, true);
        } else if ($deleteOld) {
            /*
             * Delete all previous profile image
             */
            array_map('unlink', glob(System_Constant::UPLOAD_PROFILE_IMAGE_PATH . $mask));
        }


        if (!file_exists(System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE)) {
            mkdir(System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE, 0777, true);
        } else if ($deleteOld) {
            /*
             * Delete all previous profile (mobile size) image
             */
            array_map('unlink', glob(System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE . $mask));
        }

        $outputFile = System_Constant::UPLOAD_PROFILE_IMAGE_PATH . $imageName;

        $ifp = fopen($outputFile, "wb");
        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        /*
         * Resize uploaded image for mobile view
         */
//        if ($type == 'profile') {
        $resizeTypes = System_Constant::CUSTOM_IMAGE_RESIZE_ARRAY;
        if (!empty($resizeTypes)) {
            foreach ($resizeTypes as $size) {
                $width = $size[0];
                $height = $size[1];
                self::resizeImage($outputFile, $imageName, $width, $height, $type);
            }
        }
//        }
        return $imageName;
    }

    /**
     * Process image upload from Form Post
     * @param int $ID DB row ID of table concerned
     * @param array $imageFile Upload file array
     * @param bool $oldImageToDelete if any existing image needed to be deleted or kept
     * @return string $imageName Return uploaded image name
     */
    public static function processImageUpload($ID, $imageFile, $type = '', $oldImageToDelete = TRUE) {
        $ext = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'))) {
            throw new Exception_BitCoinException(ResultCode::DATA_NOT_ALLOWED, "Improper image is provided! Only jpg and png allowed!");
        }

        $curr_time = time();
        $image_prefix = $type . $ID . '_';
        $imageName = $image_prefix . $curr_time . '.png';
        $mask = $image_prefix . '*.*';

        /*
         * If upload temp directory not exist, create
         */
        if (!file_exists(System_Constant::UPLOAD_PROFILE_IMAGE_PATH)) {
            /*
             * If upload directory not exist, create
             */
            mkdir(System_Constant::UPLOAD_PROFILE_IMAGE_PATH, 0777, true);
        } else if ($old_image_delete) {
            /*
             * Delete all previous profile image
             */
            array_map('unlink', glob(System_Constant::UPLOAD_PROFILE_IMAGE_PATH . $mask));
        }


        if (!file_exists(System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE)) {
            mkdir(System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE, 0777, true);
        } else {
            /*
             * Delete all previous profile (mobile size) image
             */
            array_map('unlink', glob(System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE . $mask));
        }

        $outputFile = System_Constant::UPLOAD_PROFILE_IMAGE_PATH . $imageName;

        if (move_uploaded_file($imageFile["tmp_name"], $outputFile)) {
            /*
             * Resize uploaded image for mobile view
             */
//            if ($type == 'profile') {
            $resizeTypes = System_Constant::CUSTOM_IMAGE_RESIZE_ARRAY;
            if (!empty($resizeTypes)) {
                foreach ($resizeTypes as $size) {
                    $width = $size[0];
                    $height = $size[1];
                    self::resizeImage($outputFile, $imageName, $width, $height);
                }
            }
//            }
        } else {
            throw new System_Exception(ResultCode::FILE_UPLOAD_ERROR, 'An error occured in system! Upload failed!');
        }

        return $imageName;
    }

    /**
     * Resizing an uploaded image
     * @param string $imageSource Source Image
     * @param string $imageName Image name to be saved
     * @param int $maxDimW Image new width 
     * @param int $maxDimH Image new height 
     * @return string $randomString 
     */
    public static function resizeImage($imageSource, $imageName, $maxDimW, $maxDimH) {
        $destinationImage = System_Constant::UPLOAD_PROFILE_IMAGE_PATH_MOBILE . $imageName;
        copy($imageSource, $destinationImage);

        $targetFilename = '';
//        if (!file_exists($destinationImage)) {
//            return FALSE;
//        }
        list($width, $height, $type, $attr) = getimagesize($destinationImage);
        if ($width > $maxDimW || $height > $maxDimH) {
            $targetFilename = $destinationImage;
            $size = getimagesize($destinationImage);
            $ratio = $size[0] / $size[1]; // width/height
            if ($ratio > 1) {
                $width = $maxDimW;
                $height = $maxDimH / $ratio;
            } else {
                $width = $maxDimW * $ratio;
                $height = $maxDimH;
            }
            $src = imagecreatefromstring(file_get_contents($destinationImage));
            $dst = imagecreatetruecolor($width, $height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

            imagejpeg($dst, $targetFilename); // adjust format as needed
        }

        if ($targetFilename == '') {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Retrieve file content type from filename
     * 
     * @param string $filename Name of file
     * @return string $mimet File Mime Type
     */
    protected static function getMimeType($filename) {
        $idx = explode('.', $filename);
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode - 1]);

        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (isset($mimet[$idx])) {
            return $mimet[$idx];
        } else {
            return NULL;
        }
    }

}

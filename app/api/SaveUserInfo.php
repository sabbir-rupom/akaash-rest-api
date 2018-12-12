<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Save user's updated information
 */
class SaveUserInfo extends BaseClass {

    // Required Login or not
    const LOGIN_REQUIRED = TRUE;

    private $user;
    private $update_user; // User information update flag

    /**
     * Verification of the request.
     */

    public function validate() {
        parent::validate();

        $this->user = $this->cache_user;
        if (empty($this->user)) {
            throw new Exception_ApiException(ResultCode::USER_NOT_FOUND, 'Session user not found!');
        }

        $this->update_user = false;
        //Entire rule: only what the value is set to be updated.

        if (!empty($this->json)) {
            if (property_exists($this->json, 'user_name')) {
                $this->user->user_name = $this->getValueFromJSON('user_name', 'string', TRUE);
                $this->update_user = true;
            }

            if (property_exists($this->json, 'profile_image')) {
                /*
                 * If image data is provided in base64 string
                 */
                $profile_image = $this->getValueFromJSON('profile_image', 'string'); // nullable
                if (!empty($profile_image)) {
                    if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $profile_image)) { // checking if data is in base64 formatted or not
                        $this->user->profile_image = $this->process_binary_image($this->userId, $profile_image, 'profile');
                        $this->update_user = true;
                    }
                }
            }

            if (property_exists($this->json, 'longitude')) {
                $this->user->longitude = $this->getValueFromJSON('longitude', 'string', TRUE);
                $this->update_user = true;
            }

            if (property_exists($this->json, 'latitude')) {
                $this->user->latitude = $this->getValueFromJSON('latitude', 'string', TRUE);
                $this->update_user = true;
            }

            if (property_exists($this->json, 'gender')) {
                $this->user->gender = $this->getValueFromJSON('gender', 'string');
                if (empty($this->user->gender) || $this->user->gender != 'female') {
                    $this->user->gender = 'male';
                }
                $this->update_user = true;
            }

            if (property_exists($this->json, 'first_name')) {
                $this->user->first_name = $this->getValueFromJSON('first_name', 'string'); // nullable
                $this->update_user = true;
            }
            if (property_exists($this->json, 'last_name')) {
                $this->user->last_name = $this->getValueFromJSON('last_name', 'string'); // nullable
                $this->update_user = true;
            }

            if (property_exists($this->json, 'personal_info')) {
                $this->user->personal_info = $this->getValueFromJSON('personal_info', 'string'); // nullable
                $this->update_user = true;
            }

            if (property_exists($this->json, 'email')) {
                $email = $this->getValueFromJSON('email', 'string', TRUE);
                If (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Email is not valid.
                    throw new Exception_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "Invalid email address");
                }
                $this->user->email = $email;
                $this->update_user = true;
            }

            if (property_exists($this->json, 'new_password')) {
                $newPassword = $this->getValueFromJSON('new_password', 'string', TRUE);
                $oldPassword = $this->getValueFromJSON('old_password', 'string', TRUE);
                /*
                 * Verify old password match with user's current password
                 */
                if (password_verify($oldPassword, $this->user->password) === FALSE) {
                    throw new Exception_ApiException(ResultCode::PASSWORD_MISMATCHED, 'Current password does not match');
                }

                $this->user->password = password_hash(trim($newPassword), PASSWORD_BCRYPT, array('cost' => 12));
                $this->update_user = true;
            }
        } else if (!empty($_FILES)) {
            /*
             * Upload process of form-data image
             */
            $profile_image = '';
            
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
                $profile_image = $_FILES['profile_image'];
            } else {
                throw new Exception_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "profile_image is not set.");
            }
            
            $this->user->profile_image = $this->process_image_upload($this->userId, $profile_image, 'profile');
            $this->update_user = true;

        }
    }

    /**
     * Process execution
     */
    public function action() {
        if ($this->update_user) {
            $this->user->update($this->pdo);
            Model_User::setCache(Model_CacheKey::getUserKey($this->user->id), $this->user);
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_Util_DateUtil::getToday(),
            'data' => array(
                'user_info' => $this->user->toJsonHash()
            ),
            'error' => []
        );
    }

}

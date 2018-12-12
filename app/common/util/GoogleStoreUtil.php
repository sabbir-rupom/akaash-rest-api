<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Google AppStore Util
 */
class Common_Util_GoogleStoreUtil {

    const ACCESS_TOKEN_INFO_CACHE_EXPIRE_TIME = 86400; // 1day
    const ACCESS_TOKEN_INFO_CACHE_KEY = "google_play_access_token_info";
    const SAFE_TIME = 900; // 15min
    const RETRY_LIMIT = 5;
    const BACKOFF_INITIAL_DELAY = 1000; // 1sec
    const MAX_BACKOFF_DELAY = 512000;
    const API_TIMEOUT_DELAY = 10000000; // 10sec

    /**
     * Get In Application Purchase Status. (For user.)
     *
     * @param  string $product_id		Product ID from Google Play Console
     * @param  string $purchase_token	Purchase token from Google InApp Billing Library
     * @return int    Purchase Status
     *         -2		Retry limit
     *         -1		Unexpected error
     *          0		Success
     *          400		Error token
     */

    public static function getInAppPurchaseStatus($product_id, $purchase_token) {

        // Get Access token
        $access_token = self::getAccessToken();

        // Get Google play purchase status
        $attempt = 0;
        $backoff = self::BACKOFF_INITIAL_DELAY;
        $response = null;
        $ch = null;
        while (TRUE) {

            $attempt++;
            if ($attempt > self::RETRY_LIMIT) {
                $response = -2;
                break;
            }

            $config = Common_Util_ConfigUtil::getInstance();

            // Get Response array
            list($response_array, $ch) = self::curlGooglePlayPurchaseStatus($config->getPackageName(), $product_id, $purchase_token, $access_token, $ch);

            // Success Response
            if (isset($response_array['purchaseState'])) {
                $response = $response_array['purchaseState'];
                break;
            }
            // Error Resposne
            else if (isset($response_array['error']) && isset($response_array['error']['code'])) {
                if ($response_array['error']['code'] === 503) {
                    continue;
                }
                // Purchase token error = 400
                $response = $response_array['error']['code'];
                break;
            }
            // Unexpected error
            else {
                $response = -1;
                break;
            }

            // Wait retry
            $sleep_time = $backoff / 2 + rand(0, $backoff);
            usleep($sleep_time);

            // Update Back off
            $backoff *= 2;
            if ($backoff > self::MAX_BACKOFF_DELAY) {
                $backoff = self::MAX_BACKOFF_DELAY;
            }
        }

        // Close Curl
        curl_close($ch);

        // Return response
        return $response;
    }

    /**
     * Get Access Token from cache.
     *
     */
    private static function getAccessToken() {

        $memcache = Common_Util_KeyValueStoreUtil::getGoogleAccessTokenMemcachedClient();
        $access_token_info = $memcache->get(self::ACCESS_TOKEN_INFO_CACHE_KEY);

        if ($access_token_info) {
            if ($access_token_info['access_token']) {
                return $access_token_info['access_token'];
            }
        }
        throw new Exception("Access token is not found.");
    }

    /**
     * Refresh Access Token Info. (Not for user. Batch only.)
     *
     * @return boolean Refresh result
     */
    public static function refreshAccessTokenInfo() {

        // Get from Cache
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $access_token_info = $memcache->get(self::ACCESS_TOKEN_INFO_CACHE_KEY);
        if ($access_token_info) {
            if ($access_token_info['access_token'] && $access_token_info['expiration_time'] > time()) {
                return FALSE; // No expiration. No Refresh.
            }
        }

        $config = Common_Util_ConfigUtil::getInstance();

        // Refresh Access token from API
        $response_array = self::curlRefreshedAccessToken($config->getClientId(), $config->getClientSecret(), $config->getRefreshToken());
        if (!isset($response_array['access_token'])) {
            throw new Exception("Access token is not found.");
        }
        $new_access_token = $response_array['access_token'];
        $new_expiration_time = $response_array['expires_in'] + time() - self::SAFE_TIME;
        $access_token_info = array("access_token" => $new_access_token, "expires_in" => $response_array['expires_in'], "expiration_time" => $new_expiration_time);
        $memcache->set(self::ACCESS_TOKEN_INFO_CACHE_KEY, $access_token_info, MEMCACHE_COMPRESSED, self::ACCESS_TOKEN_INFO_CACHE_EXPIRE_TIME);
        return $access_token_info; // Refreshed
    }

    /**
     * Set Access Token Info. (Not for user.)
     *
     * @param array $access_token_info Access Token Info
     */
    public static function setAccessTokenInfo($access_token_info) {
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        return $memcache->set(self::ACCESS_TOKEN_INFO_CACHE_KEY, $access_token_info, MEMCACHE_COMPRESSED, self::ACCESS_TOKEN_INFO_CACHE_EXPIRE_TIME);
    }

    /**
     * Delete Access Token Info. (Not for user.)
     */
    public static function deleteAccessTokenInfo() {
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        return $memcache->delete(self::ACCESS_TOKEN_INFO_CACHE_KEY);
    }

    /**
     * cURL - Google Play Purchase Status Request.
     *
     * @param  string $package_name		Package Name
     * @param  string $product_id		Product ID
     * @param  string $purchase_token	Purchase Token
     * @param  string $request_url		Request URL
     * @param  object $ch				cURL Object
     * @return array  Purchase Status Response
     */
    private static function curlGooglePlayPurchaseStatus($package_name, $product_id, $purchase_token, $access_token, $ch = null) {
        $request_url = 'https://www.googleapis.com/androidpublisher/v1.1/applications/' . $package_name .
                '/inapp/' . $product_id . '/purchases/' . $purchase_token . '?access_token=' . $access_token;
        if ($ch == null) {
            $ch = curl_init($request_url);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, self::API_TIMEOUT_DELAY);
        }
        $response_json = curl_exec($ch);
        $err_no = curl_errno($ch);
        $err_msg = curl_error($ch);
        if ($err_no != 0) {
            throw new Exception($err_msg, $err_no);
        }
        return array(json_decode($response_json, TRUE), $ch);
    }

    /**
     * cURL - Refreshed Access Token Request.
     *
     * @param  string $client_id		Client ID
     * @param  string $client_secret	Client Secret
     * @param  string $refresh_token	Refresh Token
     * @return array  Refreshed Access Token Info
     */
    private static function curlRefreshedAccessToken($client_id, $client_secret, $refresh_token) {
        $ch = curl_init('https://accounts.google.com/o/oauth2/token');
        $params['grant_type'] = 'refresh_token';
        $params['client_id'] = $client_id;
        $params['client_secret'] = $client_secret;
        $params['refresh_token'] = $refresh_token;
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response_json = curl_exec($ch);
        $err_no = curl_errno($ch);
        $err_msg = curl_error($ch);
        curl_close($ch);
        if ($err_no != 0) {
            throw new Exception($err_msg, $err_no);
        }
        return json_decode($response_json, TRUE);
    }

}

<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Apple AppStore Util
 */
class Common_Util_AppStoreUtil {

    /**
     * Verify a receipt and return receipt data
     * @param  string $receipt Base-64 encoded data
     * @param  bool $isProduction Optional. False if verifying a test receipt
     * @throws  Exception If the receipt is invalid or cannot be verified
     * @return  array Receipt info (including product ID and quantity)
     */
    public static function getReceiptData($receipt, $isProduction = false) {
        // determine which endpoint to use for verifying the receipt
        if ($isProduction) {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        } else {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        }

        //Common_Util_LogUtil::getApplicationLogger()->log($endpoint, Zend_Log::DEBUG);
        // build the post data
        $postData = json_encode(array('receipt-data' => $receipt));
        // create the cURL request
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        // execute the cURL request and fetch response data
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        // ensure the request succeeded
        if ($errno != 0) {
            throw new Exception($errmsg, $errno);
        }

        //Common_Util_LogUtil::getApplicationLogger()->log($response, Zend_Log::DEBUG);
        // parse the response data
        $data = json_decode($response);
        // ensure response data was a valid JSON string
        if (!is_object($data)) {
            throw new Exception('Invalid response data');
        }
        //status = 21007 Inquiry to the test server in the case of
        if (($isProduction == TRUE) && isset($data->status) && $data->status == 21007) {
            return Common_Util_AppStoreUtil::getReceiptData($receipt, false);
        }

        // ensure the expected data is present
        if (!isset($data->status) || $data->status != 0) {
            throw new Exception('Invalid receipt');
        }
        // build the response array with the returned data
        return array(
            'item_id' => $data->receipt->item_id,
            'original_transaction_id' => $data->receipt->original_transaction_id,
            'bvrs' => $data->receipt->bvrs,
            'product_id' => $data->receipt->product_id,
            'purchase_date' => $data->receipt->purchase_date,
            'quantity' => $data->receipt->quantity,
            'bid' => $data->receipt->bid,
            'original_purchase_date' => $data->receipt->original_purchase_date,
            'transaction_id' => $data->receipt->transaction_id
        );
    }

}

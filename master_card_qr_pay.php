<?php
session_id() ? session_start() : null;
/**
 * Generating  ECOBANK, MasterCard & VISA QR codes for payments
 * 
 * @category Payments
 * @package  MasterCardQR
 * @author Blessed Jason Mwanza | @mwanzabj
 * @license  https://opensource.org/licenses/MIT
 * @link     https://github.com/blessedjasonmwanza/Payments-MiddleWare
 * @knowlegebase (FULL API DOCUMENTATION) - https://documenter.getpostman.com/view/9576712/Szmb6epq#22e33b07-46c8-47f9-8744-37ca05c3ba2f
 * 
 * --CURRENT EXISTING METHODS
 * 
 *  htt_post() /make http_post request
 *  
 *  generate_token() //generates a token on each class instantiation
 * 
 *  generate_secure_hash($payload) //generates SHA-512 hash 
 *  
 *  validate_hash($payload_body, $hash) //Checks for Hash integrity
 *  
 *  setup_merchant(body) //This is the stage at which a terminal ID is generated for use in generating final QR image
 *  get_qr_code($body) // returns generated QR image
 * 
 */
class MasterCardQrPay{
    var $merchant_id; 
    var $merchant_password;
    var $token;
    var $header_with_token;
    var $api_origin = 'https://developer.ecobank.com';
    var $qr_creation_response;
    var $dynamic_qr_response;
    var $last_error;
    var $private_lab_key;
    var $terminal_id = false;
    var $qr_image_response;
    var $qr_code;
    function __construct($merchant_id, $merchant_password, $private_lab_key){
        $this->merchant_id = $merchant_id;
        $this->merchant_password = $merchant_password;
        $this->private_lab_key = $private_lab_key;
        $this->generate_token();
    }
    private function http_post($endpoint_path, $header, $body_fields){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->api_origin.$endpoint_path,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>$body_fields,
          CURLOPT_HTTPHEADER => is_array($header) ? $header : (array)$header,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    // GENERATE TOKEN
    private function generate_token(){
        try {
            $endpoint = '/corporateapi/user/token';            
            $header = array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Origin: developer.ecobank.com'
            );
            $body = '{
                "userId": "'.$this->merchant_id.'",
                "password": "'.$this->merchant_password.'"
            }';
            $response = $this->http_post($endpoint, $header, $body);
            try {
                $response_array = (array)json_decode($response);
                if(array_key_exists("token", $response_array)){
                    $this->token = $response_array['token'];
                    $this->header_with_token = array(
                        'Authorization: Bearer '.$response_array["token"].'',
                        'Content-Type: application/json',
                        'Accept: application/json',
                        'Origin: developer.ecobank.com'
                    );
                }else{
                    $_SESSION['get_token_response'] = $response_array;
                    $this->last_error = json_encode(error_get_last(), true);
                    return false;
                }
            } catch (\Throwable $th) {
                $this->last_error = strval($th);
            return false;
            }
        } catch (\Throwable $th) {
            $this->last_error = strval($th);
            return false;
        }
    }
    function generate_secure_hash($payload){
        // $secure_hash =  JWT::encode($payload, $private_lab_key, "HS512");
        $secure_hash = hash_hmac('sha512', $payload, $this->private_lab_key);
        return $secure_hash;
    }
    function validate_hash($payload_body, $hash){
        $endpoint = '/corporateapi/merchant/securehash';
        $header = $this->header_with_token;
        $body  = '{
            '.rtrim($payload_body).',
            "secureHash": "'.$hash.'"
        }';
        return $this->http_post($endpoint, $header, $body);
    }
      // setup_merchant (merchant qr creation)
    function setup_merchant($body){
        try {
            $endpoint = "/corporateapi/merchant/createqr";
            $header = $this->header_with_token;
            $response = $this->http_post($endpoint, $header, $body);
            try {
                $qr_creation_response = (array)(json_decode($response, true));
                $this->qr_creation_response = $qr_creation_response;
                if(array_key_exists('response_content', $qr_creation_response)){
                    $this->terminal_id = $qr_creation_response["response_content"]["terminalId"];
                    return $qr_creation_response;
                }else{
                    $_SESSION['create_qr_response'] = $qr_creation_response;
                    $this->last_error = json_encode(error_get_last(), true);
                    return false;
                }
            } catch (\Throwable $th) {
                $this->last_error = strval($th);
                return false;
            }
        } catch (\Throwable $th) {
            $this->last_error = strval($th);
            return false;
        }
    }
    // GET PAYMENT QR IMAGE
    function get_qr_code($body){
        try{
            if(isset($this->qr_creation_response)){
                $endpoint = "/corporateapi/merchant/qr";
                $header = $this->header_with_token;
                $response = $this->http_post($endpoint, $header, $body);
                try {
                    $response = (array)(json_decode($response, true));
                    $this->qr_image_response = $response;
                    $qr = 'data:image/png;base64,';
                    $this->dynamic_qr_response = $response;
                    if(array_key_exists("response_content", $response)){
                        $this->qr_code = $qr.$response['response_content']['dynamicQRBase64'];
                        return $qr.$response['response_content']['dynamicQRBase64'];
                    }else{
                        $this->last_error = json_encode(error_get_last(), true);
                        return false;
                    }
                } catch (\Throwable $th) {
                    $this->last_error = strval($th);
                    return false;
                }
            }else{
                $this->last_error = "It seems like QR Image could not be generated. Check qr_image_response.";
                return false;
            }
        } catch (\Throwable $th) {
            $this->last_error = strval($th);
            return false;
        }
    }
}
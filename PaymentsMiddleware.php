<?php
require 'php-jwt-5.2.1/src/JWT.php';
use \Firebase\JWT\JWT;

class PaymentsMiddleware{
    protected $private_key;
    public $config = [];
    var $payload;
    var $response;
    var $needs_payload = ['sparco'];
    protected $api_endpoints = [
        "sparco" => [
            "collect" => "https://live.sparco.io/gateway/api/v1/momo/debit"
        ]
    ];
    public function __construct($private_key_token){
        $this->config["private_key"] = $private_key_token;
    }
    function config($settings){
        try {
            if(is_array($settings)){
                foreach ($settings as $key => $value){
                    $this->config[$key] = $value;
                }
            }else{
                return $this->error(0, "Config settings must be an array", error_get_last(), "array");
            }
        } catch (\Throwable $th) {
            return $this->error(0, "Config Error", ["catch" => (array)$th, "last_error" => error_get_last()], "array");
        }
    }
    // post function just like $.post for jquery... now in PHP :-)
    function http_post($url, $data, $headers = null, $content_type = null){
        switch ($content_type) {
            case 'xml':
                $post_fields = '<?xml version="1.0" encoding="utf-8"?>
                '.$data.'
                ';
                $content_type = 'Content-Type: application/xml';
                break;
            case 'json':
                $post_fields = json_encode($data, true);
                // echo 'post fields'.$post_fields;
                $content_type = 'Content-Type: application/json';
                break;
            default:
                http_build_query($data);
                $content_type = "Content-Type: application/x-www-form-urlencoded";
                break;
        }
        $curl = curl_init();
        curl_setopt_array($curl, 
            array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$post_fields,
            CURLOPT_HTTPHEADER => array(
                $content_type,
                $headers,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
        
    }
    /**
     * GENERATES PAYLOADS
     */
    function generate_payload($provider){
        switch ($provider) {
            case 'sparco':
                try {
                    $new_payload = array(
                        "amount"=> $this->config['amount'],
                        "currency"=> $this->config['currency'],
                        "customerFirstName"=> $this->config['client_first_name'],
                        "customerLastName"=> $this->config['client_last_name'],
                        "customerEmail"=> $this->config['client_email'],
                        "merchantPublicKey"=> $this->config['public_key'],
                        "transactionName"=> $this->config['transaction_description'],
                        "transactionReference"=> $this->config['transaction_reference'],
                        "wallet"=> $this->config['deduct_from'],
                        "chargeMe"=> $this->config['charge_client']
                    );
                    $payload = JWT::encode($new_payload, $this->private_key);
                    $this->payload = $payload;
                    // echo $payload;
                    $this->response = [
                        "code" => 1,
                        "payload" => $this->payload,
                    ];
                } catch (\Throwable $th) {
                    $this->response = $this->error(0, "Payload generation failed", ["catch" => (array)$th, error_get_last()], "array");
                }
                break;
            default:
            $this->response = $this->error(404, "invalid payment provider", []);
                break;
        }
        return $this->response;
    }
    /* returns PaymentsMiddleware Standard error message
        Not yet standardized
    */

    function error($code = 0, $msg="Invalid error", $debug = [], $data_type = "array" ){
        $response = [
            "code" => $code,
            "msg" => $msg,
            "debug" => $debug
        ];
        switch ($data_type) {
            case 'array':
                $response = $response;
                break;
            case 'json':
                 $response = json_encode($response, true);
                break;
            
            default:
            $response = false;
                break;
        }
        return $response;

    }
    /**
     * MOBILE MONEY
     */
    function mobile_money($action){
        $this->action = $action;
        try {
            if(array_key_exists('use', $this->config) && isset($this->config['use'])){
                
                $provider = $this->config['use'];
                if(in_array($provider, $this->needs_payload)){
                    $this->generate_payload($provider);
                    if($this->action === "collect"){
                        switch ($provider) {


                            // DO NOT TOUCH THIS, I'M STILL WORKING ON IT

                            case 'sparco':
                                try {
                                    $headers = 'X-PUB-KEY: '.$this->config['public_key'];
                                    $data = ["payload" => $this->payload];
                                    $url = $this->api_endpoints[$provider][$this->action];
                                        $response = $this->http_post($url, $data, $headers, 'json');
                                    break;
                                } catch (\Throwable $th) {
                                    $response =  $this->error(0, $error->getMessage(), ["catch" => (array)$th, error_get_last()], "array");
                                }
                            default:
                                $response =  $this->error(0, "Provider not yet added to this function.", error_get_last(), "array");
                                break;
                        }
                        $this->response = $response;
                        header("Content-Type: application/json");
                        return $this->response;
                    }else{
                        $this->response = $this->error(0, "We're still working on other functionalities under this provider.", error_get_last(), "array");
                        header("Content-Type: application/json");
                        return $this->response;
                    }
                
                }else{
                    // provider does not need payload for request
                    throw new Exception("Non payload providers still under development", 1);
                }
                
            }else{
                throw new Exception("Provider is missing", 1);
            }
        } catch (\Exception $error) {
            $response = $this->error(0, $error->getMessage(), [$this->config], "array");
            $this->response = $response;
            header("Content-Type: application/json");
            return $this->response;
        }
    }
}
?>
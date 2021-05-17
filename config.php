<?php
class PaymentsMiddleware{
    protected $private_key;
    public $config = [];
    protected $payload;
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
    function httpPost($url, $data, $headers = null, $content_type = null){
        switch ($content_type) {
            case 'xml':
                $post_fields = '<?xml version="1.0" encoding="utf-8"?>
                '.$data.'
                ';
                $content_type = 'Content-Type: application/xml';
                break;
            case 'json':
                $post_fields = json_encode($data, true);
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
}
?>
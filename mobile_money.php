<?php
require 'php-jwt-5.2.1/src/JWT.php';
use \Firebase\JWT\JWT;

public class MobileMoney extends PaymentsMiddleware{
    function __construct(){
        try {
            if(array_key_exists('use', $this->config) && isset($this->config['use'])){
                $provider = $this->config['use'];
                if(in_array($provider, $this->needs_payload)){
                    generatePayLoad($provider);
                    if($this->action === "collect"){
                        switch ($provider) {
    
    
                            // DO NOT TOUCH THIS, I'M STILL WORKING ON IT
    
                            case 'sparco':
                                $headers = 'X-PUB-KEY: '.$this->config['public_key'];
                                $data = ["payload" => $this->payload];
                                $url = $this->$api_endpoints[$provider][$this->action];
                                    return $this->httpPost($url, $data, $headers, 'json');
                                break;
                            default:
                                return $this->error(0, "We're still working on other functionalities under this provider.", error_get_last(), "array");
                                break;
                        }
                    }
                
                }else{
                    // provider does not need payload for request
                    throw new Exception("Non payload providers still under development", 1);
                }
                
            }else{
                throw new Exception("Provider is missing", 1);
            }
        } catch (\Exception $error) {
            $this->error(0, $error->getMessage(), error_get_last(), "array");
        }
    }
    function generatePayLoad($provider){
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
                    $this->payload = JWT::encode($new_payload, $this->$private_key);
                    $response = [
                        "code" = 1,
                        "payload" => $this->payload,
                    ];
                } catch (\Throwable $th) {
                    $response = $this->error(0, "Payload generation failed", ["catch" => (array)$th, error_get_last()], "array");
                }
                break;
            default:
                $response = $this->error(404, "invalid payment provider", []);
                break;
        }
        return $response;
    }
}


?>
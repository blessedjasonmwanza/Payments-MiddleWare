<?php

class MobileMoney extends PaymentsMiddleware{
    function __construct($action){
        $this->action = $action;
        var_dump($this->config);
                echo '<hr>';
        try {
            if(array_key_exists('use', $this->config) && isset($this->config['use'])){
                
                $provider = $this->config['use'];
                if(in_array($provider, $this->needs_payload)){
                    generatePayLoad($provider);
                    if($this->action === "collect"){
                        switch ($provider) {
    
    
                            // DO NOT TOUCH THIS, I'M STILL WORKING ON IT
    
                            case 'sparco':
                                try {
                                    $headers = 'X-PUB-KEY: '.$this->config['public_key'];
                                    $data = ["payload" => $this->payload];
                                    $url = $this->$api_endpoints[$provider][$this->action];
                                        $response = $this->httpPost($url, $data, $headers, 'json');
                                    break;
                                } catch (\Throwable $th) {
                                    $response =  $this->error(0, $error->getMessage(), ["catch" => (array)$th, error_get_last()], "array");
                                }
                            default:
                                $response =  $this->error(0, "Provider not yet added to this function.", error_get_last(), "array");
                                break;
                        }
                        $this->response = $response;
                        return $this->response;
                    }else{
                        $this->response = $this->error(0, "We're still working on other functionalities under this provider.", error_get_last(), "array");
                        return $this->response;
                    }
                
                }else{
                    // provider does not need payload for request
                    throw new Exception("Non payload providers still under development", 1);
                }
                
            }else{
                print_r($this->config);
                throw new Exception("Provider is missing", 1);
            }
        } catch (\Exception $error) {
           $response = $this->error(0, $error->getMessage(), [$this->config], "array");
           $this->response = $response;
            return $this->response;
        }
    }
    
}


?>
<?php
/**
 * @category Payments
 * @package  PaymentsMiddleware
 * @author   Blessed Jason Mwanza <mwanzabj@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @link     https://github.com/blessedjasonmwanza/Payments-MiddleWare
 */
    
    /** FEATURE INFO
     * Deduct/accept instant payments from all Zambian Networks (ZAMTEL, AIRTEL, MTN)
     */
    class PMMobileMoneyZM{
        var $public_key;
        var $private_key;
        var $debit_url;
        var $payment_verification_url;
        var $currency = "ZMW";
        function __construct($private_key, $public_key, $mode="live"){
            $this->public_key = $public_key;
            $this->private_key = $private_key;
            $this->debit_url =  = "https://".$mode.".sparco.io/gateway/api/v1/momo/debit";
            $this->payment_verification_url = "https://".$mode.".sparco.io/gateway/api/v1/transaction/query?reference=";
        }
        function http_post($method="POST", $url, $headers=null, $body_fields=null){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "$method",
                CURLOPT_POSTFIELDS => $body_fields,
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
        function request_payment($first_name, $last_name, $email, $amount, $wallet_phone_number, $description){
            $transaction_reference = $wallet_phone_number.'_'.time();
            $payload_fields = array(
                "amount"=> $amount,
                "currency"=> "$this->currency",
                "customerFirstName"=> "$first_name",
                "customerLastName"=> "$last_name",
                "customerEmail"=> "$email",
                "merchantPublicKey"=> "$this->public_key",
                "transactionName"=> "$description",
                "transactionReference"=> "$transaction_reference",
                "wallet"=> "$wallet_phone_number",
            );
            
            $payload = JWT::encode($payload_fields, $this->private_key);
            $request_body_fields = '{
                "payload":"'.$payload.'"
            }';
            $request_headers = array(
                'X-PUB-KEY: '.$this->public_key,
                'Content-Type: application/json'
            )
            return json_decode($this->http_post("POST", $this->debit_url, $request_headers, $request_body_fields), true);
        }
        function get_token(){
            $payload = array(
                "pubKey"=> "$this->public_key"
            );
            return JWT::encode($payload, $this->private_key);
        }
        function verify_payment($reference){
            $url = $this->payment_verification_url.$reference;
            $request_headers = array(
                'token: '.$this->get_token()
            );
            return json_decode($this->http_post("GET", $this->debit_url, $request_headers, $request_body_fields), true);
        }
    }
?> 
<?php
require 'php-jwt-5.2.1/src/JWT.php';
require 'PaymentsMiddleware.php';
// setup:  usually once off
$private_key = "08109a2d75f3492a8637f83dc44689f7";
$public_key = "47c0cca63d5342d4bb403aaa060f6128";
$mobile_money = new PMMobileMoneyZM($private_key, $public_key);
$mobile_money->currency = "ZMW";

// customer details
$first_name = "Blessed Jason";
$last_name = "Mwanza";
$email = "mwanzabj@gmail.com";
$amount = 0.00;
$wallet_phone_number = "0971943638";
$description = "";

// Request payment
$payment_response = $mobile_money->request_payment($first_name, $last_name, $email, $amount, $wallet_phone_number, $description);
$response_message = array_key_exists("massage", $payment_response) ? $payment_response['massage'] : $payment_response['message'];
if(array_key_exists("isError", $payment_response) && $payment_response['isError'] === false){
    //success
    $reference = $payment_response['reference'];
    echo json_encode($payment_response, true);
}else{
    //failure
    echo json_encode($payment_response, true);
}
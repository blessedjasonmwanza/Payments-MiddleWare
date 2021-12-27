<?php
require 'php-jwt-5.2.1/src/JWT.php';
require 'PaymentsMiddleware.php';
// setup:  usually once off
$private_key = "";
$public_key = "";
$mobile_money = new PMMobileMoneyZM($private_key, $public_key);
$mobile_money->currency = "ZMW";

// customer details
$first_name = "";
$last_name = "";
$email = "";
$amount = 0.00;
$wallet_phone_number = "";
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
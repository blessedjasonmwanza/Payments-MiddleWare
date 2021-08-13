<?php
require 'php-jwt-5.2.1/src/JWT.php';
require 'PaymentsMiddleware.php';
// setup
$private_key = "";
$public_key = "";
$currency = "ZMW";
$mobile_money = new PMMobileMoneyZM($private_key, $public_key);
$mobile_money->currency = $currency;

// customer details
$first_name = "";
$last_name = "";
$email = "";
$amount = 0.00;
$wallet_phone_number = "";

// Request payment
$payment_response = $mobile_money->request_payment($first_name, $last_name, $email, $amount, $wallet_phone_number);
$response_message = array_key_exists("massage", $payment_response) ? $payment_response['massage'] : $payment_response['message'];
if(array_key_exists("isError", $payment_response) && $payment_response['isError'] === false){
    //success
    $reference = $payment_response['reference'];
    echo json_encode($payment_response, true);
}else{
    //failure
    echo json_encode($payment_response, true);
}

/**
 * VERIFY TRANSACTION
 */

$verification_response = $mobile_money->verify_payment($reference);
$response_message = array_key_exists("massage", $verification_response) ? $verification_response['massage'] : $verification_response['message'];
if(array_key_exists("isError", $verification_response) && $verification_response['isError'] === false){
    //success
    echo json_encode($verification_response, true);
}else{
    //failure
    echo json_encode($verification_response, true);
}
<?php
require 'php-jwt-5.2.1/src/JWT.php';
require 'PaymentsMiddleware.php';
// setup usually once
$private_key = "";
$public_key = "";
$mobile_money = new PMMobileMoneyZM($private_key, $public_key);
$mobile_money->currency = "ZMW";
/**
 * VERIFY TRANSACTION
 */

$verification_response = $mobile_money->verify_payment($reference);
$response_message = array_key_exists("massage", $verification_response) ? $verification_response['massage'] : $verification_response['message'];
if(array_key_exists("isError", $verification_response) && $verification_response['isError'] === false){
    //success
    echo json_encode($verification_response, true);
    // you can proceed to checkout
}else{
    //failure
    echo json_encode($verification_response, true);
}
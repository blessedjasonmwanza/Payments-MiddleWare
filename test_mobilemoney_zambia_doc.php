<?php
/**
 * CURRENTLY, YOU CAN USE THIS PAYMENT GATEWAY WHEN YOU HAVE AN ACCOUNT WITH SPARCO GATEWAY. 
 * 
 */
// require JWT and PaymentsMiddleWare files
require 'php-jwt-5.2.1/src/JWT.php';
require 'PaymentsMiddleware.php';
// setup and configure your account
$private_key = "";//your sparco private key
$public_key = ""; //your sparco public (pub) key
$currency = "ZMW"; //Transaction currency (Optional)
$mobile_money = new PMMobileMoneyZM($private_key, $public_key);
$mobile_money->currency = $currency; // (optional) 

/**
 * TO START AND TRIGGER PAYMENT, USE THE CODE BELOW
 */

// setup customer account details
$first_name = ""; //customer first name e.g John
$last_name = ""; //customer last name e.g Smith
$email = ""; //customer email. e.g johnsmith@website.com
$amount = 0.00; //Amount to be deducted from customer phone number mobile money wallet
$wallet_phone_number = ""; //customer mobile money number NOTE: it must be 10 characters only

// Request and Trigger Payment for customer to confirm
// A prompt confirmation will appear on customers phone to confirm payment
$payment_response = $mobile_money->request_payment($first_name, $last_name, $email, $amount, $wallet_phone_number); //returns response array
$response_message = array_key_exists("massage", $payment_response) ? $payment_response['massage'] : $payment_response['message']; //readable response message
if(array_key_exists("isError", $payment_response) && $payment_response['isError'] === false){
    $reference = $payment_response['reference'];
    
    // Payment triggered wait for user to confirm the you can use the code example below to confirm...
    // if transaction was paid or not.
    echo json_encode($payment_response, true);
}else{
    //payment failed to be initiated/triggered
    echo json_encode($payment_response, true);
}

/**
 * TO VERIFY IF TRANSACTION WAS SUCCESSFUL, USE THE CODE BELOW with reference code from above code
 */

$verification_response = $mobile_money->verify_payment($reference);  // verification response is an array
$response_message = array_key_exists("massage", $verification_response) ? $verification_response['massage'] : $verification_response['message'];
if(array_key_exists("isError", $verification_response) && $verification_response['isError'] === false){
    //payment was confirmed && account deducted successfully
    echo json_encode($verification_response, true);
}else{
    //payment failed or deduction failed
    //echo response 
    echo json_encode($verification_response, true);
}
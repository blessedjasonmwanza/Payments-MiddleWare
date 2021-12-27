# Payments-MiddleWare (PHP8+)
This API MiddleWare contains simplified versions of on-demand payments API's. From local Mobile Money to Visa &amp; Credit Card integrations :-)

__*Let's simplify online payments for everyone! ... Feel Free to Contribute *__
>
 - Africa
 - The World

## Currently in Development
 - *MasterCard, VISA, ECOBANK QR CODE payments*
    ### Status
    - Unstable ([Alpha] Ready for Testing.)

## LIVE & READY FOR USE
 - *Sparco* // for AIRTEL, MTN, & ZAMTEL  Instant mobile Money collections
 # SETUP
 - Create your account [here](https://gateway.sparco.io/) then, go to ```Settings``` tab in order to obtain both your public and private keys
 - For references on how to use Mobile Money Payments, Check **[test_mobilemoney_zambia_doc.php](https://github.com/blessedjasonmwanza/Payments-MiddleWare/blob/php8/test_mobilemoney_zambia_doc.php)**, or **[test_mobilemoney_zambia.php](https://github.com/blessedjasonmwanza/Payments-MiddleWare/blob/php8/test_mobilemoney_zambia.php)**
<hr>

# <center>Documentation</center>

## Request Payment
> Require or include the following files on top of your script

```php
<?php
require 'php-jwt-5.2.1/src/JWT.php';
require 'PaymentsMiddleware.php';

// setup:  usually once off
//paste your private key obtained from sparco dashboard settings section here
$private_key = "";
//paste your public key obtained from sparco dashboard settings section here
$public_key = ""; 
// instantiate or initialize the Middleware library into a variable
$mobile_money = new PMMobileMoneyZM($private_key, $public_key); 
// set currency. Default is ZMW for Zambian Kwacha
$mobile_money->currency = "ZMW";//paste your public key obtained from sparco dashboard settings section here


// customer details
/**
 * These details are required when a customer is purchasing a product from your platform
 * They need to much each respective transaction details
 * */

$first_name = "";
$last_name = "";
$email = "";
$amount = 0.00;
// Wallet phone number is the mobile line where you'll be deducting funds/money from. In this case being the customers preferred mobile money number
$wallet_phone_number = "";
// Transaction description is optional, but can include details about products being purchased
$description = ""; 

// Request payment and assign response into a variable
$payment_response = $mobile_money->request_payment($first_name, $last_name, $email, $amount, $wallet_phone_number, $description);
// Check if message key exist in the response array and assign the response into a variable
// Sometimes you do get a response with a typo in the message array hence the ternary check below
$response_message = array_key_exists("massage", $payment_response) ? $payment_response['massage'] : $payment_response['message'];

if(array_key_exists("isError", $payment_response) && $payment_response['isError'] === false){
    /** success! 
     * At this level, a prompt will show on the user phone asking them to enter their pin for transaction approval.
     * You can get the reference number and save it to your DB for reference purposes and to be used during transaction verification
    */
    $reference = $payment_response['reference'];
    echo json_encode($payment_response, true);
}else{
    //failure
    // Transactions can fail if provided details are incorrect. check the response for debugging info.
    echo json_encode($payment_response, true);
}
?>
```

## Verify Payment

```php
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
?>
```

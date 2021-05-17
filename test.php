<?php
// NB: This code has not been tested yet. Still building the file structure.
try {
    include "config.php";
    include "mobile_money.php";
    $key = "1241safwr13da1231sa";
    $pub_key = "13esar23eqdwax0j23d";
    $payments = new PaymentsMiddleware($key);
    // Collect
    $payments->config([
        'use' => "sparco",
        'amount' => 10.00,
        'currency' => "ZMW",
        'client_first_name' => "John",
        'client_last_name' => "Doe",
        'client_email' => "user@website.com",
        'public_key' => $pub_key,
        'transaction_description' => "Bought PHP future version 9 product",
        'transaction_reference' => "132qw1s2",
        'deduct_from' => "0900000000",
        'charge_client' => true
    ]);
    // TODO figure out why the class below cannot inherit values from the payments class.
    $pay = new MobileMoney("collect");
    print_r($pay->config);
    // var_dump($pay);
} catch (\Throwable $th) {
    //throw $th;
    print($th);
}

?>
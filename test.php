<?php
try {
    include "PaymentsMiddleware.php";
    $key = "1241safwr13da1231sa";
    $pub_key = "13esar23eqdwax0j23d";
    $payments = new PaymentsMiddleware($key);
    // Collect configuration
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
    $collect_money_response = $payments->mobile_money("collect");
    echo $payments->response;
} catch (\Throwable $th) {
    var_dump($th);
}
?>
<?php

$url="https://samafricaonline.com/sam_pay/public/merchantcheckout"; 
$appkey="Replace with your Key"; 
$authkey="Replace with your Key"; 
$orderID=rand(1000000,9999999); 
$ordername="Nike Shoes"; 
$orderdetails="White with extra Shoe Laces Shoes"; 
$ordertotal=1; 
$currency="ZMW"; 
$ch=curl_init($url); 


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

//Setup request to send JSON via POST  
$data=array(
    'AppKey'=>$appkey,
    'AuthKey'=>$authkey,
    'OrderID'=>$orderID,
    'OrderName'=>$ordername,
    'OrderDetails'=>$orderdetails,
    'Or derTotal'=>$ordertotal,
    'Currency'=>$currency); 
$payload=json_encode($data);


//Attach encoded JSON string to the POST fields 

curl_setopt($ch,CURLOPT_POSTFIELDS,$payload); 
//Set the content type to application/json 
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json')); 
//Return response instead of outputting 
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
//Execute the POST request 
$result=curl_exec($ch); 
print_r($result);

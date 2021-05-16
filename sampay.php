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


//Attach encoded JSON string to the POST fields 

$apiresponse = json_decode($result); 
$responsestatus = $apiresponse->status; 
$responsemessage = $apiresponse->message; 
//You are looking for status 200 
if($responsestatus=='200'){ 

//A successful request will generate a payment token that can be used when your customer visits the Sampay website. //The token must be passed to Sampay as a parameter 
$token = $responsemessage;  
header("Location: https://samafricaonline.com/sam_pay/public/merchantpayment?token=$token"); }else{ 
//If status is not 200, there is an error in the request. The request was denied. 
} 

//Close cURL resource
//Sampay only accepts verified tokens to process payments from external requests. 
//https://samafricaonline.com/sam_pay/public/merchantpayment?token=token 
//The user will have options to pay or to cancel the order. 
//Whatever option is chosen, Sampay will then offer a redirect to your return URL supplied during the App/Website Registration. We will then return the token and pass an additional parameter "response” 
//E.g. url@yourdomain.com?token=token&response=response 
//The response is either true or false 

$url="https://samafricaonline.com/sam_pay/public/paymentconfirmation"; 
$appkey="Replace with your Key"; 
$authkey="Replace with your Key"; 
$token=$token; 
$ordered="The order ID”; 
$ch=curl_init($url); 
//Setup request to send JSON via POST  
$data=array(
    'AppKey'=>$appkey,
    'AuthKey'=>$authkey, 
    'orderID'=>$ordered, 
    'token'=>$token); 
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
You can now complete the records on your App or Website according to the response 

<?php
/**
 * NOTE - This works :-)  but, might need some polish up
 * contact Ecobank to provide you with Live details or you can start by 
 * reading their docs here to extend your knowledge (https://documenter.getpostman.com/view/9576712/Szmb6epq#22e33b07-46c8-47f9-8744-37ca05c3ba2f).
 * I'll create a simplified doc soon
 */
require 'master_card_qr_pay.php';
// Basic Authentication
$merchant_user_id = ""; //you'll get this from Ecobank After Creating an account
$merchant_password = ""; //you'll get this from Ecobank After Creating an account
$merchant_private_api_lab_key = "";  //you'll get this from Ecobank After Creating an account
$master_card_pay = new MasterCardQrPay($merchant_user_id, $merchant_password, $merchant_private_api_lab_key);
$token = $master_card_pay->token;
// Merchant_account setup
$merchant_setup_body = '{
    "headerRequest": {
        "requestId": "",
        "affiliateCode": "EGH",
        "requestToken": "'.$token.'",
        "sourceCode": "ECOBANK_QR_API",
        "sourceChannelId": "KANZAN",
        "requestType":"CREATE_MERCHANT"
    },
    "merchantAddress": "123ERT",
    "merchantName":"UNIFIED SHOPPING CENTER",
    "accountNumber": "02002233444",
    "terminalName": "UNIFIED KIDS SHOPPING ARCADE",
    "mobileNumber": "0245293945",
    "email": "freemanst@gmail.com",
    "area": "Ridge",
    "city": "Ridge",
    "referralCode": "123456",
    "mcc": "0000",
    "dynamicQr":"Y",
    "callBackUrl":"http://koala.php",
    "secure_hash":"7f137705f4caa39dd691e771403430dd23d27aa53cefcb97217927312e77847bca6b8764f487ce5d1f6520fd7227e4d4c470c5d1e7455822c8ee95b10a0e9855"
}';
if($master_card_pay->setup_merchant($merchant_setup_body)){
    // generate QR Code Image
    $generate_qr_code_body = '{
        "ec_terminal_id": "'.$master_card_pay->terminal_id.'",
        "ec_transaction_id": "we009",
        "ec_amount": 200,
        "ec_charges": "0",
        "ec_fees_type": "P",
        "ec_ccy": "KES",
        "ec_payment_method": "QR",
        "ec_customer_id": "OK1337/09",
        "ec_customer_name": "DAVID AMUQUANDOH",
        "ec_mobile_no": "233260516997",
        "ec_email": "DAVYTHIT@GMAIL.COM",
        "ec_payment_description": "PAYMENT FOR JUMIA SHOPPING",
        "ec_product_code": "AEW23FSSS",
        "ec_product_name": "ONLINE SHOPPING 1212",
        "ec_transaction_date": "bnbbn",
        "ec_affiliate": "qwe123QE",
        "ec_country_code": "123",
        "secure_hash": "7f137705f4caa39dd691e771403430dd23d27aa53cefcb97217927312e77847bca6b8764f487ce5d1f6520fd7227e4d4c470c5d1e7455822c8ee95b10a0e9855"
    }';
    if($master_card_pay->get_qr_code($generate_qr_code_body)){
        echo '<img src="'.$master_card_pay->qr_code.'">';
    }else{
        echo $master_card_pay->last_error;   
    }
}else{
    echo $master_card_pay->last_error;
}

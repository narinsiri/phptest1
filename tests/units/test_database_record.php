<?php
    include_once('../../classes/common.php');       // Include all standard settings
    include_dir("data_access");                     // Include database handler class
    include_once('../../includes/payment_handler.php');   // Include gateway handler functions
    
    $arrIntegrationTestCase = array(
        "customer-name" => "Jane Doe",
        "currency" => "USD",
        "price" => "1.00",
        "for-db-holderName" => "John Doe",
        "card-type" => "VISA",
        "for-db-number" => "5555444433331111",
        "for-db-expiryMonth" => "08",
        "for-db-expiryYear" => "2018",
        "for-db-cvc" => "737");
    $objResult = submission_record_to_db($arrIntegrationTestCase);
    if($objResult===false) {
        echo "Recording failure";
    } else {
        echo "Recording success";
    }
?>
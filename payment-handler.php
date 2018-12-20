<?php
    
    include_once('classes/common.php');     // Include all standard settings
    include_dir("data_access");             // Include database handler class
    include_once('includes/payment_handler.php'); // Include gateway handler functions

    submission_record_to_db($_POST);

    switch($_POST['gateway']) {
        case 'adyen':
            $strCurrentReferenceID = uniqid();
            $objResult = adyen_request($_POST,$strCurrentReferenceID);
           
            if($objResult !== false) {
                $objOutputResult = json_decode($objResult);
                $strResultReference = $objOutputResult->pspReference;
                $strResultCode = $objOutputResult->resultCode;
                $strResultDetails = ($strResultCode == "Refused") ? $objOutputResult->refusalReason :  $objOutputResult->authCode;
            }
            break;
        case 'braintree':
            break;
    }
    echo "Results<br/>";
    echo "-------<br/>";
    echo "Payment Gateway: ".$_POST['gateway'].'<br/>';
    echo "Reference Code: ".$strResultReference.'</br>';
    echo "Result: ".$strResultCode.'</br>';
    echo "Details(Reason): ".$strResultDetails.'<br/>';
       
?>
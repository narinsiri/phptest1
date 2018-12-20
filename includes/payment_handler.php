<?php
    function adyen_request($arrRequest,$strReferences) {
       $request = array(
            "merchantAccount" => ADYEN_MERCHANT_ACC,
            "amount" => array(
                "value"=>100,
                "currency"=>"EUR"),
            "reference" => "TEST-PAYMENT-" . date("Y-m-d-H:i:s"),
            "shopperIP" => strval($_SERVER['REMOTE_ADDR']),
            "shopperEmail" => ADYEN_SHOPPER_EMAIL,
            "shopperReference" => $strReferences,
            "fraudOffset" => "0",
            "additionalData"=>array(
                "card.encrypted.json" => $arrRequest['adyen-encrypted-data']
            ),
            "browserInfo"=>array(
                "acceptHeader"=>$_SERVER['HTTP_USER_AGENT'],
                "userAgent"=>$_SERVER['HTTP_ACCEPT']
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ADYEN_URL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC  );
        curl_setopt($ch, CURLOPT_USERPWD, ADYEN_USER_PWD);
        curl_setopt($ch, CURLOPT_POST,count(json_encode($request)));
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-type: application/json"));

        $result = curl_exec($ch);
        return $result;
    } 
    
    function submission_record_to_db($arrRequest) {
        $objDataAccess = new data_access();     // Data Access Object
        
        //** Prepare the database table **//
        $strDatabaseTableSQL = "CREATE TABLE IF NOT EXISTS ".DB_MAINTABLE."(
					TransactionID mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    ReferenceID VARCHAR(31) NOT NULL DEFAULT '',
					CustomerFullName VARCHAR(255) NOT NULL DEFAULT '',
					Currency VARCHAR(32) NOT NULL DEFAULT '',
					Price DECIMAL(7,2) NOT NULL DEFAULT 0,
					CreditCardHolderName VARCHAR(255) NOT NULL DEFAULT '',
                    CreditCardType VARCHAR(8) NOT NULL DEFAULT '',
                    CreditCardNumber VARCHAR(255) NOT NULL DEFAULT '',
                    CreditCardExpiryMonth SMALLINT(3) NOT NULL DEFAULT 0,
                    CreditCardExpiryYear VARCHAR(4) NOT NULL DEFAULT '',
                    CreditCardCCV VARCHAR(3) NOT NULL DEFAULT '',
					CreatedDate timestamp DEFAULT CURRENT_TIMESTAMP
					) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
        $objDataAccess->executeSQL($strDatabaseTableSQL);
        //** End of Table Preparation **//
        $strCurrentReferenceID = uniqid();
        $arrIntegrationTestCase = array("ReferenceID" => $strCurrentReferenceID,
            "CustomerFullName" => $arrRequest['customer-name'],
            "Currency" => $arrRequest['currency'],
            "Price" => $arrRequest['price'],
            "CreditCardHolderName" => $arrRequest['for-db-holderName'],
            "CreditCardType" => $arrRequest['card-type'],
            "CreditCardNumber" => $arrRequest['for-db-number'],
            "CreditCardExpiryMonth" => $arrRequest['for-db-expiryMonth'],
            "CreditCardExpiryYear" => $arrRequest['for-db-expiryYear'],
            "CreditCardCCV" => $arrRequest['for-db-cvc']);
        return $objDataAccess->recordArrayToTable($arrIntegrationTestCase,DB_MAINTABLE);
        
    }
?>
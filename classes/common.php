<?php
	error_reporting(E_ALL);
	ini_set('display_errors','On');
	session_start();
	// Turn off magic quotes //
	
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
	
	$strCurrentURL = (empty($_SESSION['CurrentURL'])) ? 'http://www.linuxxdd.com/payments/' : $_SESSION['CurrentURL'];
	
	define("sp","/");
	define("rootdir","/home/linuxdd/domains/linuxdd.com/public_html/payments");
	define("ROOT_URL",$strCurrentURL);
	
	define("DB_SERVER","localhost");
	define("DB_NAME","linuxdd_dev");
	define("DB_USER","linuxdd_dev");
	define("DB_PASSWORD","49DJPf8G");	
	define("DB_MAINTABLE","payments__transactions");

	define("SQL_LOG_PATH",rootdir."/logs/sql/".date("Y-m-d")."-sql.txt");
		
    define("ADYEN_MERCHANT_ACC", "PRMedia360COM"); 
    define("ADYEN_SHOPPER_EMAIL", "narin.siri@gmail.com"); 
    define("ADYEN_USER_PWD", "ws@Company.PRMedia360:baR5^Pi<B4-*6ex%CiF7)wpmS"); 
    define("ADYEN_URL", "https://pal-test.adyen.com/pal/servlet/Payment/v25/authorise"); 

	function include_dir($strClassNameAndPath, $strSpecificName = "") {
		$arrPath = explode("_",$strClassNameAndPath);
		$strPath = rootdir.sp."classes";
		foreach($arrPath as $strFolder) {
			$strPath .= sp.$strFolder;
		}
		$strFileName = ($strSpecificName == "") ? ".php" : sp.$strSpecificName;
		if (file_exists($strPath.$strFileName)) {
            
			return include_once($strPath.$strFileName);
		}
		
		return 0;
	}

?>
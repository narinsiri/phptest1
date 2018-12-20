<?php
	include_dir("utils_logger");

 	class data_access {
 		public $datalink;
 		public $affectrow;
 		public $insertid;
 		public $errordescription;
 		public $database;
 		
	 	public function __construct($database=DB_NAME,$server=DB_SERVER,$user=DB_USER,$pwd=DB_PASSWORD) {
	       $this->datalink = mysqli_connect($server,$user,$pwd);
	       $this->database = $database;
	       mysqli_select_db($this->datalink,$this->database);
	       mysqli_set_charset($this->datalink,'utf8');
	   	}
	   	/*
	   	public function __destruct() {
	   		mysqli_close($this->datalink);
	   	}
	   	*/
	   	// CORE CODE PART //
 		public function executeSQL($sql) {
 		    $result = mysqli_query($this->datalink,$sql);
	   		utils_logger::addLogEntry(SQL_LOG_PATH,date("Y/m/d H:i:s")." - ".$sql);
	   		//echo $sql;
	   		$this->affectrow = mysqli_affected_rows($this->datalink);
	   		$this->insertid = mysqli_insert_id($this->datalink);
	   		$this->errordescription = mysqli_error($this->datalink);
			//if ($this->errordescription !== '') echo $sql."<br>".$this->errordescription;
	   		return $result;
	   	}
	   	
	   	public function getMultipleRowArray($sql) {
	   		$objResult = $this->executeSQL($sql);
	   		$arrReturn = array();
	   		if (strlen($this->errordescription) > 0) return $arrReturn;
	   		while ($row = mysqli_fetch_assoc($objResult)) {
	   			array_push($arrReturn,$row);
	   		}
	   		return $arrReturn;
	   	}
	   	
	   	public function getKeys($sql) {
	   		$strSQL = data_access::changeLimit($sql, 1);
	   		$objRS = $this->executeSQL($strSQL);
	   		if (!$objRS) return array();
	   		$arrResults = array();
	   		while($row = mysqli_fetch_field($objRS)) {
	   			array_push($arrResults,$row->name);
	   		}
	   		return $arrResults;
	   	}
	   	
	   	public function getNumberOfRows($sql) {
	   		if ($this->safeSQL($sql)) {
		   		$objRS = $this->executeSQL($sql);
		   		return mysqli_num_rows($objRS);
	   		}
	   		return 0;
	   	}
	   	
	   	public function safeSQL($sql) {
	   		$strSQL = str_replace(";","",$sql);
	   		$arrResults = $this->getMultipleRowArray("describe (".$strSQL.")");
	   		if (count($arrResults) == 0) return 0;
	   		return $arrResults[0]['rows'];
	   	}
	   	
	   	public static function changeLimit($sql,$intNewLimit) {
	   		$strSQL = str_replace(";","",$sql);
	   		$intLimit = strpos(strtolower($strSQL),"limit");
	   		if ($intLimit !== false) {
	   			$strSQL = substr($strSQL,0,$intLimit);
	   		}
	   		$strSQL .= " limit 0,".$intNewLimit;
	   		return $strSQL;
	   	}
	   	
	   	public function recordArrayToTable($arrRecord,$strTableName) {
	   		$result = $this->executeSQL("SELECT * FROM ".$strTableName.";");
	   		$i = 0;
	   		$arrField = array();
	   		$arrData = array();

	   		while ($i < mysqli_num_fields($result)) { 
	   			$objField = mysqli_fetch_field($result);
	   			if (!empty($arrRecord[$objField->name])) {
	   				array_push($arrField, $objField->name);
	   				$value = $arrRecord[$objField->name];
	   				if (((substr($value,-1) != ')') || (strpos($value," ") !== false))  && (!is_numeric($value)))
	   				    $value = "'".mysqli_real_escape_string($this->datalink,$value)."'";
	   				array_push($arrData,$value);
	   			}
	   			$i++;
	   		}
	   		$strSQL = "INSERT INTO $strTableName(`".implode('`,`',$arrField)."`) values(".implode(",",$arrData).");";
			$this->executeSQL($strSQL);	   	
	   	}
	   	
	   	public function recordOrUpdateArrayToTable($strTable,$arrRecord,$arrConditions) {
	   		if(count($this->getResults($strTable, $arrConditions))) {
	   			return $this->updateTable($strTable, $arrRecord, $arrConditions);
	   		} else {
	   			return $this->recordArrayToTable($arrRecord, $strTable);
	   		}
	   	}
	   	
	   	public function createTableSQL($strTableName,$arrMainObjects) {
	   		$strSQL = "CREATE TABLE IF NOT EXISTS `".$strTableName."` (";
	   		$arrPrimary = array();
	   		$intPrimaryCount = 0;
	   		foreach ($arrMainObjects as $objField) 
	   			if ($objField->primary_key == 1) $intPrimaryCount++;
	   		
	   		foreach ($arrMainObjects as $objField) {
	   			$strSQL .= "`".$objField->name."` ";
	   			if ($objField->type == 'date') 
	   				$strSQL .= $objField->type;
	   			else
	   				$strSQL .= ($objField->type == 'string' ? 'VARCHAR': $objField->type)."(".$objField->max_length.")";
	   			$strSQL .= ($objField->not_null == 1) ? " NOT NULL" : "";
	   			if (($objField->primary_key == 1) && ($intPrimaryCount > 1)) 
	   				array_push($arrPrimary,"`".$objField->name."`");
	   			else 
	   				$strSQL .= ($objField->primary_key == 1) ? " PRIMARY KEY" : "";
	   			$strSQL .= ($objField->unique_key == 1) ? " UNIQUE KEY" : "";
	   			$strSQL .= ",";
	   		}
	   		$strSQL = substr($strSQL,0,strlen($strSQL)-1);
	   		if (count($arrPrimary) > 1) $strSQL .= ", PRIMARY KEY (".implode(',',$arrPrimary).") ";
	   		$strSQL .= ") ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
	   		return $strSQL;
	   	}
	   	
	   	public function recordArrayToNewTable($arrData,$strTableName) {
	   		if (count($arrData) == 0) return false;
	   		// 1. Create Table //
	   		$strSQL = "CREATE TABLE `".$strTableName."` (";
	   		foreach ($arrData[0] as $key => $value) {
	   			$strLength = (empty($arrData['MaxLength'][$key])) ? 127 : $arrData['MaxLength'][$key];
	   			//$strType = (is_numeric($value)) ? "INT(11)" : "VARCHAR(".$strLength.")";
	   			$strType = "VARCHAR(".$strLength.")";
	   			$strSQL .= "`".$key."` ".$strType.",";
	   		}
	   		$arrRows = array_diff_key($arrData,array("MaxLength" => ""));
	   		$strSQL = substr($strSQL,0,strlen($strSQL)-1);
	   		$strSQL .= ") ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
	   		$this->executeSQL($strSQL);
	   		// 2. record data
	   		foreach($arrRows as $key => $arrRow) {
	   			$this->recordArrayToTable($arrRow, $strTableName);
	   		}
	   		return true;
	   	}
	   	// END OF CORE CODE PART //
	   	
	   	public function getResults($strTableName,$arrConditions=array(),$strSelect="*",$strExtraConditions="") {
	   		$strSql = "SELECT ".$strSelect." FROM ".$strTableName." WHERE (1)".$this->makeConditions($arrConditions);
			
	   		if ($strExtraConditions !== ""){ 
	   			$strSql .= " and ".$strExtraConditions;
	   		}
	   		$arrReturn = $this->getMultipleRowArray($strSql);
	   		//if (count($arrReturn) == 1) return $arrReturn[0];
	   		return $arrReturn;
	   	}
	   	
	   	public function updateTable($strTableName,$arrUpdateFields,$arrConditions=array(),$strExtraConditions="",$strLimit="") {
	   		if ((empty($strTableName)) || (count($arrUpdateFields) <= 0)) return false;
	   		$strSql = "UPDATE ".$strTableName;
	   		$arrSetValues = array();
	   		
	   		$result = $this->executeSQL("SELECT * FROM ".$strTableName.";");
	   		$i = 0;

	   		while ($i < mysqli_num_fields($result)) { 
	   			$objField = mysqli_fetch_field($result,$i);
	   			if (isset($arrUpdateFields[$objField->name])) {
	   				$value = $arrUpdateFields[$objField->name];
	   				if (((substr($value,-1) == ')') && (strpos($value," ") === false))  || (is_numeric($value)))
	   					array_push($arrSetValues, $objField->name." = ".$value);
	   				else 
	   					array_push($arrSetValues, $objField->name." = '".mysqli_real_escape_string($value)."'");
	   			}
	   			$i++;
	   		}
	   		
	   		$strSql .= " SET ".implode(",",$arrSetValues)." WHERE (1)".$this->makeConditions($arrConditions);
	   		
	   		if ($strExtraConditions !== ""){
	   			$strSql .= " and ".$strExtraConditions;
	   		}
	   		
	   		$strSql .= $strLimit;
	   		
	   		if (!$this->executeSQL($strSql)) return false;
	   		//if (count($arrReturn) == 1) return $arrReturn[0];
	   		return true;
	   	}
	   	
	   	public function deleteDataFromTable($strTableName, $arrConditions=array(),$strExtraConditions="",$strLimit="") {
	   		if (empty($strTableName)) return false;
	   		$strSql = "DELETE FROM ".$strTableName." WHERE (1)".$this->makeConditions($arrConditions);
	   		
	   		if ($strExtraConditions !== ""){
	   			$strSql .= " and ".$strExtraConditions;
	   		}
	   		
	   		$strSql .= $strLimit;
	   		
	   		//echo $strSql;
	   		
	   		if (!$this->executeSQL($strSql)) return false;
	   		//if (count($arrReturn) == 1) return $arrReturn[0];
	   		return true;
	   	}
	   	
 		public static function reduceArrayDimension($arrInput, $strFieldToSelect) {
 			$arrOutput = array();
 			foreach($arrInput as $key => $arrRow) {
 				$arrOutput[$key] = $arrRow[$strFieldToSelect];
 			}
 			return $arrOutput;
 		} 
 		
 		private function makeConditions($arrConditions) {
 			$strSql = "";
 			foreach($arrConditions as $key => $value) {
 				if (((substr($value,-1) == ')')  && (strpos($value," ") === false))  || (is_numeric($value)))
 				$strSql .= " and (".$key." = ".$value.")";
 				else
 				$strSql .= " and (".$key." = '".mysqli_real_escape_string($value)."')";
 			}
 			return $strSql;
 		}
 	}
 	
 	
 	class datafield {
 		public $name;
 		public $type = 'string';
 		public $max_length = 255;
 		public $not_null =1;
 		public $primary_key = 0;
 		public $unique_key = 0;
 		public function __construct($strName,$strType = 'string',$intSize = 255) {
 			$this->name = $strName;
 			$this->type = $strType;
 			$this->max_length = $intSize;
 		}
 	}
?>
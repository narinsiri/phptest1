<?php
	class utils_pathhandler {
		public static function changedir($strCurrentDir, $strTargetDir) {
			// Check whether it is from root //
			if (substr($strTargetDir,0,1) == '/') {
				return utils_pathhandler::removeLastSlash($strTargetDir);
			}
			
			$strReturn = utils_pathhandler::removeLastSlash($strCurrentDir);
			$arrTargetDir = explode("/",$strTargetDir);
			
			for($i=0;$arrTargetDir[$i] == '..';$i++) {
					$strReturn = dirname($strReturn);
			}
			
			for($k=$i;$k<count($arrTargetDir);$k++) {
				if (!empty($arrTargetDir[$k]) && ($arrTargetDir[$k] != '.'))
					$strReturn .= ($strReturn == '')? $arrTargetDir[$k] :"/".$arrTargetDir[$k];
			}
			return $strReturn;
		}
		
		public static function removeLastSlash($strPath) {
			$strReturn = (substr($strPath,strlen($strPath)-1,1) == '/') ? substr($strPath,0,strlen($strPath)-1) : $strPath;
			return $strReturn;
		}
		
		public static function getFolder($strPath) {
			$strReturn = strrev($strPath);
			$intSlash = strpos($strReturn,'/');
			if ($intSlash !== false) {
				return utils_pathhandler::removeLastSlash(strrev(substr($strReturn,$intSlash,strlen($strReturn))));
			}
			return utils_pathhandler::removeLastSlash($strPath);
		}
		
		public static function isLocal($strPath) {
			return (substr(trim(strtolower($strPath)),0,4) != 'http');
		}
		
		public static function getHost($strURL) {
			$arrURL = parse_url($strURL);
			$strHost = $arrURL['scheme']."://".$arrURL['host'];
			$strHost .= (empty($arrURL['port'])) ? "" : ":".$arrURL['port'];
			return $strHost;
		}
		
		public static function toFullURL($strHtmlURL,$strRelativeFolderFile) {
			$arrMainURL = parse_url($strHtmlURL);
			
			$arrURLInfo = pathinfo($strRelativeFolderFile);			
			$strFileName = $arrURLInfo['basename']; // Get the file name
			// Change the directory to the one css is staying
			$strCssPath = utils_pathhandler::changedir(
			utils_pathhandler::getFolder($arrMainURL['path']),
			utils_pathhandler::getFolder($strRelativeFolderFile));
			// Combine to get the full URL
			return utils_pathhandler::getHost($strHtmlURL).$strCssPath."/".$strFileName;
		}
		
		public static function curPageURL($booFullUrl=false) {
			 $pageURL = 'http';
			 if (!empty($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
			 $pageURL .= "://";
			 if ($_SERVER["SERVER_PORT"] != "80") {
			  	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			 } else {
			  	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			 }
			 if ($booFullUrl) return $pageURL;
			 utils_pathhandler::getFolder($pageURL);
		}
	}
?>
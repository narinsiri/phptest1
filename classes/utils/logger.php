<?php
	//include_dir("data_access");
 	class utils_logger extends data_access {
 		public static function addLogEntry($strFilePath,$strContent) {
 			if (file_exists($strFilePath)) {
 				file_put_contents($strFilePath,$strContent.chr(13),FILE_APPEND);
 			} else {
 				file_put_contents($strFilePath,$strContent.chr(13));
 			}
 		}
 	}
?>
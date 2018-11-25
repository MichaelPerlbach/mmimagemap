<?php
/***************************************************************
 *	(c) 2018 MikelMade (www.mikelmade.de)
 *	All rights reserved
***************************************************************/

namespace MikelMade\Mmimagemap\Utility;

/**
 * Class Tx_Mmimagemap_Utility_Div
 * @package argenproducts
 */
class Tx_Mmimagemap_Utility_Div {
	
	/**
		*	returns the extension configuration
		*	return array
	*/
	public static function GetExtConf(){
		$extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mmimagemap']);
		$retconf = [];
		$retconf['colors'] = [];
		
		foreach($extconf['colors.'] as $key=>$value){
			if(strlen($value) != 0){
				$valarr = explode('|',$value);
				$thiscol['color'] = $valarr[0];
				$valarr2 = explode(',',$valarr[1]);
				foreach($valarr2 as $lang){
					$larr = explode(':',$lang);
					$thiscol[$larr[0]] = $larr[1];
				}
				$retconf['colors'][] = $thiscol;
			}
		}

		$retconf['ext'] = $extconf['extension.'];
		
		return $retconf;
	}
	
	/**
	* Corrects the additional parameters for a given area - removes expressions not needed and excess quotes.
	*
	* @param	string		an event (e.g. "onclick").
	* @param	string		a string containing the parameters.
	* @return string
	*/
	public static function CorrectParams($event, $pstring) {
		$pstring = str_ireplace('javascript:', '', trim(rtrim($pstring)));
		$pstring = str_ireplace($event, '', $pstring);
		$pstring = str_ireplace('=', '', $pstring);
		$quotepos = strpos($pstring, '"');
		$quotepos_end = strrpos($pstring, '"');
		$str_len = $quotepos_end - $quotepos;
		$badquote = substr($pstring, $quotepos, $str_len+1);
		$badquote2 = str_replace('"', '', $badquote);
		$badquote3 = str_replace(',', '', $badquote2);
		$pstring			= str_replace($badquote, $badquote3, $pstring);
		return $pstring;
	}
	
	/**
		*	returns the absolute serverpath
		*	return string
	*/
	public static function absPath(){
		return trim(rtrim($_SERVER['DOCUMENT_ROOT']));
	}
	
	/**
		*	creates a thumbnail from a given path and an image
		*	@param string $filename
		*	@param string $path
		*	return string
	*/
	public static function CreateThumb($filename, $path) {
		$impath = $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path'];
		if($GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] == 'GraphicsMagick') { $impath .= 'gm '; }
		
		$salt = 'mmimagemap';
		$temppath = PATH_site.'fileadmin/_processed_/mmimagemap';
		
		if(!is_dir($temppath)){ mkdir($temppath,0755); }
		
		$img_data = getimagesize ( PATH_site .'fileadmin/'. $path . $filename );
		$width		= $img_data['0'];
		$height	 = $img_data['1'];
		
		if ($width >= $height) { $ratio = $width/100; }
		else { $ratio = $height/100; }
		$new_width	= ceil($width/$ratio);
		$new_height = ceil($height/$ratio);
		$new_path	 = $temppath.'/'.md5($path.$salt.$filename);
		
		if(!file_exists($new_path)){
			if(preg_match("/WIN/", PHP_OS)) { exec('"'.rtrim($impath).'" convert -resize '.$new_width.'!x'.$new_height.'! -quality 100 -unsharp 1.5x1.2+1.0+0.10 "'.PATH_site.'fileadmin/'.$path.$filename.'" "'.$new_path.'.jpg"'); }
			else { exec($impath.'convert -resize '.$new_width.'!x'.$new_height.'! -quality 100 -unsharp 1.5x1.2+1.0+0.10 '.PATH_site.'fileadmin/'.$path.$filename.' '.$new_path.'.jpg'); }
			rename($new_path.'.jpg', $new_path);
		}
		return md5($path.$salt.$filename);
	}
	
	/**
		*	removes a thumbnail from a given path and an image
		*	@param string $filename
		*	@param string $path
		*	return string
	*/
	public static function RemoveThumb($filename, $path) {
		$salt = 'mmimagemap';
		$temppath = PATH_site.'fileadmin/_processed_/mmimagemap';
		$del_path	 = $temppath.'/'.md5($path.$salt.$filename);
		if(file_exists($del_path)){
			unlink($del_path);
		}
	}
	
	/**
		*	returns an array with image path and image size
		*	@param string $img
		*	@param string $path
		*	return array
	*/
	public static function GetPicSize($img,$path){
		$img_data = getimagesize ( PATH_site .'fileadmin/'. $path . $img );
		$width		= $img_data['0'];
		$height	 = $img_data['1'];
		
		return array(
			'w' => $img_data['0'],
			'h' => $img_data['1'],
			'dir' => '../fileadmin/'. $path.$img
		);
	}
	
	
	/**
		*	creates a random string
		* @param integer
		*	return string
	*/
	public function rand_string($lng=8) {
		mt_srand((double)microtime()*1000000);
		$charset = "abcdefghijklmnpqrstuvwxyz";
		$length	= strlen($charset)-1;
		$code		= '';
		for($i=0;$i<$lng;$i++) {
			$code .= $charset{mt_rand(0, $length)};
		}
		return $code;
	}
	
	/**
		*	saves debug information in the extension's base directory
		* @param string
		* @param string
		* @param boolean (false: debug information is added, true: the debug file will be overwritten)
		*	return string
	*/
	public static function debug($function='',$info='',$new=false){
		$divider = "\n---------------------------------------\n";
		$debuginfo = '';
		if($new == false){
			$debuginfo = file_get_contents(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3conf/ext/mmimagemap/debug.txt'));
		}
		$debug = fopen(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3conf/ext/mmimagemap/debug.txt'),'w+');
		$fdist = (strlen($debuginfo) == 0) ? '' : "\n";
		$debuginfo .= $fdist.'#######################################'."\n".$function.' ';
		$debuginfo .= '['.date('Y-m-d H:i:s').']'.$divider;
		$debuginfo .= $info;
		$debuginfo .= "\n#######################################\n";
		fputs($debug,$debuginfo);
		fclose($debug);
	}
	// How to call:
	//\MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::debug(get_class($this).'->'.__FUNCTION__,'info',false);
	
	/**
		* tries to normalize encodings to utf8
		*	@param string
		*	return string
		*
		*/
	public static function normalizetoUTF8($string,$filepath='',$save=false){
		$convertedstring = $string;
		$encoding = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::detect_utf_encoding($string);
		if($encoding != 'UTF-8'){ $convertedstring = mb_convert_encoding($string,'UTF-8', $encoding); }

		if($save == true && strlen($filepath) != 0){
			if(file_exists($filepath)){
				$fp = fopen($filepath,'w');
				fputs($fp,$string);
				fclose($fp);
			}
		}

		return $convertedstring;
	}
	
	/**
		* tries to purify a string
		*	@param string
		*	return string
		*
		*/
	public static function purifyString($string){
		$string = strip_tags($string);
		$string = str_replace(array("\n","\c","\t"),'',$string);
		$string = trim(rtrim($string));
		return $string;
	}
	
	/**
		* returns HEX color to RGB
		*	@param string
		*	return array
		*/
	function ConvertToRGB($hexcolor) {
		$bc = str_replace('#', '', $hexcolor);
		$r = hexdec(substr($bc, 0, 2));
		$g = hexdec(substr($bc, 2, 2));
		$b = hexdec(substr($bc, 4, 2));
		return $r.','.$g.','.$b;
	}
	
	
	/**
		* tries to detect string encoding
		*	@param string
		*	return string
		*
		*/
	public static function detect_utf_encoding($string) {
		define ('UTF32_BIG_ENDIAN_BOM'	 , chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
		define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
		define ('UTF16_BIG_ENDIAN_BOM'	 , chr(0xFE) . chr(0xFF));
		define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
		define ('UTF8_BOM'							 , chr(0xEF) . chr(0xBB) . chr(0xBF));
			
	 $text = $string;
	 $first2 = substr($text, 0, 2);
	 $first3 = substr($text, 0, 3);
	 $first4 = substr($text, 0, 3);
	 
	 if ($first3 == UTF8_BOM) return 'UTF-8';
	 elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
	 elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
	 elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
	 elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
	}
	
	/** 
		* reduces a string to a limited set of characters
		* currently A-Za-z0-9_\-\.
		* 
	*/
	public static function szreduce($string){
		$newstring = '';
		$fsarr = str_split($string,1);
		foreach($fsarr as $item){
			if(preg_match("/^[A-Za-z0-9_\-\.]+$/",$item)){ $newstring .= $item; }
		}
		return $newstring;
	}
	
	/**
		*	removes lots of special characters from a given string
		*	@param string
		*	return string
	*/
	public static function szreplace($string){
		$t = str_replace("	", " ", $string);
		$t = str_replace("	", " ", $t);
		$t = str_replace(" ", "-", $t);
		$t = str_replace("À", "A", $t);
		$t = str_replace("à", "a", $t);
		$t = str_replace("Á", "A", $t);
		$t = str_replace("á", "a", $t);
		$t = str_replace("Â", "A", $t);
		$t = str_replace("â", "a", $t);
		$t = str_replace("Ã", "A", $t);
		$t = str_replace("ã", "a", $t);
		$t = str_replace("Ä", "AE", $t);
		$t = str_replace("ä", "ae", $t);
		$t = str_replace("Å", "A", $t);
		$t = str_replace("å", "a", $t);
		$t = str_replace("Æ", "Ae", $t);
		$t = str_replace("æ", "ae", $t);
		$t = str_replace("Ç", "C", $t);
		$t = str_replace("ç", "c", $t);
		$t = str_replace("È", "E", $t);
		$t = str_replace("è", "e", $t);
		$t = str_replace("É", "E", $t);
		$t = str_replace("é", "e", $t);
		$t = str_replace("Ê", "E", $t);
		$t = str_replace("ê", "e", $t);
		$t = str_replace("Ë", "E", $t);
		$t = str_replace("ë", "e", $t);
		$t = str_replace("Ì", "I", $t);
		$t = str_replace("ì", "i", $t);
		$t = str_replace("Í", "I", $t);
		$t = str_replace("í", "i", $t);
		$t = str_replace("Î", "I", $t);
		$t = str_replace("î", "i", $t);
		$t = str_replace("Ï", "I", $t);
		$t = str_replace("ï", "i", $t);
		$t = str_replace("Ñ", "N", $t);
		$t = str_replace("ñ", "n", $t);
		$t = str_replace("Ò", "O", $t);
		$t = str_replace("ò", "o", $t);
		$t = str_replace("Ó", "O", $t);
		$t = str_replace("ó", "o", $t);
		$t = str_replace("Ô", "O", $t);
		$t = str_replace("ô", "o", $t);
		$t = str_replace("Õ", "O", $t);
		$t = str_replace("õ", "o", $t);
		$t = str_replace("Ö", "Oe", $t);
		$t = str_replace("ö", "oe", $t);
		$t = str_replace("Ø", "Oe", $t);
		$t = str_replace("ø", "oe", $t);
		$t = str_replace("Ù", "U", $t);
		$t = str_replace("ù", "u", $t);
		$t = str_replace("Ú", "U", $t);
		$t = str_replace("ú", "u", $t);
		$t = str_replace("Û", "U", $t);
		$t = str_replace("û", "u", $t);
		$t = str_replace("Ü", "Ue", $t);
		$t = str_replace("ü", "ue", $t);
		$t = str_replace("Y´", "Y", $t);
		$t = str_replace("y´", "y", $t);
		$t = str_replace("ß", "ss", $t);
			 
		for ($i = 0; $i < 48; $i++){ $t = str_replace(chr ($i), "", $t); }
		for ($i = 58; $i < 65; $i++){ $t = str_replace(chr ($i), "", $t); }
		for ($i = 91; $i < 97; $i++) { $t = str_replace(chr ($i), "", $t); }
		for ($i = 123; $i < 256; $i++){ $t = str_replace(chr ($i), "", $t); }
		return $t;
	}
	
	/**
		*	restores special characters when changed by Linux
		*	@param string
		*	return string
	*/
	public static function restorespecialchars($string){
		$uc = array(
			array('u00A1','¡'),
			array('u00A2','¢'),
			array('u00A3','£'),
			array('u00A4','¤'),
			array('u00A5','¥'),
			array('u00A6','¦'),
			array('u00A7','§'),
			array('u00A8','¨'),
			array('u00A9','©'),
			array('u00AA','ª'),
			array('u00AB','«'),
			array('u00AC','¬'),
			array('u00AD','­'),
			array('u00AE','®'),
			array('u00AF','¯'),
			array('u00B0','°'),
			array('u00B1','±'),
			array('u00B2','²'),
			array('u00B3','³'),
			array('u00B4','´'),
			array('u00B5','µ'),
			array('u00B6','¶'),
			array('u00B7','·'),
			array('u00B8','¸'),
			array('u00B9','¹'),
			array('u00BA','º'),
			array('u00BB','»'),
			array('u00BC','¼'),
			array('u00BD','½'),
			array('u00BE','¾'),
			array('u00BF','¿'),
			array('u00C0','À'),
			array('u00C1','Á'),
			array('u00C2','Â'),
			array('u00C3','Ã'),
			array('u00C4','Ä'),
			array('u00C5','Å'),
			array('u00C6','Æ'),
			array('u00C7','Ç'),
			array('u00C8','È'),
			array('u00C9','É'),
			array('u00CA','Ê'),
			array('u00CB','Ë'),
			array('u00CC','Ì'),
			array('u00CD','Í'),
			array('u00CE','Î'),
			array('u00CF','Ï'),
			array('u00D0','Ð'),
			array('u00D1','Ñ'),
			array('u00D2','Ò'),
			array('u00D3','Ó'),
			array('u00D4','Ô'),
			array('u00D5','Õ'),
			array('u00D6','Ö'),
			array('u00D7','×'),
			array('u00D8','Ø'),
			array('u00D9','Ù'),
			array('u00DA','Ú'),
			array('u00DB','Û'),
			array('u00DC','Ü'),
			array('u00DD','Ý'),
			array('u00DE','Þ'),
			array('u00DF','ß'),
			array('u00E0','à'),
			array('u00E1','á'),
			array('u00E2','â'),
			array('u00E3','ã'),
			array('u00E4','ä'),
			array('u00E5','å'),
			array('u00E6','æ'),
			array('u00E7','ç'),
			array('u00E8','è'),
			array('u00E9','é'),
			array('u00EA','ê'),
			array('u00EB','ë'),
			array('u00EC','ì'),
			array('u00ED','í'),
			array('u00EE','î'),
			array('u00EF','ï'),
			array('u00F0','ð'),
			array('u00F1','ñ'),
			array('u00F2','ò'),
			array('u00F3','ó'),
			array('u00F4','ô'),
			array('u00F5','õ'),
			array('u00F6','ö'),
			array('u00F7','÷'),
			array('u00F8','ø'),
			array('u00F9','ù'),
			array('u00FA','ú'),
			array('u00FB','û'),
			array('u00FC','ü'),
			array('u00FD','ý'),
			array('u00FE','þ'),
			array('u00FF','ÿ')
		);
		$c = count($uc);
		for($i=0;$i<$c;$i++){ $string = str_ireplace(array('#'.$uc[$i][0],'\''.$uc[$i][0]),$uc[$i][1],$string); }
		return $string;
	}
	
	/**
		*	tests if a csv row is empty
		*	return string
	*/
	public static function is_not_clear($arr,$cols){
		for($i=0;$i<$cols;$i++){ 
			if(strlen($arr[$i]) > 0){ return false; }
		}
		return true;
	}
	
	/**
		*	removes a byte order mark from a string
		*	return string
	*/
	public static function remove_utf8_bom($text){
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
	}
	
	/**
		*	returns the last part of a string which is presumed to be a directory.
		* If it is not a directory, the string is returned.
		*	@param string
		*	return string
	*/
	public static function getlaststring($str){
		$strparts = array();
		if(preg_match("/\\\\/",$str)){ $strparts = explode("\\",$str); }
		if(preg_match("/\//",$str)){ $strparts = explode("/",$str); }
		if(count($strparts) > 0){ return $strparts[count($strparts)-1]; }
		else{ return $str; }
	}
	
}
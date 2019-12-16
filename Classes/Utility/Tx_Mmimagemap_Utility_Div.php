<?php
/***************************************************************
 *	(c) 2019 MikelMade (www.mikelmade.de)
 *	All rights reserved
***************************************************************/

namespace MikelMade\Mmimagemap\Utility;

/**
 * Class Tx_Mmimagemap_Utility_Div
 * @package argenproducts
 */
class Tx_Mmimagemap_Utility_Div
{
    
    /**
        *	returns the extension configuration
        *	return array
    */
    public static function GetExtConf()
    {
        $extconf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mmimagemap']);
				if($extconf != false) {
				    $extconf['extension'] = $extconf['extension.'];
				    $extconf['colors'] = $extconf['colors.'];
				    unset($extconf['colors.']);
				    unset($extconf['extension.']);
				}
				else {
				    $extconf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('mmimagemap');
				}
				
        $retconf = [];
        $retconf['colors'] = [];
        
        foreach ($extconf['colors'] as $key=>$value) {
            if (strlen($value) != 0) {
                $valarr = explode('|', $value);
                $thiscol['color'] = $valarr[0];
                $valarr2 = explode(',', $valarr[1]);
                foreach ($valarr2 as $lang) {
                    $larr = explode(':', $lang);
                    $thiscol[$larr[0]] = $larr[1];
                }
                $retconf['colors'][] = $thiscol;
            }
        }

        $retconf['ext'] = $extconf['extension'];
        
        return $retconf;
    }
    
    /**
    * Corrects the additional parameters for a given area - removes expressions not needed and excess quotes.
    *
    * @param	string		an event (e.g. "onclick").
    * @param	string		a string containing the parameters.
    * @return string
    */
    public static function CorrectParams($event, $pstring)
    {
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
    public static function absPath()
    {
        return trim(rtrim($_SERVER['DOCUMENT_ROOT']));
    }
    
    /**
        *	creates a thumbnail from a given path and an image
        *	@param string $filename
        *	@param string $path
        *	return string
    */
    public static function CreateThumb($filename, $path)
    {
        $impath = $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path'];
        if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] == 'GraphicsMagick') {
            $impath .= 'gm ';
        }
        
        $salt = 'mmimagemap';
        $temppath = PATH_site.'fileadmin/_processed_/mmimagemap';
        
        if (!is_dir($temppath)) {
            mkdir($temppath, 0755);
        }
        
        $img_data = getimagesize(PATH_site .'fileadmin/'. $path . $filename);
        $width		= $img_data['0'];
        $height	 = $img_data['1'];
        
        if ($width >= $height) {
            $ratio = $width/100;
        } else {
            $ratio = $height/100;
        }
        $new_width	= ceil($width/$ratio);
        $new_height = ceil($height/$ratio);
        $new_path	 = $temppath.'/'.md5($path.$salt.$filename);
        
        if (!file_exists($new_path)) {
            if (preg_match("/WIN/", PHP_OS)) {
                exec('"'.rtrim($impath).'" convert -resize '.$new_width.'!x'.$new_height.'! -quality 100 -unsharp 1.5x1.2+1.0+0.10 "'.PATH_site.'fileadmin/'.$path.$filename.'" "'.$new_path.'.jpg"');
            } else {
                exec($impath.'convert -resize '.$new_width.'!x'.$new_height.'! -quality 100 -unsharp 1.5x1.2+1.0+0.10 '.PATH_site.'fileadmin/'.$path.$filename.' '.$new_path.'.jpg');
            }
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
    public static function RemoveThumb($filename, $path)
    {
        $salt = 'mmimagemap';
        $temppath = PATH_site.'fileadmin/_processed_/mmimagemap';
        $del_path	 = $temppath.'/'.md5($path.$salt.$filename);
        if (file_exists($del_path)) {
            unlink($del_path);
        }
    }
    
    /**
        *	returns an array with image path and image size
        *	@param string $img
        *	@param string $path
        *	return array
    */
    public static function GetPicSize($img, $path)
    {
        $img_data = getimagesize(PATH_site .'fileadmin/'. $path . $img);
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
    public function rand_string($lng=8)
    {
        mt_srand((double)microtime()*1000000);
        $charset = "abcdefghijklmnpqrstuvwxyz";
        $length	= strlen($charset)-1;
        $code		= '';
        for ($i=0;$i<$lng;$i++) {
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
    public static function debug($function='', $info='', $new=false)
    {
        $divider = "\n---------------------------------------\n";
        $debuginfo = '';
        if ($new == false) {
            $debuginfo = file_get_contents(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3conf/ext/mmimagemap/debug.txt'));
        }
        $debug = fopen(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3conf/ext/mmimagemap/debug.txt'), 'w+');
        $fdist = (strlen($debuginfo) == 0) ? '' : "\n";
        $debuginfo .= $fdist.'#######################################'."\n".$function.' ';
        $debuginfo .= '['.date('Y-m-d H:i:s').']'.$divider;
        $debuginfo .= $info;
        $debuginfo .= "\n#######################################\n";
        fputs($debug, $debuginfo);
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
    public static function normalizetoUTF8($string, $filepath='', $save=false)
    {
        $convertedstring = $string;
        $encoding = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::detect_utf_encoding($string);
        if ($encoding != 'UTF-8') {
            $convertedstring = mb_convert_encoding($string, 'UTF-8', $encoding);
        }

        if ($save == true && strlen($filepath) != 0) {
            if (file_exists($filepath)) {
                $fp = fopen($filepath, 'w');
                fputs($fp, $string);
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
    public static function purifyString($string)
    {
        $string = strip_tags($string);
        $string = str_replace(array("\n","\c","\t"), '', $string);
        $string = trim(rtrim($string));
        return $string;
    }
    
    /**
        * returns HEX color to RGB
        *	@param string
        *	return array
        */
    public static function ConvertToRGB($hexcolor)
    {
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
    public static function detect_utf_encoding($string)
    {
        define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
        define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
        define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
        define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
        define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
            
        $text = $string;
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 3);
     
        if ($first3 == UTF8_BOM) {
            return 'UTF-8';
        } elseif ($first4 == UTF32_BIG_ENDIAN_BOM) {
            return 'UTF-32BE';
        } elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) {
            return 'UTF-32LE';
        } elseif ($first2 == UTF16_BIG_ENDIAN_BOM) {
            return 'UTF-16BE';
        } elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) {
            return 'UTF-16LE';
        }
    }
    
    /**
        *	removes a byte order mark from a string
        *	return string
    */
    public static function remove_utf8_bom($text)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
    
    /**
        *	returns the last part of a string which is presumed to be a directory.
        * If it is not a directory, the string is returned.
        *	@param string
        *	return string
    */
    public static function getlaststring($str)
    {
        $strparts = array();
        if (preg_match("/\\\\/", $str)) {
            $strparts = explode("\\", $str);
        }
        if (preg_match("/\//", $str)) {
            $strparts = explode("/", $str);
        }
        if (count($strparts) > 0) {
            return $strparts[count($strparts)-1];
        } else {
            return $str;
        }
    }
}


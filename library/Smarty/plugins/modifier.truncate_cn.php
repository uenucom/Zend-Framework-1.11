<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * 字符截取 Smarty truncate modifier plugin
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   tianhm<tianhm at gucas dot ac dot cn>
 * @param string $string
 * @param int $length
 * @param string $code
 * @param string $etc
 * @return string
 */

function smarty_modifier_truncate_cn($string, $length = 80, $code = 'UTF-8', $etc = '')
{

	if(strtoupper($code) == 'UTF-8'){
		$result = '';
		$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
		$strlen = strlen($string);

		for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
		{
			if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')){
				if ($length < 1.0)
				{
					break;
				}

				$result .= substr($string, $i, $number);
				$length -= 1.0;
				$i += $number - 1;
			}else{
				$result .= substr($string, $i, 1);
				$length -= 0.5;
			}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
		
		if ($i < $strlen)
		{
			$result .= $etc;
		}
		return $result;
		
	}elseif (strtoupper($code) == 'GB2312'){
		$result = '';
		$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'GB2312');
		$strlen = strlen($string);

		for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
		{
			if (ord(substr($string, $i, 1)) > 128){
				if ($length < 1.0)
				{
					break;
				}
				$result .= substr($string, $i, 2);
				$length -= 1.0;
				$i++;
			}else{
				$result .= substr($string, $i, 1);
				$length -= 0.5;
			}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'GB2312');
		if ($i < $strlen)
		{
			$result .= $etc;
		}
		return $result;
		
	}else{
		if ($length == 0)
		return '';
		if($code == 'UTF-8'){
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		}
		else{
			$pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
		}
		preg_match_all($pa, $string, $t_string);
		if(count($t_string[0]) > $length){
			return join('', array_slice($t_string[0], 0, $length)).$etc;
		}
		return join('', array_slice($t_string[0], 0, $length));
	}
}

?>

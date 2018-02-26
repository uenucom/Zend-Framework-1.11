<?php
require_once 'Bingo/String.php';
require_once 'Bingo/View/Script.php';
require_once 'Bingo/Http/Request.php';
/**
 * 以下函数主要是给FE使用
 */
function h($strVar, $strEncode = 'GB18030'/*UTF8DIFF*/)
{
	return Bingo_String::escapeHtml($strVar,$strEncode);
}
function htmlAll($strVar, $strEncode = 'GB18030'/*UTF8DIFF*/)
{
	return Bingo_String::escapeHtmlAll($strVar,$strEncode);
}
function j($strVar, $strEncode = 'GB2312'/*UTF8DIFF*/)
{
	return Bingo_String::escapeJs($strVar,$strEncode);
}
function u($strVar)
{
	return Bingo_String::escapeUrl($strVar);
}
function json($arrVar, $strEncode = 'GBK'/*UTF8DIFF*/)
{
	return Bingo_String::array2json($arrVar, $strEncode);
}
function json2array($strVar, $strToEncode='GBK'/*UTF8DIFF*/)
{
	return Bingo_String::json2array($strVar, $strToEncode);
}
function c($string, $intLen = 80, $strEtc = '...', $strEncode = 'GBK'/*UTF8DIFF*/)
{
	return Bingo_String::truncate($string, $intLen, $strEtc, $strEncode);
}

function CONF($strConfKey, $strTypeKey='', $mixDefaultValue='')
{
	return Bingo_View_Script::getInstance()->conf($strConfKey, $strTypeKey, $mixDefaultValue);
}

function GET($strKey, $mixDefaultValue='')
{
	return Bingo_Http_Request::getGet($strKey, $mixDefaultValue);
}

function POST($strKey, $mixDefaultValue='')
{
	return Bingo_Http_Request::getPost($strKey, $mixDefaultValue);
}

function REQUEST($strKey, $mixDefaultValue='')
{
    return Bingo_Http_Request::get($strKey, $mixDefaultValue);
}

function COOKIE($strKey, $mixDefaultValue='')
{
	return Bingo_Http_Request::getCookie($strKey, $mixDefaultValue);
}

function IFELSE($bolVar, $mixValue, $mixFalseValue)
{
	return ($bolVar)?$mixValue:$mixFalseValue;
}

function helper()
{
	return Bingo_View_Script::getInstance()->getObjHelper();
}

function ContentType($strType, $strCharSet='GBK'/*UTF8DIFF*/)
{
    require_once 'Bingo/Http/Response.php';
    $strContentType = 'text/html';
    switch ($strType) {
        case 'json':
            $strContentType = 'application/json';
            break;
        case 'wml':
            $strContentType = 'text/vnd.wap.wml';
            break;
        case 'xml':
            $strContentType = 'text/xml';
            break;
        case 'xhtml':
            $strContentType = 'application/xhtml+xml';
            break;
        case 'html':
            $strContentType = 'text/html';
            break;
        default:
            $strContentType = $strType;
            break;
    }
    Bingo_Http_Response::contextType($strContentType, $strCharSet);
}
/**
 * 空格转化，&#160;  or &nbsp;
 * @param $strVar
 * @param $strSpace
 */
function spaceFormat($strVar, $strSpace='&nbsp;')
{
    //TODO
    return mb_ereg_replace(' ', $strSpace, $strVar);
}
function spaceAndDonaFormat($strVar, $strSpace='&nbsp;')
{
    return str_replace(array('$', ' '), array('$$', $strSpace), $strVar);
}
/**
 * xhtml/wml 转义函数转换
 * @param $strType
 */
class Bingo_View_Function_Escape
{
    public static $_strType = 'xhtml';
}
function escapeSwitch($strType='xhtml') 
{
    Bingo_View_Function_Escape::$_strType = $strType;
}
/**
 * h(),xhtml里的html转义
xml的转义，对于< 、>、空格、&等
w(),wml里的html转义
和h转义类似，但是增加对于$符号的转义，$转义为$$
 * @param $strVar
 * @param $strEncode
 */
function xh($strVar, $strEncode = 'GB18030'/*UTF8DIFF*/)
{
    $strVar = Bingo_String::escapeHtml($strVar,$strEncode);
    if (Bingo_View_Function_Escape::$_strType == 'xhtml') {
        //转义空格
        $strVar = spaceFormat($strVar, '&#160;');
    } else {
        //转义空格+dona
        $strVar = spaceAndDonaFormat($strVar, '&nbsp;');
    }
    return $strVar;
}
/**
 * m(),xhtml里的
n(),wml里的
由于上面h/w的转义，会把空格转义为&nbsp;，在提交表单域里，会导致编码问题。
m/n会将空格转义为实体字符，其他和上面一致
 * @param unknown_type $strVar
 * @param unknown_type $strEncode
 */
function xhf($strVar, $strEncode = 'GB18030'/*UTF8DIFF*/)
{
    $strVar = Bingo_String::escapeHtml($strVar,$strEncode);
    if (Bingo_View_Function_Escape::$_strType == 'xhtml') {
        //转义空格
        $strVar = spaceFormat($strVar, '&#160;');
    } else {
        //转义空格+dona
        $strVar = spaceAndDonaFormat($strVar, '&#160;');
    }
    return $strVar;
}

<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */


function smarty_modifier_filetype2Units($FileType)
{

    $AllType = array("doc", "docx", "ppt", "pptx", "xls", "xlsx", "psd", "ai", "pdf", "swf", "flv", "html", "php", "jsp", "htm", "wmv", "mkv", "rmvb", "mp3", "zip", "exe", "txt", "rar", "jpg", "png", "gif", "bmp");
    if (in_array(strtolower($FileType), $AllType)) {
        return $FileType;
    }else{
        return '';
    }
}

/* vim: set expandtab: */

?>

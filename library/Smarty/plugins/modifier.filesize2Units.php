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


function smarty_modifier_filesize2Units ($FileSizeNum)
{
    $T  = 1024*1024*1024;
    $G  = 1024*1024*1024;
    $M  = 1024*1024;
    $K  = 1024;
    if($FileSizeNum > 0){
        if($FileSizeNum > $T){
            $num = number_format($FileSizeNum/$T, 2);
            return $num.' TB';
        }
        if($FileSizeNum > $G)
        {
            $num = number_format($FileSizeNum/$G, 2);
            return $num.' GB';
        }
        if($FileSizeNum > $M)
        {
            $num = number_format($FileSizeNum/$M, 2);
            return $num.' MB';
        }
        if($FileSizeNum > $K)
        {
            $num = number_format($FileSizeNum/$K, 2);
            return $num.' KB';
        }
        else{
            return $FileSizeNum.' Byte';
        }
    }
}

/* vim: set expandtab: */

?>

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

function smarty_modifier_truncate_replace($string, $from='', $to='')
{
    return str_replace($from, $to, $string);
}

?>

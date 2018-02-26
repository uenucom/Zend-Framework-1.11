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

function smarty_modifier_time2remain ($time) 
{ 
    $difftime   = $time-time();
    if($difftime >0){
    $days   = intval($difftime/86400);
    $remain = $difftime%86400;
    $hours  = intval($remain/3600);
    $remain = $remain%3600;
    $mins   = intval($remain/60);
    $secs   = $remain%60;
    $returntime = '';
    //$returntime = $days.'天 '.$hours.'小时'.$mins.'分'.$secs.'秒';
    if($days >0){$returntime .= $days.'天 ';}
    if($hours >0){$returntime .= $hours.'小时';}
    if($mins >0){$returntime .= $mins.'分钟';}
    //if($secs >0){$returntime .= $secs.'秒';}
    return $returntime; 
    }else{
        return '已过期';
    }
} 

/* vim: set expandtab: */

?>

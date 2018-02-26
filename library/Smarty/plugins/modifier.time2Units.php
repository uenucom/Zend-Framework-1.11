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

function smarty_modifier_time2Units ($time)
{
    if($time>0){
        $days   = intval($time/86400);
        $remain = $time%86400;
        $hours  = intval($remain/3600);
        $remain = $remain%3600;
        $mins   = intval($remain/60);
        $secs   = $remain%60;
        $returntime = '';
        //$returntime = $days.'天 '.$hours.'小时'.$mins.'分'.$secs.'秒';
        //return $returntime;
        if($days >0){$returntime .= $days.'天 ';}
        if($hours >0){$returntime .= $hours.'小时';}
        if($mins >0){$returntime .= $mins.'分';}
        if($secs >0){$returntime .= $secs.'秒';}
        return $returntime;
    }
}

/* vim: set expandtab: */

?>

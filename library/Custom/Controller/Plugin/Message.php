<?php

class Custom_Controller_Plugin_Message extends Zend_Controller_Plugin_Abstract {

    public function showMsg($msg, $url = 'javascript:history.back(-1);', $float = '') {

        echo '<div class="centerDiv" style="' . $float . '">';

        echo '<div class="tishik">';

        echo '<div class="tishichar">提示框</div>';

        echo '<div class="tishiyu">' . $msg . '</div>';

        echo '<div class="tiaozhuan"><a href="' . $url . '">自动跳转到您所需的页面</a></div>';

        echo '</div>';

        echo '</div>';

        echo '<div style="clear:both;"></div>';

        echo '<meta http-equiv="refresh" content="3;url=' . $url . '">';
    }

    public function getAlertMsg($msg, $lang = 'cn') {

        $alert = $lang == 'cn' ? '提示框' : 'Tips';

        $thisString = '<div class="centerDiv">';

        $thisString .= '<div class="tishik">';

        $thisString .= '<div class="tishichar">' . $alert . '</div>';

        $thisString .= '<div class="tiaozhuan"> 　</div>';

        $thisString .= '<div class="tishiyu">' . $msg . '</div>';

        $thisString .= '</div>';

        $thisString .= '</div>';

        return $thisString;
    }

    public function gotoMsg($url) {

        header("location:" . $url);
    }

    public function getOption($thisArray, $name1, $name2, $thisValue) {
        $count = count($thisArray);
        $optionString = "";
        for ($i = 0; $i < $count; $i ++) {

            if ($thisArray [$i] [$name1] == $thisValue) {
                $isSelected = "selected='selected'";
            }
            $optionString .= '<option value="' . $thisArray [$i] [$name1] . '" ' . $isSelected . '>' . $thisArray [$i] [$name2] . '</option>';
            $isSelected = "";
        }
        return $optionString;
    }

    public function getOption2($thisArray, $thisValue) {
        $count = count($thisArray);
        $optionString = "";
        for ($i = 0; $i < $count; $i ++) {
            if ($thisArray [$i] == $thisValue) {
                $isSelected = "selected='selected'";
            }
            $optionString .= '<option value="' . $thisArray [$i] . '" ' . $isSelected . '>' . $thisArray [$i] . '</option>';
            $isSelected = "";
        }
        return $optionString;
    }

    public function excuteScript($script) {
        $thisString = '<script language="javascript" type="text/javascript">';
        $thisString .= $script;
        $thisString .= '</script>';
        return $thisString;
    }

}

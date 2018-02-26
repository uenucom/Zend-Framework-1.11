<?php

/**
 * BBCode
 * format text marked up with bbcode tags to html
 */
class Custom_Controller_Plugin_BBCode {

    private static function _format_bbcode($string) {
        while (preg_match('|\[([a-z]+)=?(.*?)\](.*?)\[/\1\]|', $string, $part, PREG_OFFSET_CAPTURE)) {
            $part[2][0] = str_replace('"', "", $part[2][0]);
            $part[2][0] = str_replace("'", "", $part[2][0]);
            $part[3][0] = self::_format_bbcode($part[3][0]);
            switch ($part[1][0]) {
                //处理加粗 斜体 下划线的元素
                case 'b':
                case 'i':
                case 'u':
                    $replace = sprintf('<%s> %s </%s>', $part[1][0], $part[3][0], $part[1][0]);
                    break;

                //处理代码元素
                case 'code':
                    $replace = '<pre>' . $part[3][0] . '</pre>';
                    break;

                //处理电子邮件元素
                case 'email':
                    $replace = sprintf('<a href="mailto:%s"> %s </a>', $part[3][0], $part[3][0]);
                    break;

                //处理大小样式
                case 'size':
                    $replace = sprintf('<span style="font-size: %s"> %s </span>', $part[2][0], $part[3][0]);
                    break;

                //处理引用元素
                case 'quote':
                    $replace = (empty($part[2][0])) ? ('<blockquote><p>' . $part[3][0] . '</p></blockquote>') : sprintf('<blockquote><p> %s wrote:<br />%s</p>' .
                                    '</blockquote>', $part[2][0], $part[3][0]);
                    break;

                //处理图像元素
                case 'image':
                    $replace = '<img src="' . $part[3][0] . '" alt="" />';
                    break;

                //处理超链接
                case 'url':
                    $replace = sprintf('<a href="%s"> %s </a>', (!empty($part[2][0])) ? $part[2][0] : $part[3][0], $part[3][0]);
                    break;

                case 'list':
                    $replace = str_replace('[*]', '</li><li>', $part[3][2]);
                    $replace = '<x>' . $replace;
                    switch ($part[2][0]) {
                        case 'i':
                            $replace = str_replace('<x></li>', '<ol style="list-style-type: decimal">', $replace . '</ol>');
                            break;

                        case 'A':
                            $replace = str_replace('<x></li>', '<ol style="list-style-type: upper-alpha">', $replace . '</ol>');
                            break;

                        case 'a':
                            $replace = str_replace('<x></li>', '<ol style="list-style-type: lower-alpha">', $replace . '</ol>');
                            break;

                        default:
                            $replace = str_replace('<x></li>', '<ul>', $replace . '</ol>');
                            break;
                    }

                default:
                    $replace = $part[3][0];
                    break;
            }
            $string = substr_replace($string, $replace, $part[0][1], strlen($part[0][0]));
        }
        return $string;
    }

    public static function format($string) {
        $string = BBCode::_format_bbcode(strip_tags($string));
        $string = str_replace("\r\n\r\n", '</p><p>', $string);
        $string = str_replace("\n\n", '</p><p>', $string);
        $string = str_replace("\r\n", '<br />', $string);
        $string = str_replace("\n", '<br />', $string);
        $string = '<p>' . $string . '<br />';
        return $string;
    }

}

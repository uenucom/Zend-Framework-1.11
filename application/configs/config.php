<?php

/**
 * 系统方法
 */
function manage_shutdown() {
    $errorinfo = error_get_last();
    $msginfo['message'] = "请求超时，请重试。 Error[M{$errorinfo['type']}_L{$errorinfo['line']}]";
    echo json_encode($msginfo);
    exit();
}

/**
 * 获取时间
 * @return float
 */
function get_microtime() {
    list($usec, $sec) = explode(' ', microtime());
    return ((float) $usec + (float) $sec);
}

/**
 * 处理数组
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_merge_recursive_distinct(array &$array1, array &$array2) {
    $merged = $array1;
    foreach ($array2 as $key => &$value) {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
            $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
        } else {
            $merged[$key] = $value;
        }
    }
    return $merged;
}

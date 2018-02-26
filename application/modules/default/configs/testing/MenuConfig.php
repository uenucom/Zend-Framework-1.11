<?php

//菜单配置
return array(
    "user" => array(
        "title" => "用户管理",
        "sublist" => array(
            "A" => array('title' => "用户管理", "rel" => "user_list", "href" => "/user", "target" => "navTab", "act" => "default_user_index"),
            "B" => array('title' => "权限模板", "rel" => "role_list", "href" => "/role", "target" => "navTab", "act" => "default_role_index"),
            "C" => array('title' => "批量授权", "rel" => "default_user_batch", "href" => "/user/batch", "target" => "navTab", "act" => "default_user_batch"),
            "D" => array('title' => "权限日志", "rel" => "alog_list", "href" => "/alog", "target" => "navTab", "act" => "default_alog_index"),
            "E" => array('title' => "会话管理", "rel" => "session_list", "href" => "/session", "target" => "navTab", "act" => "default_session_index"),
            "F" => array('title' => "修改密码", "rel" => "user_passwd", "href" => "/user/passwd", "target" => "navTab", "act" => "default_user_passwd"),
        ),
    )
);

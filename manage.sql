-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-02-26 18:26:09
-- 服务器版本： 5.7.18-1
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manage`
--

-- --------------------------------------------------------

--
-- 表的结构 `softrpc_admin_role`
--

CREATE TABLE `softrpc_admin_role` (
  `role_id` int(10) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_intro` text,
  `add_time` timestamp NULL DEFAULT NULL,
  `enable` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `softrpc_user_info`
--

CREATE TABLE `softrpc_user_info` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL COMMENT '用户名',
  `user_password` varchar(32) DEFAULT NULL COMMENT '密码',
  `user_realname` varchar(20) DEFAULT NULL COMMENT '姓名',
  `user_cdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_status` int(11) NOT NULL,
  `user_mdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type` int(1) NOT NULL,
  `user_mobile` varchar(11) NOT NULL,
  `user_mail` varchar(50) NOT NULL,
  `role_name` varchar(50) NOT NULL COMMENT '权限模板',
  `role_acl` longtext NOT NULL COMMENT '菜单',
  `role_menu_old` longtext NOT NULL COMMENT '菜单',
  `role_menu` longtext NOT NULL COMMENT '权限'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `softrpc_user_info`
--

INSERT INTO `softrpc_user_info` (`user_id`, `user_name`, `user_password`, `user_realname`, `user_cdate`, `user_status`, `user_mdate`, `user_type`, `user_mobile`, `user_mail`, `role_name`, `role_acl`, `role_menu_old`, `role_menu`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '2018-02-26 17:08:11', 1, '2018-02-26 17:08:11', 1, '13112345678', 'info@admin.com', 'admin', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `softrpc_user_info_session`
--

CREATE TABLE `softrpc_user_info_session` (
  `session_id` char(32) NOT NULL,
  `save_path` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `user_name` varchar(20) DEFAULT NULL COMMENT '姓名',
  `user_realname` varchar(20) DEFAULT NULL COMMENT '姓名',
  `ip` varchar(16) DEFAULT '' COMMENT 'IP',
  `ua` varchar(200) DEFAULT '' COMMENT 'UA',
  `encryption` varchar(32) DEFAULT NULL COMMENT '验证信息',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `session_data` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `softrpc_user_info_session`
--

INSERT INTO `softrpc_user_info_session` (`session_id`, `save_path`, `name`, `uid`, `user_name`, `user_realname`, `ip`, `ua`, `encryption`, `modified`, `lifetime`, `session_data`) VALUES
('mep4453uruesr3qt55hb96mho4', '', 'PHPSESSID', NULL, NULL, NULL, '', '', NULL, 1516330342, 1440, 'Verification_Code|a:1:{s:9:\"imagecode\";s:5:\"04353\";}__ZF|a:1:{s:17:\"Verification_Code\";a:1:{s:3:\"ENT\";i:1516330462;}}');

-- --------------------------------------------------------

--
-- 表的结构 `softrpc_user_role_log`
--

CREATE TABLE `softrpc_user_role_log` (
  `id` int(10) NOT NULL COMMENT 'ID',
  `user_id` int(10) NOT NULL COMMENT 'user_id',
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `log` text NOT NULL COMMENT 'log内容',
  `opname` varchar(20) NOT NULL COMMENT 'op',
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `softrpc_user_role_log`
--

INSERT INTO `softrpc_user_role_log` (`id`, `user_id`, `username`, `log`, `opname`, `datetime`) VALUES
(1, 113, '赵都', '开通权限：浏览(操作日志),查看详情(操作日志)', '赵都', '2014-11-28 02:23:58'),
(2, 69, '田慧民', '开通权限：浏览(操作日志),查看详情(操作日志),添加(用户管理),导出excel(用户管理),浏览权限(用户管理),角色模板(用户管理),更新(用户管理)', '赵都', '2014-11-29 10:01:35'),
(3, 2, 'test', '开通权限：充值卡详细信息(抽奖管理后台),列表(抽奖管理后台)', '赵都', '2014-11-30 07:50:06'),
(4, 2, 'test', '开通权限：首页(普通推荐位设置)', '赵都', '2014-12-02 06:48:21'),
(5, 2, 'test', '禁用权限：首页(普通推荐位设置)|开通权限：案例展示(合作案例),导入(游戏结算数据导入),列表(高速下载管理),修改状态(高速下载管理)', '赵都', '2014-12-02 06:49:06'),
(6, 2, 'test', '开通权限：添加数据(来电记录),default_customer_ajaxgname(来电记录),历史详情查看(来电记录),default_customer_excel(来电记录),转移频道(来电记录),线上反馈列表页(来电记录),default_customer_history(来电记录),default_customer_historydetail(来电记录),default_customer_messagehandle(来电记录),发送短信(来电记录),不需处理(来电记录),客服工作记录(来电记录)', '赵都', '2014-12-02 06:52:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `softrpc_admin_role`
--
ALTER TABLE `softrpc_admin_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `softrpc_user_info`
--
ALTER TABLE `softrpc_user_info`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `softrpc_user_info_session`
--
ALTER TABLE `softrpc_user_info_session`
  ADD PRIMARY KEY (`session_id`,`save_path`,`name`);

--
-- Indexes for table `softrpc_user_role_log`
--
ALTER TABLE `softrpc_user_role_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `softrpc_admin_role`
--
ALTER TABLE `softrpc_admin_role`
  MODIFY `role_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `softrpc_user_info`
--
ALTER TABLE `softrpc_user_info`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `softrpc_user_role_log`
--
ALTER TABLE `softrpc_user_role_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2488;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

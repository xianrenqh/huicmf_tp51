/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50725
 Source Host           : 127.0.0.1:3306
 Source Schema         : 1038huicmf_test2

 Target Server Type    : MySQL
 Target Server Version : 50725
 File Encoding         : 65001

 Date: 14/11/2019 14:36:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for hui_admin
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin`;
CREATE TABLE `hui_admin`  (
  `adminid` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `roleid` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `realname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logintime` int(10) UNSIGNED NULL DEFAULT 0,
  `loginip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `addtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `addpeople` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` int(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`adminid`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_admin
-- ----------------------------
INSERT INTO `hui_admin` VALUES (1, 'admin', '725dfbaca5be34807d740c468456e36a', 1, '超级管理员', '', '762229008@qq.com', 1573690673, '127.0.0.1', 1573088126, '1', 1);
-- ----------------------------
-- Table structure for hui_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_log`;
CREATE TABLE `hui_admin_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `action` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `querystring` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `adminid` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `adminname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `logtime`(`logtime`) USING BTREE,
  INDEX `adminid`(`adminid`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for hui_admin_login_log
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_login_log`;
CREATE TABLE `hui_admin_login_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logintime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loginip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `address` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `loginresult` tinyint(1) NOT NULL DEFAULT 0 COMMENT '登录结果1为登录成功0为登录失败',
  `cause` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for hui_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_role`;
CREATE TABLE `hui_admin_role`  (
  `roleid` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rolename` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `disabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`roleid`) USING BTREE,
  INDEX `disabled`(`disabled`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_admin_role
-- ----------------------------
INSERT INTO `hui_admin_role` VALUES (1, '超级管理员', '（超级管理员） ', 0, 0);

-- ----------------------------
-- Table structure for hui_admin_role_priv
-- ----------------------------
DROP TABLE IF EXISTS `hui_admin_role_priv`;
CREATE TABLE `hui_admin_role_priv`  (
  `roleid` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `m` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `c` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `a` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `data` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `authid` int(6) NULL DEFAULT NULL,
  INDEX `roleid`(`roleid`, `m`, `c`, `a`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for hui_attachment
-- ----------------------------
DROP TABLE IF EXISTS `hui_attachment`;
CREATE TABLE `hui_attachment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` char(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `originname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `filename` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `filepath` char(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `filesize` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `fileext` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `isimage` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `downloads` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `userid` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `uploadtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `uploadip` char(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `userid_index`(`userid`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for hui_config
-- ----------------------------
DROP TABLE IF EXISTS `hui_config`;
CREATE TABLE `hui_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置类型',
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置值',
  `fieldtype` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '字段类型',
  `setting` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '字段设置',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_config
-- ----------------------------
INSERT INTO `hui_config` VALUES (1, 'site_name', 1, '站点名称', 'HuiCmf - 演示站2', '', '', 1);
INSERT INTO `hui_config` VALUES (2, 'site_url', 1, '站点跟网址', 'http://huicmf2.com/', '', '', 1);
INSERT INTO `hui_config` VALUES (3, 'admin_log', 3, '启用后台管理操作日志', '0', '', '', 1);
INSERT INTO `hui_config` VALUES (4, 'site_keyword', 1, '站点关键字', 'huicmf', '', '', 1);
INSERT INTO `hui_config` VALUES (5, 'site_copyright', 1, '网站版权信息', 'Powered By HuiCMF内容管理系统 © 2018-2020 小灰灰工作室', '', '', 1);
INSERT INTO `hui_config` VALUES (6, 'site_beian', 1, '站点备案号', '京ICP备666666号', '', '', 1);
INSERT INTO `hui_config` VALUES (7, 'site_description', 1, '站点描述', '我是描述', '', '', 1);
INSERT INTO `hui_config` VALUES (8, 'site_code', 1, '统计代码', '', '', '', 1);
INSERT INTO `hui_config` VALUES (9, 'admin_prohibit_ip', 3, '禁止登录后台的IP', '', '', '', 1);
INSERT INTO `hui_config` VALUES (10, 'mail_server', 4, 'SMTP服务器', 'smtp.exmail.qq.com', '', '', 1);
INSERT INTO `hui_config` VALUES (11, 'mail_port', 4, 'SMTP服务器端口', '465', '', '', 1);
INSERT INTO `hui_config` VALUES (12, 'mail_from', 4, 'SMTP服务器的用户邮箱', '', '', '', 1);
INSERT INTO `hui_config` VALUES (13, 'mail_user', 4, 'SMTP服务器的用户帐号', '', '', '', 1);
INSERT INTO `hui_config` VALUES (14, 'mail_pass', 4, 'SMTP服务器的用户密码', '', '', '', 1);
INSERT INTO `hui_config` VALUES (15, 'mail_inbox', 4, '收件邮箱地址', '', '', '', 1);
INSERT INTO `hui_config` VALUES (16, 'mail_auth', 4, 'AUTH LOGIN验证', '1', '', '', 1);
INSERT INTO `hui_config` VALUES (17, 'login_code', 3, '后台登录验证码', '0', '', '', 1);
INSERT INTO `hui_config` VALUES (18, 'upload_maxsize', 2, '允许上传附件大小', '3048', '', '', 1);
INSERT INTO `hui_config` VALUES (19, 'watermark_enable', 2, '是否开启图片水印', '1', '', '', 1);
INSERT INTO `hui_config` VALUES (20, 'watermark_name', 2, '水印图片名称', 'mark.png', '', '', 1);
INSERT INTO `hui_config` VALUES (21, 'watermark_position', 2, '水印的位置', '9', '', '', 1);
INSERT INTO `hui_config` VALUES (22, 'watermark_touming', 2, '水印透明度', '100', '', '', 1);
INSERT INTO `hui_config` VALUES (23, 'upload_types', 2, '允许上传类型', 'zip|rar|mp3|mp4|jpg|jpeg|png|gif|bmp', '', '', 1);
INSERT INTO `hui_config` VALUES (24, 'upload_mode', 2, '图片上传方式', 'local', '', '', 1);
INSERT INTO `hui_config` VALUES (25, 'ftp_host', 2, 'FTP服务器地址', '', '', ' ', 1);
INSERT INTO `hui_config` VALUES (26, 'ftp_port', 2, 'FTP端口', '21', '', ' ', 1);
INSERT INTO `hui_config` VALUES (27, 'ftp_user', 2, 'FTP账号', '', '', ' ', 1);
INSERT INTO `hui_config` VALUES (28, 'ftp_pwd', 2, 'FTP密码', '', '', ' ', 1);
INSERT INTO `hui_config` VALUES (29, 'ftp_url', 2, '外链url地址', '', '', ' ', 1);
INSERT INTO `hui_config` VALUES (30, 'ftp_path', 2, '文章保存路径', '/uploads/', '', ' ', 1);


-- ----------------------------
-- Table structure for hui_content
-- ----------------------------
DROP TABLE IF EXISTS `hui_content`;
CREATE TABLE `hui_content`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `catid` smallint(6) NOT NULL DEFAULT 0,
  `userid` smallint(10) NOT NULL DEFAULT 0,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `inputtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `keywords` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `setting` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status` smallint(1) NOT NULL DEFAULT 0,
  `collect` smallint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for hui_menu
-- ----------------------------
DROP TABLE IF EXISTS `hui_menu`;
CREATE TABLE `hui_menu`  (
  `id` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `parentid` smallint(6) NOT NULL DEFAULT 0,
  `m` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `c` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `a` char(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `data` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `listorder` smallint(6) UNSIGNED NOT NULL DEFAULT 0,
  `display` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `listorder`(`listorder`) USING BTREE,
  INDEX `parentid`(`parentid`) USING BTREE,
  INDEX `module`(`m`, `c`, `a`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of hui_menu
-- ----------------------------
INSERT INTO `hui_menu` VALUES (1, '内容管理', 0, '', '', '', 'icon-read', 1, 1);
INSERT INTO `hui_menu` VALUES (2, '管理员管理', 0, '', '', '', 'icon-profile', 2, 1);
INSERT INTO `hui_menu` VALUES (3, '会员管理', 0, '', '', '', 'icon-friend', 3, 0);
INSERT INTO `hui_menu` VALUES (4, '模块管理', 0, '', '', '', 'icon-apps', 4, 1);
INSERT INTO `hui_menu` VALUES (5, '个人信息', 0, '', '', '', 'icon-album', 5, 1);
INSERT INTO `hui_menu` VALUES (6, '系统管理', 0, '', '', '', ' icon-settings', 6, 1);
INSERT INTO `hui_menu` VALUES (7, '数据管理', 0, '', '', '', 'icon-lightauto', 7, 1);
INSERT INTO `hui_menu` VALUES (8, '管理员列表', 2, 'admin', 'admin_manage', 'init', '', 1, 1);
INSERT INTO `hui_menu` VALUES (9, '角色管理', 2, 'admin', 'role', 'init', '', 2, 1);
INSERT INTO `hui_menu` VALUES (10, '添加管理员', 8, 'admin', 'admin_manage', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (11, '编辑管理员', 8, 'admin', 'admin_manage', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (12, '添加角色', 9, 'admin', 'role', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (13, '编辑角色', 9, 'admin', 'role', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (14, '权限管理', 9, 'admin', 'role', 'role_priv', '', 0, 0);
INSERT INTO `hui_menu` VALUES (15, '修改个人信息', 5, 'admin', 'admin_manage', 'public_edit_info', '', 1, 1);
INSERT INTO `hui_menu` VALUES (16, '修改密码', 5, 'admin', 'admin_manage', 'public_edit_pwd', '', 2, 1);
INSERT INTO `hui_menu` VALUES (18, '系统设置', 6, 'admin', 'system_manage', 'init', '', 0, 1);
INSERT INTO `hui_menu` VALUES (19, '后台菜单管理', 6, 'admin', 'menu', 'init', '', 2, 1);
INSERT INTO `hui_menu` VALUES (20, '菜单排序', 19, 'admin', 'menu', 'order', '', 0, 0);
INSERT INTO `hui_menu` VALUES (21, '添加菜单', 19, 'admin', 'menu', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (22, '编辑菜单', 19, 'admin', 'menu', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (23, '删除菜单', 19, 'admin', 'menu', 'delete', '', 3, 0);
INSERT INTO `hui_menu` VALUES (24, '数据备份', 7, 'admin', 'database', 'init', '', 1, 1);
INSERT INTO `hui_menu` VALUES (25, '数据还原', 7, 'admin', 'database', 'databack_list', '', 2, 1);
INSERT INTO `hui_menu` VALUES (26, '修复表', 24, 'admin', 'database', 'public_repair', '', 1, 0);
INSERT INTO `hui_menu` VALUES (27, '优化表', 24, 'admin', 'database', 'public_optimize', '', 2, 0);
INSERT INTO `hui_menu` VALUES (28, '数据导入', 25, 'admin', 'database', 'import', '', 1, 0);
INSERT INTO `hui_menu` VALUES (29, '备份文件下载', 25, 'admin', 'database', 'databack_down', '', 2, 0);
INSERT INTO `hui_menu` VALUES (30, '备份文件删除', 25, 'admin', 'database', 'databack_del', '', 3, 0);
INSERT INTO `hui_menu` VALUES (31, '立即备份', 24, 'admin', 'database', 'export_list', '', 0, 0);
INSERT INTO `hui_menu` VALUES (32, '后台操作日志', 6, 'admin', 'admin_log', 'init', '', 11, 1);
INSERT INTO `hui_menu` VALUES (33, '后台登录日志', 6, 'admin', 'admin_log', 'admin_login_log_list', '', 12, 1);
INSERT INTO `hui_menu` VALUES (34, '自定义配置', 6, 'admin', 'system_manage', 'user_config_list', '', 2, 1);
INSERT INTO `hui_menu` VALUES (35, '添加配置', 34, 'admin', 'system_manage', 'user_config_add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (36, '编辑配置', 34, 'admin', 'system_manage', 'user_config_edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (37, '批量删除配置', 34, 'admin', 'system_manage', 'user_config_del', '', 3, 0);
INSERT INTO `hui_menu` VALUES (38, 'SQL命令行', 6, 'admin', 'sql', 'init', '', 4, 1);
INSERT INTO `hui_menu` VALUES (39, '提交命令行', 38, 'admin', 'sql', 'do_sql', '', 1, 0);
INSERT INTO `hui_menu` VALUES (40, '模块管理', 4, 'admin', 'module', 'init', '', 1, 1);
INSERT INTO `hui_menu` VALUES (41, '模块安装', 40, 'admin', 'module', 'install', '', 1, 0);
INSERT INTO `hui_menu` VALUES (42, '模块卸载', 40, 'admin', 'module', 'uninstall', '', 2, 0);
INSERT INTO `hui_menu` VALUES (43, '内容管理', 1, 'admin', 'content', 'init', '', 1, 1);
INSERT INTO `hui_menu` VALUES (44, '添加内容', 1, 'admin', 'content', 'add', '', 2, 0);
INSERT INTO `hui_menu` VALUES (45, '编辑内容', 1, 'admin', 'content', 'edit', '', 3, 0);
INSERT INTO `hui_menu` VALUES (46, '删除一条内容', 1, 'admin', 'content', 'del_one', '', 48, 0);
INSERT INTO `hui_menu` VALUES (47, '单条删除配置', 34, 'admin', 'system_manage', 'user_config_del_one', '', 3, 0);
INSERT INTO `hui_menu` VALUES (48, '删除管理员', 8, 'admin', 'admin_manage', 'del', '', 3, 0);
INSERT INTO `hui_menu` VALUES (49, '删除角色', 9, 'admin', 'role', 'del', '', 4, 1);
INSERT INTO `hui_menu` VALUES (50, '分类管理', 1, 'admin', 'type', 'init', '', 2, 1);
INSERT INTO `hui_menu` VALUES (51, '添加分类', 50, 'admin', 'type', 'add', '', 1, 0);
INSERT INTO `hui_menu` VALUES (52, '修改分类', 50, 'admin', 'type', 'edit', '', 2, 0);
INSERT INTO `hui_menu` VALUES (53, '删除分类', 50, 'admin', 'type', 'del', '', 3, 0);
INSERT INTO `hui_menu` VALUES (54, '列表页排序', 50, 'admin', 'type', 'order', '', 6, 0);
INSERT INTO `hui_menu` VALUES (55, '列表页更改状态', 50, 'admin', 'type', 'change_status', '', 5, 0);
INSERT INTO `hui_menu` VALUES (56, '上传案例', 1, 'admin', 'content', 'test', '', 99, 1);
INSERT INTO `hui_menu` VALUES (57, '文件管理器', 4, 'file', 'file', 'index', '', 5, 1);
INSERT INTO `hui_menu` VALUES (58, '编辑文件', 57, 'file', 'file', 'edit', '', 1, 0);
INSERT INTO `hui_menu` VALUES (59, '删除文件', 57, 'file', 'file', 'del', '', 2, 0);
INSERT INTO `hui_menu` VALUES (60, '重命名文件', 57, 'file', 'file', 'rname', '', 3, 0);
INSERT INTO `hui_menu` VALUES (61, '下载文件', 57, 'file', 'file', 'down', '', 4, 0);

-- ----------------------------
-- Table structure for hui_type
-- ----------------------------
DROP TABLE IF EXISTS `hui_type`;
CREATE TABLE `hui_type`  (
  `type_id` int(10) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type_en` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `type_pid` int(10) NULL DEFAULT NULL COMMENT '父级id',
  `type_status` int(1) NULL DEFAULT NULL COMMENT '状态',
  `type_sort` int(10) NULL DEFAULT NULL COMMENT '排序',
  `type_uptime` int(10) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`type_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for hui_module
-- ----------------------------
DROP TABLE IF EXISTS `hui_module`;
CREATE TABLE `hui_module`  (
  `module` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `iscore` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `version` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `setting` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `listorder` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `disabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `installdate` date NOT NULL DEFAULT '2019-01-01',
  `updatedate` date NOT NULL DEFAULT '2019-01-01',
  PRIMARY KEY (`module`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hui_module
-- ----------------------------
INSERT INTO `hui_module` VALUES ('admin', '后台模块', 1, '1.0', '后台模块', '', 0, 0, '2019-01-27', '2019-01-27');
INSERT INTO `hui_module` VALUES ('index', '前台模块', 1, '1.0', '前台模块', '', 0, 0, '2019-01-27', '2019-01-27');
INSERT INTO `hui_module` VALUES ('api', '接口模块', 1, '1.0', '为整个系统提供接口', '', 0, 0, '2019-01-27', '2019-01-27');
INSERT INTO `hui_module` VALUES ('attachment', '附件模块', 1, '1.0', '附件模块', '', 0, 0, '2019-07-10', '2019-07-10');
INSERT INTO `hui_module` VALUES ('file', '文件管理器', 1, '1.0', '文件管理器', '', 0, 0, '2019-12-08', '2019-12-08');

SET FOREIGN_KEY_CHECKS = 1;

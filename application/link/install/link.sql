DROP TABLE IF EXISTS `hui_link`;
CREATE TABLE `hui_link`  (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typeid` smallint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1首页,2列表页,3内容页',
  `linktype` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0:文字链接,1:logo链接',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `username` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `listorder` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未通过,1正常,2未审核',
  `addtime` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `index_typeid`(`typeid`) USING BTREE
) ENGINE = MyISAM  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

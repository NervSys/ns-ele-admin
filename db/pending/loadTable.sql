DROP TABLE IF EXISTS `admin_permission`;
CREATE TABLE `admin_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `control_name` varchar(200) NOT NULL COMMENT '功能名字',
  `model_name` varchar(255) NOT NULL COMMENT '模块名',
  `control_real_name` varchar(200) NOT NULL COMMENT '权限控制器名字',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dedated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `info` varchar(200) NOT NULL DEFAULT '' COMMENT '权限描述',
  `score` varchar(100) NOT NULL DEFAULT '0' COMMENT '权限分值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dedated_at` datetime DEFAULT NULL,
  `sulg` varchar(255) NOT NULL COMMENT '标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT ' 1 男  2 女 0  秘密',
  `age` int(11) NOT NULL DEFAULT '10' COMMENT '年纪',
  `code` varchar(6) NOT NULL COMMENT '用户密码加密 code',
  `company` varchar(255) NOT NULL COMMENT '公司名字',
  `logo` varchar(255) NOT NULL COMMENT '公司logo',
  `country` varchar(255) NOT NULL DEFAULT '中国' COMMENT '国家',
  `province` varchar(255) DEFAULT NULL COMMENT '省份',
  `city` varchar(255) NOT NULL COMMENT '城市',
  `ip` varchar(255) DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dedated_at` datetime DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL COMMENT '角色id',
  `score` varchar(100) NOT NULL DEFAULT '0' COMMENT '本人权限值',
  `phone` varchar(20) DEFAULT NULL COMMENT '电话',
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

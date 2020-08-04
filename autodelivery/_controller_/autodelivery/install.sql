# 创建自动发货插件数据表
CREATE TABLE IF NOT EXISTS `{PREFIX}plugins_autodelivery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `mobile` varchar(16) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '手机号',
  `email` varchar(60) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '邮箱',
  `upd_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET={CHARSET} AUTO_INCREMENT=1 ;
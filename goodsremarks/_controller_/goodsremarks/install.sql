CREATE TABLE IF NOT EXISTS `{PREFIX}plugins_goodsremarks_notes` (`id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '自增id', `goods_id` char(60) NOT NULL DEFAULT '' COMMENT '商品ID', `admin_note` char(255) NOT NULL DEFAULT '' COMMENT '后台备注', `upd_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间') DEFAULT CHARSET={CHARSET};
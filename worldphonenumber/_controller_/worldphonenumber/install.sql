# 增加-国际电话区号-字段
ALTER TABLE `{PREFIX}user` add `plugins_worldphonenumber_phone_code` VARCHAR( 5 ) NULL DEFAULT '' COMMENT '国际电话区号' after `referrer`;
# 增加-国际电话区号-索引
ALTER TABLE `{PREFIX}user` ADD INDEX ( `plugins_worldphonenumber_phone_code` ) ;
# 升级-电话位数- 最高 5位 国际电话区号 + 最高 11位 手机号码
ALTER TABLE `{PREFIX}user` CHANGE `mobile` `mobile` VARCHAR( 16 )
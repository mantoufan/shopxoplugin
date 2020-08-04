# 删除-国际电话区号-索引
ALTER TABLE `{PREFIX}user` DROP INDEX `plugins_worldphonenumber_phone_code`;
# 删除-国际电话区号-字段
ALTER TABLE `{PREFIX}user` DROP `plugins_worldphonenumber_phone_code`;

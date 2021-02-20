<?php
namespace app\plugins\goodsremarks\service;
use think\Db;
use app\service\PluginsService;
use app\service\MessageService;
use app\plugins\goodsremarks\wga\WGA;

class BaseService
{
  public static function BaseConfigSave($params = array())
  {
    $wga= new WGA();
    return $wga->save($params);
  }

  public static function BaseConfig($is_cache = true)
  {
    return PluginsService::PluginsData('goodsremarks', null, $is_cache);
  }
  public static function GoodsDetail($params = [])
  {
    // 请求参数
    $p = [
      [
        'checked_type'    => 'empty',
        'key_name'      => 'id',
        'error_msg'     => '商品id不能为空',
      ]
    ];
    $ret = ParamsChecked($params, $p);
    if($ret !== true)
    {
      return DataReturn($ret, -1);
    }
    $res = Db::name('plugins_goodsremarks_notes')->where(['goods_id'=>$params['id']])->find();
    $ret = array(
			'goods_id' => $params['id'],
      'admin_note' => '',
      'admin_note_upd_time' => ''
    );
    if (!empty($res)) {
      $ret['admin_note'] = $res['admin_note'];
      $ret['admin_note_upd_time'] = $res['upd_time'];
    } 
		return DataReturn('查询成功', 0, $ret);
  }
  public static function GoodsUpdate($params = [])
  {
    $success = false;
    // 请求参数
    $p = [
      [
        'checked_type'    => 'empty',
        'key_name'      => 'id',
        'error_msg'     => '商品id不能为空',
      ]
    ];
    $ret = ParamsChecked($params, $p);
    if($ret !== true)
    {
      return DataReturn($ret, -1);
    }
    // 获取商品信息
    $ret = self::GoodsDetail($params);
    if($ret['code'] !== 0)
    {
      return $ret;
    }
    // 更新数据
    $wga = new WGA();
    $data = $wga->getGoodsUpdateData($params);
    if (isset($data['msg'])) {
      return $data;
    }
    if (!empty($ret['data']['admin_note_upd_time'])) {
      if(Db::name('plugins_goodsremarks_notes')->where(['goods_id'=>$params['id']])->update($data))
      {
				return DataReturn('修改成功', 0);
      }
    } else {
			$data['goods_id'] = $params['id'];
      if(Db::name('plugins_goodsremarks_notes')->insert($data))
      {
        return DataReturn('修改成功', 0);
      }
    }
    
    return DataReturn('备注未修改或备注失败', -100);
  }
  
  public static function GoodsAdminNote($goods_id)
  {
    // 基础配置
    $base = self::BaseConfig();
    if(!empty($base['data']))
    {
      // 列表页查看商品备注
      if(isset($base['data']['is_dispaly_on_list']) && $base['data']['is_dispaly_on_list'] == 1)
      {
        if ($goods_id) {
          $ret = Db::name('plugins_goodsremarks_notes')->where(['goods_id'=>$goods_id])->find();
          if ($ret) {
            return array('admin_note'=>$ret['admin_note'], 'upd_time'=>$ret['upd_time']);
          }
        }
        
      }
    }
    return FALSE;
  }
}
?>
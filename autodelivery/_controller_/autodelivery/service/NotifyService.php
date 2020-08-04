<?php
namespace app\plugins\autodelivery\service;

use think\Db;

class NotifyService
{
    /**
     * 海报数据保存
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-04-22
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    public static function PpsterDataSave($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'empty',
                'key_name'          => 'backdrop',
                'error_msg'         => '请上传海报背景图片',
            ],
            [
                'checked_type'      => 'isset',
                'key_name'          => 'avatar_width',
                'error_msg'         => '请设置头像宽度',
            ],
            [
                'checked_type'      => 'min',
                'key_name'          => 'avatar_width',
                'checked_data'      => 30,
                'error_msg'         => '头像宽度尺寸 30~300 之间',
            ],
            [
                'checked_type'      => 'max',
                'key_name'          => 'avatar_width',
                'checked_data'      => 300,
                'error_msg'         => '头像宽度尺寸 30~300 之间',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'avatar_border_style',
                'checked_data'      => array_column(self::$autodelivery_border_style_list, 'value'),
                'error_msg'         => '头像样式数据值有误',
            ],
            [
                'checked_type'      => 'isset',
                'key_name'          => 'qrcode_width',
                'error_msg'         => '请设置二维码宽度尺寸',
            ],
            [
                'checked_type'      => 'min',
                'key_name'          => 'qrcode_width',
                'checked_data'      => 60,
                'error_msg'         => '二维码宽度尺寸 60~300 之间',
            ],
            [
                'checked_type'      => 'max',
                'key_name'          => 'qrcode_width',
                'checked_data'      => 300,
                'error_msg'         => '二维码宽度尺寸 60~300 之间',
            ],
            [
                'checked_type'      => 'in',
                'key_name'          => 'qrcode_border_style',
                'checked_data'      => array_column(self::$autodelivery_border_style_list, 'value'),
                'error_msg'         => '二维码样式数据值有误',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 数据字段
        $data_field = 'poster_data';

        // 附件
        $data_fields = ['backdrop'];
        $attachment = ResourcesService::AttachmentParams($params, $data_fields);

        // 数据
        $data = [
            'backdrop'              => $attachment['data']['backdrop'],
            'avatar_width'          => empty($params['avatar_width']) ? 60 : intval($params['avatar_width']),
            'qrcode_width'          => empty($params['qrcode_width']) ? 110 : intval($params['qrcode_width']),

            'avatar_top'            => empty($params['avatar_top']) ? 12 : intval($params['avatar_top']),
            'avatar_left'           => empty($params['avatar_left']) ? 119 : intval($params['avatar_left']),

            'nickname_top'          => empty($params['nickname_top']) ? 72 : intval($params['nickname_top']),
            'nickname_left'         => empty($params['nickname_left']) ? 113 : intval($params['nickname_left']),

            'qrcode_top'            => empty($params['qrcode_top']) ? 96 : intval($params['qrcode_top']),
            'qrcode_left'           => empty($params['qrcode_left']) ? 94 : intval($params['qrcode_left']),

            'avatar_border_style'   => isset($params['avatar_border_style']) ? intval($params['avatar_border_style']) : 2,
            'qrcode_border_style'   => isset($params['qrcode_border_style']) ? intval($params['qrcode_border_style']) : 0,

            'nickname_color'        => empty($params['nickname_color']) ? '#666' : $params['nickname_color'],
            'nickname_auto_center'  => isset($params['nickname_auto_center']) ? intval($params['nickname_auto_center']) : 1,
            'operation_time'        => time(),
        ];

        // 原有数据
        $ret = PluginsService::PluginsData('autodelivery', self::$base_config_attachment_field, false);

        // 保存
        $ret['data'][$data_field] = $data;
        return PluginsService::PluginsDataSave(['plugins'=>'autodelivery', 'data'=>$ret['data']]);
    }

    /**
     * 分享海报数据
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-04-22
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    public static function PosterData($params = [])
    {
        // 数据字段
        $data_field = 'poster_data';

        // 获取数据
        $ret = PluginsService::PluginsData('autodelivery', self::$base_config_attachment_field);
        $data = (empty($ret['data']) || empty($ret['data'][$data_field])) ? [] : $ret['data'][$data_field];

        // 数据处理
        if(!isset($params['is_handle_data']) || $params['is_handle_data'] == 1)
        {
            // 背景图片地址
            $data['backdrop_old'] = empty($data['backdrop']) ? '/static/plugins/images/autodelivery/default-backdrop.png' : $data['backdrop'];
            $data['backdrop'] = ResourcesService::AttachmentPathViewHandle($data['backdrop_old']);

            // 头像
            $data['avatar_width'] = empty($data['avatar_width']) ? 60 : intval($data['avatar_width']);
            $data['avatar_top'] = empty($data['avatar_top']) ? 12 : intval($data['avatar_top']);
            $data['avatar_left'] = empty($data['avatar_left']) ? 119 : intval($data['avatar_left']);
            $data['avatar_border_style'] = isset($data['avatar_border_style']) ? intval($data['avatar_border_style']) : 2;

            // 昵称
            $data['nickname_color'] = empty($data['nickname_color']) ? '#666' : $data['nickname_color'];
            $data['nickname_top'] = empty($data['nickname_top']) ? 72 : intval($data['nickname_top']);
            $data['nickname_left'] = empty($data['nickname_left']) ? 113 : intval($data['nickname_left']);
            $data['nickname_auto_center'] = isset($data['nickname_auto_center']) ? intval($data['nickname_auto_center']) : 1;

            // 二维码
            $data['qrcode_width'] = empty($data['qrcode_width']) ? 110 : intval($data['qrcode_width']);
            $data['qrcode_top'] = empty($data['qrcode_top']) ? 96 : intval($data['qrcode_top']);
            $data['qrcode_left'] = empty($data['qrcode_left']) ? 94 : intval($data['qrcode_left']);
            $data['qrcode_border_style'] = isset($data['qrcode_border_style']) ? intval($data['qrcode_border_style']) : 0;
        }

        return DataReturn('处理成功', 0, $data);
    }

    /**
     * 获取用户分销数据
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-06-05
     * @desc    description
     * @param   [int]     $user_id      [用户id]
     */
    public static function UserautodeliveryLevel($user_id)
    {
        $level = [];
        
        // 等级列表数据处理
        $level_params = [
            'where' => [
                'is_enable'     => 1,
            ],
        ];
        $level_list = LevelService::DataList($level_params);
        if(!empty($level_list['data']))
        {
            // 用户是否配置了自定义等级
            // 没有自定义的使用自动模式分配分销等级
            $user_level_id = Db::name('User')->where(['id'=>$user_id])->value('plugins_autodelivery_level');
            if(!empty($user_level_id))
            {
                // 当前等级id数组列
                $level_id_col = array_column($level_list['data'], 'id');
                $level_id_key = array_search($user_level_id, $level_id_col);
                if($level_id_key !== false)
                {
                    $level = $level_list['data'][$level_id_key];
                }
            }
            
            // 自动分配
            if(empty($level))
            {
                // 消费总额（已支付订单）
                $value = self::UserEffectiveOrderTotalPrice($user_id);

                // 匹配相应的等级
                foreach($level_list['data'] as $rules)
                {
                    if(isset($rules['is_enable']) && $rules['is_enable'] == 1 && isset($rules['is_level_auto']) && $rules['is_level_auto'] == 1)
                    {
                        // 0-0
                        if($rules['rules_min'] <= 0 && $rules['rules_max'] <= 0)
                        {
                            $level = $rules;
                            break;
                        }

                        // 0-*
                        if($rules['rules_min'] <= 0 && $rules['rules_max'] > 0 && $value < $rules['rules_max'])
                        {
                            $level = $rules;
                            break;
                        }

                        // *-*
                        if($rules['rules_min'] > 0 && $rules['rules_max'] > 0 && $value >= $rules['rules_min'] && $value < $rules['rules_max'])
                        {
                            $level = $rules;
                            break;
                        }

                        // *-0
                        if($rules['rules_max'] <= 0 && $rules['rules_min'] > 0 && $value > $rules['rules_min'])
                        {
                            $level = $rules;
                            break;
                        }
                    }
                }
            }
        }
        if(empty($level))
        {
            return DataReturn('没有相关等级', -1);
        }
        return DataReturn('处理成功', 0, $level);
    }

    /**
     * 用户有效订单消费金额
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-06-05T22:08:34+0800
     * @param    [int]                   $user_id [用户id]
     */
    public static function UserEffectiveOrderTotalPrice($user_id)
    {
        // 订单状态（0待确认, 1已确认/待支付, 2已支付/待发货, 3已发货/待收货, 4已完成, 5已取消, 6已关闭）
        $where = [
            ['user_id', '=', $user_id],
            ['status', 'in', [2,3,4]],
        ];
        return (float) Db::name('Order')->where($where)->sum('total_price');
    }

    /**
     * 海报清空
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-06-12T20:36:38+0800
     * @param   [array]           $params [输入参数]
     */
    public static function PpsterDelete($params = [])
    {
        $dir_all = ['poster', 'qrcode'];
        foreach($dir_all as $v)
        {
            $dir = ROOT.'public'.DS.'static'.DS.'upload'.DS.'images'.DS.'plugins_autodelivery'.DS.$v;
            if(is_dir($dir))
            {
                // 是否有权限
                if(!is_writable($dir))
                {
                    return DataReturn('目录没权限', -1);
                }

                // 删除目录
                \base\FileUtil::UnlinkDir($dir);
            }
        }

        return DataReturn('操作成功', 0);
    }


    /**
     * 商品海报数据
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-04-22
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    public static function PosterGoodsData($params = [])
    {
        // 数据字段
        $data_field = 'poster_goods_data';

        // 获取数据
        $ret = PluginsService::PluginsData('autodelivery', self::$base_config_attachment_field);
        $data = (empty($ret['data']) || empty($ret['data'][$data_field])) ? [] : $ret['data'][$data_field];

        return DataReturn('处理成功', 0, $data);
    }

    /**
     * 商品海报数据保存
     * @author   Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-04-22
     * @desc    description
     * @param   [array]           $params [输入参数]
     */
    public static function PpsterGoodsDataSave($params = [])
    {
        // 请求参数
        $p = [
            [
                'checked_type'      => 'length',
                'key_name'          => 'bottom_left_text',
                'checked_data'      => '10',
                'is_checked'        => 1,
                'error_msg'         => '底部左侧文本不超过 10 个字符',
            ],
            [
                'checked_type'      => 'length',
                'key_name'          => 'bottom_right_text',
                'checked_data'      => '6',
                'is_checked'        => 1,
                'error_msg'         => '底部右侧文本不超过 6 个字符',
            ],
        ];
        $ret = ParamsChecked($params, $p);
        if($ret !== true)
        {
            return DataReturn($ret, -1);
        }

        // 数据字段
        $data_field = 'poster_goods_data';

        // 数据
        $data = [
            'goods_title_text_color'    => empty($params['goods_title_text_color']) ? '' : $params['goods_title_text_color'],
            'goods_simple_text_color'   => empty($params['goods_simple_text_color']) ? '' : $params['goods_simple_text_color'],

            'bottom_left_text'          => empty($params['bottom_left_text']) ? '' : $params['bottom_left_text'],
            'bottom_left_text_color'    => empty($params['bottom_left_text_color']) ? '' : $params['bottom_left_text_color'],

            'bottom_right_text'         => empty($params['bottom_right_text']) ? '' : $params['bottom_right_text'],
            'bottom_right_text_color'   => empty($params['bottom_right_text_color']) ? '' : $params['bottom_right_text_color'],
        ];

        // 原有数据
        $ret = PluginsService::PluginsData('autodelivery', self::$base_config_attachment_field, false);

        // 保存
        $ret['data'][$data_field] = $data;
        return PluginsService::PluginsDataSave(['plugins'=>'autodelivery', 'data'=>$ret['data']]);
    }

    /**
     * 商品海报清空
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  1.0.0
     * @datetime 2019-06-12T20:36:38+0800
     * @param    [array]           $params [输入参数]
     */
    public static function PpsterGoodsDelete($params = [])
    {
        $path = ROOT.'public'.DS.'static'.DS.'upload'.DS.'images'.DS.'plugins_autodelivery'.DS;
        $dir_all = ['poster_goods_qrcode', 'poster_goods'];
        foreach($dir_all as $v)
        {
            if(is_dir($path.$v))
            {
                // 是否有权限
                if(!is_writable($path.$v))
                {
                    return DataReturn('目录没权限['.$path.$v.']', -1);
                }

                // 删除目录
                \base\FileUtil::UnlinkDir($path.$v);
            }
        }
        return DataReturn('操作成功', 0);
    }

    /**
     * 商品分享地址
     * @author  Devil
     * @blog    http://gong.gg/
     * @version 1.0.0
     * @date    2019-12-09
     * @desc    description
     * @param   [int]          $goods_id [商品id]
     */
    public static function GoodsShareUrl($goods_id)
    {
         // 用户信息
        $user = UserService::LoginUserInfo();
        $url = '';
        if(!empty($user))
        {
            $url = MyUrl('index/goods/index', ['id'=>$goods_id]);
            $referrer = UserService::UserReferrerEncryption($user['id']);
            if(stripos($url, '?') === false)
            {
                $url .= '?referrer='.$referrer;
            } else {
                $url .= '&referrer='.$referrer;
            }
        }
        return $url;
    }


    public static function BaseConfig($is_cache = true)
    {
        $ret = PluginsService::PluginsData('autodelivery', self::$base_config_attachment_field, $is_cache);
        if(!empty($ret['data']))
        {
            // 用户海报页面顶部描述
            if(!empty($ret['data']['user_poster_top_desc']))
            {
                $ret['data']['user_poster_top_desc'] = explode("\n", $ret['data']['user_poster_top_desc']);
            }

            // 等级介绍顶部描述
            if(!empty($ret['data']['user_center_level_desc']))
            {
                $ret['data']['user_center_level_desc'] = explode("\n", $ret['data']['user_center_level_desc']);
            }

            // 自提取货点申请介绍
            if(!empty($ret['data']['self_extraction_apply_desc']))
            {
                $ret['data']['self_extraction_apply_desc'] = explode("\n", $ret['data']['self_extraction_apply_desc']);
            }

            // 自提取货点顶部公告
            if(!empty($ret['data']['self_extraction_common_notice']))
            {
                $ret['data']['self_extraction_common_notice'] = explode("\n", $ret['data']['self_extraction_common_notice']);
            }

            // 不符合分销条件描述
            if(!empty($ret['data']['non_conformity_desc']))
            {
                $ret['data']['non_conformity_desc'] = explode("\n", $ret['data']['non_conformity_desc']);
            }

            // 分销中心公告
            if(!empty($ret['data']['user_center_notice']))
            {
                $ret['data']['user_center_notice'] = explode("\n", $ret['data']['user_center_notice']);
            }
        }
        return $ret;
    }
}
?>
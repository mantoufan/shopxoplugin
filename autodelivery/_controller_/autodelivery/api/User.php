<?php
namespace app\plugins\autodelivery\api;

class User
{
    public function __construct()
    {
        // 调用父类前置方法
        parent::__construct();

        // 是否登录
        $this->IsLogin();
    }

    public function Index()
    {
        // 应用配置
        $base = BaseService::BaseConfig();

        // 分销信息
        $user_level = BaseService::UserautodeliveryLevel($this->user['id']);

        // 取货点信息
        if(isset($base['data']) && isset($base['data']['is_enable_self_extraction']) && $base['data']['is_enable_self_extraction'] == 1)
        {
            $extraction = ExtractionService::ExtractionData($this->user['id']);
        }

        // 返回数据
        $result = [
            'base'          => empty($base['data']) ? null : $base['data'],
            'user_level'    => empty($user_level['data']) ? null : $user_level['data'],
            'extraction'    => (isset($extraction) && !empty($extraction['data'] ))? $extraction['data'] : null
        ];
        return DataReturn('success', 0, $result);
    }
}
?>
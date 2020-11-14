if (location.href.indexOf('index/order/respond/') > -1 || location.href.indexOf('pluginscontrol/auth/pluginsaction/wxpub') > -1) {
    var plugin_notice_s = 'font-size: 16px; color: #333;', plugin_notice_c = 'font-size: 12px; color: #666', plugin_notice_tip = $('<div>').append('<p style="margin-top: 16px;' + plugin_notice_s + '">请' + (
        plugins_notice_data['is_weixin'] ? '长按识别' : (plugins_notice_data['is_mobile'] ? '截屏' : '') + '用微信扫描'
    ) + '二维码</p><p style="' + plugin_notice_c + '">Please long press to identify the QR code</p><p style="' + plugin_notice_s + '">关注公众号，接收发货通知</p><p style="' + plugin_notice_c + '">Subscribe to official account, receive shipping notice</p><img src="' + (
        plugins_notice_data[plugins_notice_data['weixin_web_openid'] ? 'wxpub_qrcode' : 'wxpub_qrcode_auth'] 
    )  + '" alt="微信二维码" width="90%" style="max-width:360px">');
    if ($('#plugin-notice-tip').length) {
        $('#plugin-notice-tip').append(plugin_notice_tip)
    } else {
        plugin_notice_tip.insertAfter($('.my-content .msg'))
    }
}
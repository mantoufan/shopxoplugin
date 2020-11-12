if (pluginOptimizerConf) {
    if (pluginOptimizerConf.availableSeoBaiduPush) {
        $.getScript(__attachment_host__ + '/static/plugins/js/optimizer/index/baidu_push.js?v=' + pluginOptimizerConf.cacheVersion)
    }
    var pluginOptimizerRes = []
    $([{t: 'img', a: 'src', s: '/static/upload/images/'}]).each(function(_, c) {
        $(c.t).each(function(_, o) {
            var s = $(o).attr(c.a)
                if (s && s.indexOf(c.s) > -1) {
                    pluginOptimizerRes.push(s.replace(/http[s]*:\/\/.*?\//g,'').split('?')[0])
                }
        })
    })
    if (pluginOptimizerRes.length) {
        $.post('?s=index/plugins/index/pluginsname/optimizer/pluginscontrol/mtf/pluginsaction/better', {
            paths: pluginOptimizerRes.join(',')
        })
    }
}
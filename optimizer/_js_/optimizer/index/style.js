if (pluginOptimizerConf) {
    if (pluginOptimizerConf.availableSeoBaiduPush) {
        $.getScript(__attachment_host__ + '/static/plugins/js/optimizer/index/baidu_push.js?v=' + pluginOptimizerConf.cacheVersion)
    }
    var pluginOptimizerRes = []
    $('img').each(function(_, o){
        var s = $(o).attr('src')
        if (s.indexOf('/static/upload/images/') > -1) {
            pluginOptimizerRes.push(s.replace(/http[s]:\/\/.*?\//g,''))
        }
    })
    $('script').each(function(_, o){
        var s = $(o).attr('src')
        if (s.indexOf('/static/common/lib/') > -1) {
            pluginOptimizerRes.push(s.replace(/http[s]:\/\/.*?\//g,''))
        }
    })
    $('link').each(function(_, o){
        var s = $(o).attr('href')
        if (s.indexOf('/static/common/lib/') > -1) {
            pluginOptimizerRes.push(s.replace(/http[s]:\/\/.*?\//g,''))
        }
    })
}
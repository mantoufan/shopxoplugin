
var serviceproBox = $('#js_servicepro_box');
if (serviceproBox.length > 0)
{
    var serviceproClipboard = new ClipboardJS('.servicepro-btn-a'), serviceproTimeout = 2500, serviceproStr = function(bt) {return '进入'+ serviceproGetNoName(bt) +'添加好友';}
    serviceproClipboard.on('success', function(e) {
        var bt = $(e.trigger);
        serviceproTipOnbtn (bt, '复制' + serviceproGetNoName(bt) + '号成功，请' + serviceproStr(bt), serviceproTimeout)
    })
    serviceproClipboard.on('error', function(e) {
        var bt = $(e.trigger);
        serviceproTipOnbtn (bt, '复制失败，请手动复制' + serviceproGetNoName(bt) + '号，' + serviceproStr(bt), serviceproTimeout)
    })
    function serviceproTipOnbtn (bt, s, t) {
        bt.popover({
            content: s
        }).popover('open')
        setTimeout(function() {
            bt.popover('close')
        }, t)
    }
    function serviceproGetNoName (bt) {
        return bt.hasClass('servicepro-btn-a-weixin') ? '微信' : '旺旺'
    }
}
var serviceproAr = [
    {k: 'float', j: '/static/plugins/js/servicepro/index/float.js'},
    {k: 'fix', j: '/static/plugins/js/servicepro/index/fix.js'},
    {k: 'chat_daovoice', j: '//widget.daovoice.io/widget/e9333ef8.js', cb: function() {
        daovoice('init', {
            app_id: serviceproData['id_chat_daovoice'],
            user_id: serviceproConf.user.id,
            email: serviceproConf.user.email,
            name: serviceproConf.user.nickname || serviceproConf.user.username,
            signed_up: serviceproConf.user.addtime
          });
          daovoice('update');
    }},
    {k: 'chat_crisp', j: 'https://client.crisp.chat/l.js', bb: function () {
        window.$crisp = [];
        window.CRISP_WEBSITE_ID = serviceproData['id_chat_crisp'];
        $crisp.push(['set', 'user:email', serviceproConf.user.email]);
        $crisp.push(['set', 'user:phone', serviceproConf.user.mobile]);
        $crisp.push(['set', 'user:nickname', serviceproConf.user.nickname || serviceproConf.user.username]);
        $crisp.push(['set', 'user:avatar', serviceproConf.user.avatar]);
        $crisp.push(['do', 'message:send', ['text', document.title + ':' + window.location.href]]);
    }},
    {k: 'fix', j: '/static/plugins/js/servicepro/index/fix.js'},
];
for (var i = 0; i < serviceproAr.length; i++) {
    var item = serviceproAr[i], k = item.k, j = item.j, bb = item.bb || new Function(), cb = item.cb || new Function(),
        d = {
            a: serviceproData['available_' + k],
            d: serviceproData['display_' + k],
            r: serviceproData['right_' + k],
            s: serviceproData['scope_' + k],
            _d: true,
            _r: true,
            _s: true
        }
    if (d.a) {
        if (serviceproConf.isMobile) {
            if (d.d && d.d.indexOf('mobile') === -1) {
                d._d = false;
            }
        } else {
            if (d.d && d.d.indexOf('pc') > -1) {
                d._d = false;
            }
        }
        if (d.r === '1') {
            if (!serviceproConf.user.id) {
                d._r = false;
            }
        }
        if (d.s === '0') {
            if (!serviceproConf.isHome) {
                d._s = false;
            }
        }
        if (d._d && d._r && d._s) {
            bb && bb();
            $.getScript(j + '?v='+cacheVersion, cb);
        }
    }
}
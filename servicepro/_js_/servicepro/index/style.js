
var serviceproBox = $('#js_servicepro_box');
if (serviceproBox.length > 0)
{
    var serviceproClipboard = new ClipboardJS('.servicepro-btn-a'), serviceproTimeout = 2500, 
        serviceproClip = function(e, type) {
            var bt = $(e.trigger), n = serviceproGetNoName(bt);
            serviceproTipOnbtn (bt, '复制' + n + (n === '号码' ? '' : '号') + (type === 'success' ? '成功' : '失败，请手动复制') + (n === '号码' ? '' : '，进入'+ serviceproGetNoName(bt) +'添加好友'), serviceproTimeout)
        }
    serviceproClipboard.on('success', function(e) {
        serviceproClip(e, 'success');
    }).on('error', function(e) {
        serviceproClip(e, 'error');
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
        return bt.hasClass('servicepro-btn-a-weixin') ? '微信' : (bt.hasClass('servicepro-btn-a-wangwang') ? '旺旺' : '号码');
    }
}
var serviceproAr = [
    {k: 'float', j: __attachment_host__ + '/static/plugins/js/servicepro/index/float.js', cb: function() {
        var click_fix = serviceproData['click_fix'] || '0';
        if (click_fix !== '0') {
            $('.servicepro-btn, .nav-icon-comments').attr('data-am-modal','').clcik(function(e){
                e.preventDefault();
                switch(click_fix) {
                    case '1':
                        daovoice && daovoice('openMessages');
                    break;
                    case '2':
                        $crisp && $crisp.push(["do", "chat:open"]);
                    break;
                    case '3':
                        $('.main-contact').trigger('click');
                    break;
                }
            })
        }
    }},
    {k: 'fix', j: __attachment_host__ + '/static/plugins/js/servicepro/index/fix.js'},
    {k: 'chat_daovoice', j: '//widget.daovoice.io/widget/e9333ef8.js', bb: function() {
        var e = window, n = 'daovoice';
        e.DaoVoiceObject = '';
        e[n] = e[n] || function() {
            (e[n].q = e[n].q || []).push(arguments);
        }, e[n].l = 1 * new Date();
    }, cb: function() {
        daovoice('init', {
            app_id: serviceproData['id_chat_daovoice'],
            user_id: serviceproConf.user.id,
            email: serviceproConf.user.email,
            name: serviceproConf.user.nickname || serviceproConf.user.username,
            signed_up: serviceproConf.user.add_time
          });
          daovoice('update');
    }},
    {k: 'chat_crisp', j: 'https://client.crisp.chat/l.js', bb: function() {
        window.$crisp = [];
        window.CRISP_WEBSITE_ID = serviceproData['id_chat_crisp'];
        $crisp.push(['set', 'user:email', serviceproConf.user.email]);
        $crisp.push(['set', 'user:phone', serviceproConf.user.mobile]);
        $crisp.push(['set', 'user:nickname', serviceproConf.user.nickname || serviceproConf.user.username]);
        $crisp.push(['set', 'user:avatar', serviceproConf.user.avatar]);
        $crisp.push(['do', 'message:send', ['text', document.title + ':' + window.location.href]]);
        $crisp.push(["safe", true]);
    }},
    {k: 'chat_qqyzf', j: 'https://yzf.qq.com/xv/web/static/chat_sdk/yzf_chat.min.js', cb: function() {
        window.yzf && window.yzf.init({
            sign: serviceproData['id_chat_qqyzf'],
            uid: serviceproConf.user.id,
            data: {
              c1: serviceproConf.user.nickname || serviceproConf.user.username,
              c2: serviceproConf.user.email,
              c3: serviceproConf.user.mobile,
              c4: document.title,
              c5: window.location.href
            }
        })
    }},
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
            if (d.d && d.d.indexOf('pc') === -1) {
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
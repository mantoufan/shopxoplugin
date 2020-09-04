
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
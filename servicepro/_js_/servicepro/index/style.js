
var serviceproSider = $('#js_servicepro_sider'), serviceproModal = $('#js_servicepro_modal'), serviceproBox = $('#js_servicepro_box');
if (serviceproSider.length > 0)
{
    serviceproSider.find('.cn').html(serviceproBox.html());
    // 在线客服
    $('.servicepro .btn-open').click(function()
    {
        $('.servicepro .content').animate({'margin-right':'0px'}, 300);
        $('.servicepro .btn-open').css('display', 'none');
        $('.servicepro .btn-ctn').css('display', 'block');        
    })

    $('.servicepro .btn-ctn').click(function()
    {
        $('.servicepro .content').animate({'margin-right':'-150px'}, 300);
        $('.servicepro .btn-open').css('display', 'block');
        $('.servicepro .btn-ctn').css('display', 'none');  
    })
}
if (serviceproModal.length > 0)
{
    serviceproModal.find('.am-modal-bd').html(serviceproBox.find('ul').html());
}
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
if (daovoice_is_takeover) {
    $('.servicepro-btn, .nav-icon-comments').attr('data-am-modal','').click(function(){
        event.preventDefault();
        daovoice('openMessages');
    }) 
}
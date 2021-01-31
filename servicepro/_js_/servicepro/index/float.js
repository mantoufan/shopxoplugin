var serviceproSider = $('#js_servicepro_sider');
if (serviceproSider.length > 0) {
    serviceproSider.find('.cn').html(serviceproBox.html());
    // 在线客服
    $('.servicepro .btn-open').click(function()
    {
        $('.servicepro').removeClass('servicepro-close').addClass('servicepro-open');
        $('.servicepro .content').animate({'margin-right':'0px'}, 300);
        $('.servicepro .btn-open').css('display', 'none');
        $('.servicepro .btn-ctn').css('display', 'block');        
    })

    $('.servicepro .btn-ctn').click(function()
    {
        $('.servicepro').removeClass('servicepro-open').addClass('servicepro-close');
        $('.servicepro .content').animate({'margin-right':'-150px'}, 300);
        $('.servicepro .btn-open').css('display', 'block');
        $('.servicepro .btn-ctn').css('display', 'none');  
    })
    $('.servicepro').fadeIn();
}
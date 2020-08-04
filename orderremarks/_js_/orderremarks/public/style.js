$(function()
{
    // 表单初始化
    FromInit('form.form-validation-plugins-orderremarks-popup');

    // 订单改价事件
    $('.plugins-orderremarks-order-submit').on('click', function()
    {
        // 参数
        var $this = $(this);
        var id = $this.data('id') || 0;
        var url = $this.data('url') || null;
        if(id == 0 || url == null)
        {
            Prompt('参数有误');
            return false;
        }

        // 开启进度条
        $.AMUI.progress.start();
        $this.addClass('am-disabled');

        // 获取订单数据
        $.ajax({
            url:url,
            type:'POST',
            dataType:"json",
            timeout:30000,
            data:{"id":id},
            success:function(result)
            {
                $.AMUI.progress.done();
                $this.removeClass('am-disabled');
                if(result.code == 0)
                {
                    var $popup = $('#plugins-orderremarks-order-popup');

                    // 基础数据
                    $popup.find('.base .data-order-no').text(result.data.order_no);
                    $popup.find('.base .data-order-admin-note-upd-time').text(result.data.admin_note_upd_time && result.data.admin_note_upd_time || '无');
                    $popup.find('form.am-form input[name="id"]').val(result.data.id);

                    // 初始化修改数据
                    $popup.find('form.am-form textarea[name="admin_note"]').val(result.data.admin_note && result.data.admin_note || '');

                    $popup.modal();
                } else {
                    Prompt(result.msg);
                }
            },
            error:function(xhr, type)
            {
                $.AMUI.progress.done();
                $this.removeClass('am-disabled');
                Prompt('网络异常出错');
            }
        });
    });

    var isNeedTableContainerInit = false;
    //订单列表显示备注
    $('.plugins-orderremarks-order-submit').each(function(index, ele) {
        var e = $(ele), t = e.attr('title'), tr = e.parentsUntil('tbody'), d=$('<div class="plugins-orderremarks-order-list-note">');
        tr = tr.last();
        if (t) {
            isNeedTableContainerInit = true;
            d.html(t);
            tr.find('td:first .goods-item:first').append(d);
        }
    })

    //初始化表格
    isNeedTableContainerInit && TableContainerInit()
});
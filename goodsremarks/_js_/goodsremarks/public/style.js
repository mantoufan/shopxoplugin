$(function() {
  // 表单初始化
  FromInit('form.form-validation-plugins-goodsremarks-popup');
  // 订单备注事件
  $('.plugins-goodsremarks-submit').on('click', function() {
    // 参数
    var $this = $(this);
    var id = $this.data('id') || 0;
    var url = $this.data('url') || null;
    if(id == 0 || url == null) {
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
      success:function(result) {
        $.AMUI.progress.done();
        $this.removeClass('am-disabled');
        if(result.code == 0) {
          var $popup = $('#plugins-goodsremarks-popup');

          // 基础数据
          $popup.find('.base .data-goods-id').text(result.data.goods_id);
          $popup.find('.base .data-goods-admin-note-upd-time').text(result.data.admin_note_upd_time && result.data.admin_note_upd_time || '无');
          $popup.find('form.am-form input[name="id"]').val(result.data.goods_id);

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
  //商品管理列表显示备注
  $('.plugins-goodsremarks-submit').each(function(_, ele) {
    var e = $(ele), t = e.attr('title'), tr = e.parentsUntil('tbody'), d=$('<div class="plugins-goodsremarks-list-note">');
    tr = tr.last();
    if (t) {
      d.html(t);
      d.insertAfter(tr.find('.am-nbfc'));
    }
  })
});
var plugins_thirdpartylogin_form_login = $('.user-form-container .am-form .am-form-group:last, .login-modal-container .am-form .am-form-group:last'),
    plugins_thirdpartylogin_form_reg = $('.register-container .am-form');
if (plugins_thirdpartylogin_form_login.length > 0) { // 登录
    if (plugins_thirdpartylogin_bind() === false) {
        plugins_thirdpartylogin_config = JSON.parse($('#plugins_thirdpartylogin_config_str').val()),
        plugins_thirdpartylogin_attachment_dir = $('#plugins_thirdpartylogin_attachment_dir').val(),
        plugins_thirdpartylogin_url = $('#plugins_thirdpartylogin_url').val(),
        plugins_thirdpartylogin_icons = $('<span class="plugins_thirdpartylogin_icons">');
        for (var type in plugins_thirdpartylogin_config) {
            var  config = plugins_thirdpartylogin_config[type],
                plugins_thirdpartylogin_attachment_src =  plugins_thirdpartylogin_attachment_dir + 'icon/' + type + '.png',
                a = $('<a>'),
                img = $('<img>');
                img.attr({
                        'src': plugins_thirdpartylogin_attachment_src,
                        'alt': type + ' icon',
                        'title': '使用' + config['n'] + '登录'
                });
                a.append(img).addClass('am-fr').attr('href', plugins_thirdpartylogin_url.replace('/party/party', '/party/' + type));
                a.on('click', function() {
                    event.preventDefault();
                    top.location.href = $(this).attr('href');
                });
                plugins_thirdpartylogin_icons.append(a);
        }
        plugins_thirdpartylogin_form_login.append(plugins_thirdpartylogin_icons);
    }
} else if (plugins_thirdpartylogin_form_reg.length > 0) { // 注册
    plugins_thirdpartylogin_bind();
}
function plugins_thirdpartylogin_bind() {
    var h = window.location.hash,
        h = h.substr(1),
        a = h.split('&'),
        a = a[0].split('plugin_thirdpartylogin_b='),
        res = false;

        if (a.length > 1) {
            var plugins_thirdpartylogin_b = $('<input type="hidden" name="plugins_thirdpartylogin_b">');
            plugins_thirdpartylogin_b.val(a[1]);
            if (plugins_thirdpartylogin_form_login.length > 0) { // 登录
                plugins_thirdpartylogin_form_login.append(plugins_thirdpartylogin_b);
                var sub = plugins_thirdpartylogin_form_login.find('[type="submit"]');
                sub.html(sub.html() + '并绑定');
                res = true;
            } else if (plugins_thirdpartylogin_form_reg.length > 0) { // 注册
                plugins_thirdpartylogin_form_reg.append(plugins_thirdpartylogin_b);
                var sub = plugins_thirdpartylogin_form_reg.find('[type="submit"]');
                sub.html(sub.html() + '并绑定');
                res = true;
            }
        } 
        return res;
}
var js_plugins_goodslocation_bt = $('#js_plugins_goodslocation_bt'), js_plugins_goodslocation_timer = null;
if (js_plugins_goodslocation_bt.length > 0) {
    var js_plugins_goodslocation_map = null
    js_plugins_goodslocation_bt.on('click', function()
    {
        var ak = $("#js_plugins_goodslocation_ak").val();
        if (typeof BMap === 'undefined') {
            var pop = $('#js_plugins_goodslocation_popup'), h = 0;
            $.getScript('https://api.map.baidu.com/api?v=2.0&callback=js_plugins_goodslocation_init&ak='+ak);
            setTimeout(function(){$('#js_plugins_goodslocation_map').height(pop.height() - pop.find('.am-popup-hd').height() - pop.find('.plugins_goodslocation_menu').height());}, 90);
        }
    });
    function js_plugins_goodslocation_init () {
        var lon = $('input[name="plugins_goodslocation_lon"]'),
            lat = $('input[name="plugins_goodslocation_lat"]'),
            locate = $('input[name="plugins_goodslocation_location"]'),
            idAdmin = $('input[name="js_plugins_goodslocation_is_admin"]').val(),
            x = lon.val() || 116.32715863448607,
            y = lat.val() || 39.990912172420714,
            Point = new BMap.Point(x,y);

        js_plugins_goodslocation_map = new BMap.Map('js_plugins_goodslocation_map');
        js_plugins_goodslocation_map.centerAndZoom(Point, 15);
        js_plugins_goodslocation_map.addControl(new BMap.NavigationControl());

        var local = new BMap.LocalSearch(js_plugins_goodslocation_map, {
            renderOptions: {map: js_plugins_goodslocation_map, panel: "js_plugins_goodslocation_result"}
        });

        if (idAdmin) {
            js_plugins_goodslocation_map.addEventListener("click", function(e) {
                setTimeout(function(){
                    var info = $('.BMap_pop :visible .BMap_bubble_content'), address = info.find('tbody tr:first td+td').text() || info.find('p:first').text(), tipItems = ['坐标'];
                    lat.val(e.point.lat);
                    lon.val(e.point.lng);
                    if (address.indexOf('：') > -1) {
                        address = address.split('：')[1];
                    }
                    if (address) {
                        locate.val(address);
                        tipItems.push('地址');
                    }
                    
                    Prompt(tipItems.join('和') + '已更改', 'success');
                }, 90);
            });
            var input = $('#js_plugins_goodslocation_input');
            if (input.length > 0) {
                input.on('input', function() {
                    clearTimeout(js_plugins_goodslocation_timer);
                    js_plugins_goodslocation_timer = setTimeout(function() {
                        local.search(input.val());
                    }, 500);
                })
            }
        }
        local.search(locate.val());
    }
}
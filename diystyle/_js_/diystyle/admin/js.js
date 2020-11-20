var hsl = $('input[name="hsl"]').val().split(',')
if (hsl.length === 3) {
    $('input[name="hue"]').on('input', function() {
        $('#js_color').css('background-color', 'hsl(' + ((hsl[0] | 0) + ($(this).val() | 0)) + ',' + hsl[1]  + ',' + hsl[2] + ')')
    }).trigger('input');
}
function tpl() {
    if ($('select[name=tpl_id]').val() === 'default') {
        $('#js_color').fadeIn();
    } else {
        $('#js_color').fadeOut();
    }
}
$('select[name=tpl_id]').on('change', tpl);
tpl();
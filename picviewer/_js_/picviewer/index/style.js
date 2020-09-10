var pluginPicviewer = (function() {
    function getImgByIndex(index) {
        return $('.mtf-pic-list').children().eq(index).children('img');
    }
    function setBgWithDominantColor(curIndex, bgColor) {
        var img = getImgByIndex(curIndex)[0];
        $('.mtf-pic-viewer').css('backgroundColor', bgColor || '#000');
        if (img.src.indexOf('file://') === -1) {
            RGBaster.colors(img, {
            paletteSize: 1,
            success: function(payload) {
                $('.mtf-pic-viewer').css('backgroundColor', payload.dominant);
            }
            });
        }
    }
    function scrollAuto(curIndex) {
        var $img = getImgByIndex(curIndex);
        $('html,body').animate({'scrollTop': $('img[src="' + $img.attr('src') + '"]').offset().top});
    }
    return {
        scrollAuto: scrollAuto,
        setBgWithDominantColor: setBgWithDominantColor
    }
})();


/**
 * Created by Роман on 22.12.2015.
 */
$(function () {
    "use strict";

    var $gf = $('section.gamefield');

    $('img').mapster({
        mapKey: 'state',
        staticState: true,
        fill: false,
        stroke: true,
        strokeWidth: 2,
        strokeColor: 'ff0000'
    }).mapster('set', true, 's');

    /*$gf.find('.panzoom').panzoom({
        $zoomIn: $gf.find('.zoom-in'),
        $zoomOut: $gf.find('.zoom-out'),
        $zoomRange: $gf.find('.zoom-range'),
        $reset: $gf.find('.reset'),
        startTransform: 'scale(1.1)',
        increment: 0.1,
        maxScale: 1.3,
        minScale: 0.6,
        contain: 'invert'
    }).panzoom('zoom', true);
    */
});
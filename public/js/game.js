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
});
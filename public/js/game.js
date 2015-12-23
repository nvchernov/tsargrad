/**
 * Created by Роман on 22.12.2015.
 */
$(function () {
    "use strict";

    $('#gamefield').mapster({
        mapKey: 'user-id',
        staticState: true,
        fill: false,
        stroke: true,
        strokeWidth: 2,
        strokeColor: 'ff0000'
    }).mapster('set', true, ''+User.id);
});
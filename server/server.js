var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

server.listen(8080, function () {
    console.log('Tiny server is running...');
});

io.on('connection', function (socket) {
    console.log('New client connected...');

    var client = redis.createClient();
    client.subscribe('message');

    client.on('message', function(channel, message) {
        var msg = JSON.parse(message);
        console.log('New message in queue ' + channel + '/' + msg.event);
        console.log('Data to send: ' + message);
        socket.emit(channel + '/' + msg.event, msg.data);
    });

    socket.on('disconnect', function() {
        client.quit();
        console.log('...client disconnected.');
    });
});

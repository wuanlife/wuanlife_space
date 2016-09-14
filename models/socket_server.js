var io = require('socket.io')();
var router = require('../routes/news').post;

io.on('connection',function(socket){
    console.log('one connected');
    socket.on('disconnect',function(){
        console.log('one leave');
    });
});
router(function(req,res){
    var userid = req.body.userid;
    console.log(userid);
    var clients = io.sockets.clients();
    console.log(clients);
});
exports.listen = function(server){
    return io.listen(server);
}
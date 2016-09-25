var io = require('socket.io')();
var sessionMiddleware = require('../app').sessionMiddleware;
var router = require('../routes/news').post;

io.use(function(socket,next){
    sessionMiddleware(socket.request,socket.request.res,next);
});
io.on('connection',function(socket){
    //console.log('one connected');
    if (socket.request.session.user) {
        var roomid = 'wuan' + socket.request.session.user.userID;
        socket.join(roomid);
        //console.log('user joined room:'+roomid);
    }
    socket.on('disconnect',function(){
        if (socket.request.session.user) {
            var roomid = 'wuan' + socket.request.session.user.userID;
            socket.leave(roomid);
            //console.log('user leave room:'+roomid);
        }
        //console.log('one leave');
    });
});
router(function(req,res){
    res.header('Content-type', 'application/json');
    res.header('Charset', 'utf8');
    var userid = req.body.userid;
    var reg = /^\d+$/;
    if (!reg.test(userid)) {
        return res.send(JSON.stringify({ret:400,data:{},msg:'请输入正确的用户id'}));
    }
    var roomid = 'wuan' + userid;
    if (io.sockets.adapter.rooms[roomid]) {
        //console.log(io.sockets.adapter.rooms[roomid].length);
        io.sockets.in(roomid).emit('news');
        res.send(JSON.stringify({ret:200,data:{code:1,msg:''},msg:''}));
    } else{
        res.send(JSON.stringify({ret:200,data:{code:0,msg:'该用户不在线'},msg:''}));
    }
});
exports.listen = function(server){
    return io.listen(server);
}
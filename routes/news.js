var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var page = 'news';
    res.render(page, {'path':'','title':'登录','user':req.session.user});
});
// router.post('/', function(req, res, next) {
//     var userid = req.body.userid;
//     console.log(userid);
//     // io.sockets.clients().forEach(function(socket){
//     //     if (socket.request.session.user.userID == userid) {
//     //         socket.emit('news',{data:'haha'});
//     //     }
//     // });
// });

exports.post = function(cb){
    router.post('/',function(req,res,next){
        cb(req,res);
    })
}
exports.router = router;
// module.exports = router;
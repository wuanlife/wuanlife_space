var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');

/* GET users listing. */
// router.get('/', function(req, res, next) {
//     var agent = ua(req.headers['user-agent']);
//     var page = 'news';
//     res.render(page, {'path':'','title':'登录','user':req.session.user});
// });

exports.post = function(cb){
    router.post('/',function(req,res,next){
        cb(req,res);
    })
}
exports.router = router;
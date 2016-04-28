var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');

/* GET home page. */
router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    request(config.server + '?service=Post.GetIndexPost', function(error, response, body) {
        if (!error && response.statusCode == 200) {
            console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
            var data = JSON.parse(body);
            console.log(data.data.posts[0])
            if (data.ret == 200) {
                res.render('test', {
                    'path':'',
                    'result': data.data,
                    'ag': agent,
                    'title':'测试页'
                });
            } else {
                res.render('error', {
                    'message': data.msg,
                    error: {
                        'status': data.ret,
                        'stack': ''
                    }
                });
            }
        } else {
            //          res.status(err.status || 500);
            //          res.render('error', {
            //              message: response.statusCode + '/n服务器忙，请稍后再试！',
            //              error: {}
            //          });
            next(error);
        }
    })

});

module.exports = router;
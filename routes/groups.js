var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

router.post('/:groupid/post', function(req, res, next) {
    var userid = (req.session.user) ? req.session.user.userID : null;
    console.log("can post be used?");
    var html = xss(req.body.text, {
        onTag: function(tag, html, options) {
            if (tag == "strike") {
                return html;
            }
        },
        onTagAttr: function(tag, name, value, isWhiteAttr) {
            if (name == 'style') {
                return name + '="' + value + '"';
            }
        }
    });
    request.post({
        url: config.server + 'group/posts',
        formData: {
            group_id: 1,
            user_id: 2,
            p_title: 1,
            p_text: 1
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        try{
            if (!err && httpResponse.statusCode == 200) {
                var result = JSON.parse(body);
                //console.log(result);
                return res.send(result);
            }
            res.send({
                ret:500,
                msg:'服务器异常'
            });
        } catch(e){
            res.send({
                ret:500,
                msg:'服务器异常'
            });
        }
    });

});
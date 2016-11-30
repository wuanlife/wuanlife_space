var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

/* GET users listing. */
router.get('/:id', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var userid = (req.session.user) ? req.session.user.userID : null;
    request(config.server + "?service=Post.GetPostBase&post_id=" + req.param('id') + "&id=" + userid,
        function(err, response, body) {
            if (!err && response.statusCode == 200) {
                try{
                    var result = JSON.parse(body);
                    if (result.ret ==200) {
                        if (result.data.code == 1) {
                            var page = agent.Mobile ? 'topicMobile' : 'topic';
                            res.render(page, {
                                result: result.data,
                                'title': result.data.title,
                                'user': req.session.user
                            });
                        } else if (result.data.code == 2) {
                            res.redirect('/group/' + result.data.groupID);
                        } else{
                            throw new Error('no page');
                        }
                    }
                } catch(e){
                    next(e);
                }
            } else {
                next(err);
            }
        });
});

//移动端楼中楼回复页面
router.get('/:id/comment', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    if (req.session.user) {
        try {
            if (agent.Mobile) {
                res.render('topicCommentMobile', {
                    'title': '回复评论',
                    'user': req.session.user,
                    'postID': req.params.id,
                    'reply': req.query.reply
                });
            } else {
                throw 'no page';
            }
        } catch (e) {
            next(e);
        }
    } else{
        res.redirect('/login');
    }
});

router.get('/:id/edit', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var userid = (req.session.user) ? req.session.user.userID : null;
    if (agent.Mobile) {
        res.render('error', {
            'message': result.msg,
            error: {
                'status': 404,
                'stack': ''
            }
        });
    } else {
        request(config.server + "?service=Post.GetPostBase&post_id=" + req.param('id') + "&id=" + userid,
            function(err, response, body) {
                if (!err && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.msg == "" && result.data.editRight == 1) {
                        res.render('postEdit', {
                            'path': '../../',
                            'topicID': req.param('id'),
                            'title': '编辑帖子',
                            'user': req.session.user
                        });
                    } else {
                        res.render('error', {
                            'message': result.msg,
                            error: {
                                'status': 404,
                                'stack': ''
                            }
                        });
                    }
                } else {
                    next(err);
                }
            });
    }
});

router.post('/:id/edit', function(req, res, next) {
    var userid = (req.session.user) ? req.session.user.userID : null;
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
        url: config.server + '?service=Post.editPost',
        formData: {
            user_id: userid,
            post_id: req.params.id,
            title: xss(req.body.title),
            text: html
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        try {
            if (!err && httpResponse.statusCode == 200) {
                var result = JSON.parse(body);
                //console.log(result);
                return res.send(result);
            }
            res.send({
                ret: 500,
                msg: '服务器异常'
            });
        } catch (e) {
            res.send({
                ret: 500,
                msg: '服务器异常'
            })
        }
    });

});
module.exports = router;

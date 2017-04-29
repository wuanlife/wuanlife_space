var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

/* 获取星球主页 */
router.get('/:groupid', function(req, res, next) {
    var agent = ua(req.headers['user-agent']),
        pn = req.query.page || 1;
    var userid = (req.session.user) ? req.session.user.user_id : null;
    console.log(config.server + "post/get_group_post?group_id=" + req.params.groupid + "&user_id=" + userid + "&pn=1");
    request(config.server + "post/get_group_post?group_id=" + req.params.groupid + "&user_id=" + userid + "&pn=1",
        function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    console.log(result);
                    if (result.ret == 200) {
                        var page = agent.Mobile ? 'groupM' : 'group';
                        res.render(page, {
                            result: result.data,
                            'title': result.data.g_name,
                            'user': req.session.user
                        });
                    } else {
                        res.render('error', {
                            'message': result.msg,
                            error: {
                                'status': result.ret,
                                'stack': ''
                            }
                        });
                    }
                } else {
                    console.error('group failed:', error);
                    next(error);
                }
        })
});

//获取更多帖子
router.get('/:groupid/more',function(req, res, next){
    var pn=req.query.pagecount;
    var userid = (req.session.user) ? req.session.user.user_id : null;
    request(config.server + 'post/get_group_post?group_id=' + req.params.groupid + "&user_id=" + userid + "&pn="+pn,function(error, response, body){
        if(!error){
            var result = JSON.parse(body);
            if(result.ret==200){
                res.send(result);
            }else{
                res.render('error', {
                    'message': result.msg,
                    error: {
                        'status': result.ret,
                        'stack': ''
                    }
                });
            }
        }else{
            console.error('allGroup failed:', error);
            next(error);
        }
    })
});
//星球发帖
router.get('/:groupid/post', function(req, res, next) {
        console.log("uehfu");
        var agent = ua(req.headers['user-agent']);
        //var userid = req.session.user.userID;
        var page = agent.Mobile ? 'publishPostM' : 'publishPostM';
        var tip = agent.Mobile ? 'publishPostM' : 'publishPostM';
        //http://dev.wuanlife.com:800/group/posts?group_id=1&user_id=2&p_title=1&p_text=1
        request(config.server+"group/posts?group_id=1&user_id=2&p_title=1&p_text=1",
            function(error, response, body) {
                res.render(page, {
                                'groupID': 'req.params.groupid',
                                'title': '发表帖子',
                                'user': req.session.user
                            });
                /*console.log("jg");
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    console.log(result);
                    if (result.ret == 200) {
                        
                            res.render(page, {
                                'groupID': req.params.groupid,
                                'title': '发表帖子',
                                'user': req.session.user
                            });
                        
                    } else {
                        res.render(page, {
                                'groupID': req.params.groupid,
                                'title': '发表帖子',
                                'user': req.session.user
                            });
                    }
                } else {
                    console.error('group failed:', error);
                    next(error);
                }*/
            });

});
//发表帖子
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

//设置星球,进入页面路由
router.get('/:groupid/set', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var userid = (req.session.user) ? req.session.user.user_id : null;
    var page = agent.Mobile ? 'setGroupM' : 'setGroupM';
    var groupid = req.params.groupid;
    if(req.session.user){
    request(config.server +"group/get_group_info?group_id=" + groupid + "&user_id=" + userid,
        function(error, response, body) {
            if (!error && response.statusCode == 200) {
                var result = JSON.parse(body);
                if (result.ret == 200) {
                    res.render(page, {
                            'result': result.data,
                            'title': '星球设置',
                            'user': req.session.user
                        });
                } else {
                    res.render('error', {
                        'message': result.msg,
                        error: {
                            'status': result.ret,
                            'stack': ''
                        }
                    });
                }
            } else {
                console.error('group failed:', error);
                next(error);
            }
        });
    }else{
        res.redirect('/personal/login');
    }
});
//修改信息的提交
router.get('/:groupid/sub',function(req,res,body){
    if(req.session.user){
    var introduction=encodeURIComponent(req.query.introduction),
        image=req.query.image,
        private=req.query.private,
        userid=req.session.user.user_id,
        groupid=req.params.groupid;
    console.log(introduction+'  '+image+'   '+private);
    request(config.server+"group/alter_group_info?group_id="+groupid+"&user_id="+userid+"&g_introduction="+introduction+"&g_image="+image+"&private="+private,
        function(error, response, body) {
            if (!error && response.statusCode == 200) {
                var result = JSON.parse(body);
                if (result.ret == 200) {
                    res.send(result);
                } else {
                    res.render('error', {
                        'message': result.msg,
                        error: {
                            'status': result.ret,
                            'stack': ''
                        }
                    });
                }
            } else {
                console.error('group failed:', error);
                next(error);
            }
        });
    }else{
        res.redirect('/personal/login');
    }
})


//退出星球
// router.post('/:groupid/quit', function(req, res, next) {
//  var userid = (req.session.user) ? req.session.user.userID : null;
//  request.post({
//      url:config.server + '?service=Group.Quit',
//      formData:{
//          user_id:userid,
//          group_base_id:req.params.groupid
//      }
//  },function optionalCallback(err, httpResponse, body){
//      res.header('Content-type', 'application/json');
//         res.header('Charset', 'utf8');
//         try {
//                     if (!err && httpResponse.statusCode == 200){
//                         //console.log(JSON.parse(body));
//                         return res.send(JSON.parse(body));
//                     }
//                     //console.error('processApply failed:', err);
//                     res.send({
//                         ret: 500,
//                         msg:'服务器异常'
//                     });
//             } catch(e){
//                     res.send({
//                         ret: 500,
//                         msg:e.message
//                     });
//             }
//  })
// });

module.exports = router;
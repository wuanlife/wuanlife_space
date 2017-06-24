var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../../config/config');


router.get('/', function(req, res) {
  res.json({ message: 'this is groups' });  
});
router.route('/:postid/edit')
    .put(function(req, res, next) {
        request.post({
            url:config.server + 'post/edit_post',
            formData:{
                post_id : req.params.postid,
                p_title : req.body.p_title,
                p_text : req.body.p_text,
            },
            headers:{
                'Access-Token':req.session.user['Access-Token']
            },
        },function(err,httpResponse,body){
            if(!err && httpResponse.statusCode === 200){
                var data = JSON.parse(body).data; 
                res.send(JSON.parse(body));
            }else{
                err && console.log(' Failed:',err.toString());
                res.send(httpResponse.statusCode, {
                    ret: httpResponse.statusCode,
                    msg: JSON.parse(body)
                });
            }
        });
      
    })

router.route('/:postid/replies/:repliyid')
    .delete(function(req, res, next) {
        console.log(`apply: ${config.server}post/delete_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&floor=${req.params.repliyid}`)
        request(`${config.server}post/delete_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&floor=${req.params.repliyid}`,
            function(error, httpResponse, body) {
                if (!error && httpResponse.statusCode == 200) {
                    console.log('delete success!');
                    res.send(JSON.parse(body));
                } else {
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        );        
    })

router.route('/:postid/onSetTop')
    .put(function(req,res,next){
        if(req.body.setTop){
            request({url:`${config.server}post/sticky_post?post_id=${req.params.postid}`,headers:{'Access-Token':req.session.user['Access-Token']}},
                function(error,httpResponse,body){
                    //console.log(req.session.user['Access-Token']);
                    //console.log('error',error+httpResponse.statusCode);
                    //console.log('body',body)
                    if(!error && httpResponse.statusCode==200){
                        console.log('set top success');
                        res.send(JSON.parse(body));
                    }else{
                        res.send(httpResponse.statusCode, {
                            ret: httpResponse.statusCode,
                            msg: JSON.parse(body)
                        });
                    }
                })
        }else{
            //帖子置顶取消
        }
    })


router.route('/:postid/onLock')
    .put(function(req,res,next){
            request({url:`${config.server}post/lock_post?post_id=${req.params.postid}`,headers:{'Access-Token':req.session.user['Access-Token']}},
                function(error,httpResponse,body){
                    if(!error && httpResponse.statusCode==200){
                        console.log('lock success');
                        res.send(JSON.parse(body));
                    }else{
                        res.send(httpResponse.statusCode, {
                            ret: httpResponse.statusCode,
                            msg: JSON.parse(body)
                        });
                    }
                })

    })


router.route('/:postid/onDelete')
    .delete(function(req,res,next){
            console.log('url',`${config.server}post/delete_post?post_id=${req.params.postid}&user_id=${req.session.user.user_id}`);
            request({url:`${config.server}post/delete_post?post_id=${req.params.postid}&user_id=${req.session.user.user_id}`,headers:{'Access-Token':req.session.user['Access-Token']}},
                function(error,httpResponse,body){
                    if(!error && httpResponse.statusCode==200){
                        console.log('delete success');
                        res.send(JSON.parse(body));
                    }else{
                        res.send(httpResponse.statusCode, {
                            ret: httpResponse.statusCode,
                            msg: JSON.parse(body)
                        });
                    }
                })
    })

router.route('/:postid/onEdit')
    .put(function(req,res,next){
            request({url:`${config.server}post/edit_post?post_id=${req.params.postid}`,headers:{'Access-Token':req.session.user['Access-Token']}},
                function(error,httpResponse,body){
                    if(!error && httpResponse.statusCode==200){
                        console.log('edit success');
                        res.send(JSON.parse(body));
                    }else{
                        res.send(httpResponse.statusCode, {
                            ret: httpResponse.statusCode,
                            msg: JSON.parse(body)
                        });
                    }
                })
    })


module.exports.router = router;
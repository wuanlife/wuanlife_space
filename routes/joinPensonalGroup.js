var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* 获取页面 */
//应该是从星球详情页面渲染来的
router.get('/', function(req, res, next) {
    if (req.session.user) {
        var agent = ua(req.headers['user-agent']);
        var page = agent.Mobile ? 'joinPensonalGroupM' : 'joinPensonalGroupM';
        res.render(page, {
                            'title': '申请加入私密星球',
                            'user': req.session.user
                        });
    } else {
        res.redirect('/personal/login');
    }
});
router.get('/sub',function(req,res,next){
    var user_id=req.session.user.user_id,
        group_id=req.query.group_id,
        text=encodeURIComponent(req.query.text);
    if(req.session.user){
        request(config.server+'group/private_group?user_id='+user_id+'&group_id='+group_id+'$text='+text,function(error, response, body){
            if(!error && response.statusCode == 200){
                var result=JSON.parse(body);
                if(result.ret == 200){
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
                console.error('group failed:', error);
                next(error);
            }
        })
    }else{
        res.redirect('/personal/login');
    }
});
module.exports = router;
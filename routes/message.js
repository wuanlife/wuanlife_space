var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
    if (req.session.user) {
        var agent = ua(req.headers['user-agent']),
            pn = req.query.page || 1,
            userid = req.session.user.userID,
            status = req.query.status || 1,
            mtype = req.query.mtype || 1;
        request(config.server + "?service=User.ShowMessage&user_id=" + userid + "&pn=" + pn + "&status=" + status + "&mtype=" + mtype,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    try{
                        var result = JSON.parse(body);
                        var page = '';
                        console.log(result);
                        if (result.ret == 200 && result.data) {
                            switch(parseInt(mtype)){
                                case 1:   //消息类型：回复我的
                                    break;
                                case 2:   //消息类型: 其他通知
                                    break;
                                case 3:   //消息类型：私密星球申请
                                    page = agent.Mobile ? 'messageMobile' : 'message';
                                    res.render(page,{
                                        'result': result.data,
                                        'title' : '消息中心',
                                        'user' : req.session.user
                                    });
                                    break;
                                default:
                                    throw 'no page';
                                    break;
                            }
                        } else{
                            throw result.msg;
                        }
                    } catch(e){
                        next(e);
                    }
                } else {
                    console.error('group failed:', error);
                    next(error);
                }
            }
        );
    }
    else {
        res.redirect('/login');
    }
});

module.exports = router;

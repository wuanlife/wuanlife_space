var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
    if (req.session.user) {
        var agent = ua(req.headers['user-agent']);
        var userid = req.session.user.userID;
        var page = agent.Mobile ? 'messageMobile' : 'message';
        request(config.server + "?service=User.getUserInfo&user_id=" + userid,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.data) {
                        res.render(page, {
                            'result': result.data,
                            'title': '消息中心',
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
                    console.error('user failed:', error);
                    next(error);
                }
            });
    } else {
        res.redirect('/login');
    }
});

router.post('/', function(req, res, next) {
    request.post({
        url: config.server + '?service=User.alterUserInfo',
        formData: {
            user_id: req.session.user.userID,
            sex: req.body.sex,
            year:req.body.year,
            month:req.body.month,
            day:req.body.day
        }
    }, function optionalCallback(err, httpResponse, body) {
        if (err) {
            console.error('alter info failed:', err);
            res.header('Content-type', 'application/json');
            res.header('Charset', 'utf8');
            res.send({
                err: err
            });
        }
        //console.log('alter info successful!  Server responded with:', body);
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        res.send(JSON.parse(body));
    });
});

module.exports = router;

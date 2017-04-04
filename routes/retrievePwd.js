var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	console.log('start bug');
    var agent = ua(req.headers['user-agent']);
    var page = agent.Mobile ? 'retrievePwdMobile' :'retrievePwd';
    try{
        res.render(page, {
            'title':'重置密码',
            'user' : req.session.user,
        });
    } catch(e){
        next(e);
    }     

});

//发送邮箱验证，用于更改密码
router.post('/',function(req, res, next) {
	request.get({
        url: config.server + 'user/send_mail',
        formData: {
            user_email: req.body.email,
            fuck: "fuck",
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            return res.send(JSON.parse(body));
        } else {
            console.log('CreatePlanet error!  Server responded with:', body);
            try {
                console.error('get index posts failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get index posts exception:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            }

        }
	});
});


//更改密码
router.post('/reset', function(req, res, next) {

});

module.exports = router;
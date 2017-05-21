var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../../config/config');


router.get('/', function(req, res) {
  res.json({ message: 'this is groups' });  
});

router.route('/:messageid/agree')
    .post(function(req, res, next) {
        console.log(`apply: ${config.server}user/process_apply?user_id=${req.session.user.user_id}&m_id=${req.params.messageid}&mark=1`)
        request(`${config.server}user/process_apply?user_id=${req.session.user.user_id}&m_id=${req.params.messageid}&mark=1`,
            function(error, httpResponse, body) {
                if (!error && httpResponse.statusCode == 200) {
                    console.log('aplly success!');
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
router.route('/:messageid/reject')
    .post(function(req, res, next) {
        console.log(`apply: ${config.server}user/process_apply?user_id=${req.session.user.user_id}&m_id=${req.params.messageid}&mark=0`)
        request(`${config.server}user/process_apply?user_id=${req.session.user.user_id}&m_id=${req.params.messageid}&mark=0`,
            function(error, httpResponse, body) {
                if (!error && httpResponse.statusCode == 200) {
                    console.log('reject success!');
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
module.exports.router = router;
var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../../config/config');

router.use(function(req, res, next) {
  console.log('Something is happening.');
  next();
});

router.get('/', function(req, res) {
  res.json({ message: 'hooray! welcome to our rest video api!' });  
});
router.post('/checkNewInfo', function(req, res) {
    request(`${config.server}user/check_new_info?user_id=${req.session.user.user_id}`,
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
});

module.exports.router = router;
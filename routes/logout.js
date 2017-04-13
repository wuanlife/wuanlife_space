var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
    delete req.session.user;
    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'indexMobile' : 'index';
        res.render(page, {
            'enterMsg': "你已经成功登出",
            'user': null
        });
    } catch(e){
        next(e);
    }
});

// router.get('/', function(req, res, next) {
//     request(config.server + '?service=User.Logout',
//         function(error, response, body) {
//             if (!error && response.statusCode == 200) {
//                 req.session.destroy(function() {
//                     console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
//                     res.header('Content-type', 'application/json');
//                     res.header('Charset', 'utf8');
//                     res.send(body);
//                 });
//             } else {
//                 console.error('Logout failed:', error);
//                 res.header('Content-type', 'application/json');
//                 res.header('Charset', 'utf8');
//                 res.send({
//                     err: error
//                 });
//             }
//         })
// });

module.exports = router;
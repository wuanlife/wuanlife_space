var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');


router.get('/', function(req, res, next) {
    request(config.server + '?service=User.Logout', function(error, response, body) {
        if (!error && response.statusCode == 200) {
            //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
            var data = JSON.parse(body);
            if (data.ret == 200) {//操作成功或失败均注销成功
                req.session.destroy(function() {
                    res.redirect('back');
                });
            } 
        } else {
            next(error);
        }
    })

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
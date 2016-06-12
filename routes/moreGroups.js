var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/:userid', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    request(config.server + "?service=Group.GetCreate&user_id=" + req.param("userid"),
        function(error, response, body) {
            if (!error) {
                console.log('CreateGroups List Success:OK');
                var result = JSON.parse(body);
                if (result.ret == 200 && result.msg == "") {
                    var page = agent.Mobile ? 'moreGroupsMobile' : 'moreGroups';
                    res.render(page, {
                        'path':'../',
                        result: result.data,
                        'title':"我加入的星球",
                        'userid':req.param("userid")
                    });
                }
            } else {
                console.error('CreateGroups failed:', err);
                next(error);
            }
        })
});

module.exports = router;
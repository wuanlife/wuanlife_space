var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

router.get('/', function(req, res, next) {

    var agent = ua(req.headers['user-agent']);
    try{
        var page = agent.Mobile ? 'mycollectionsMobile' : 'mycollections';
        res.render(page, {
            'result': null,
            'user': req.session.user,
        });
    } catch(e){
        next(e);
    }
    
});

router.post('/:id', function(req, res, next) {
    console.log(`${config.server}post/collect_post?post_id=${req.params.id}&user_id=${req.session.user.user_id}`);
    request(`${config.server}post/collect_post?post_id=${req.params.id}&user_id=${req.session.user.user_id}`,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('cancel collection success!');

                console.log(JSON.parse(body).msg);
                return res.send(JSON.parse(body));
            } else {
                console.log('cancel collection error!  Server responded with:', body);
                 res.send({
                    ret: 500,
                    msg:JSON.parse(body).msg
                });
 
            }
        }
    )
});
module.exports = router;
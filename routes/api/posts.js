var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../../config/config');


router.get('/', function(req, res) {
  res.json({ message: 'this is groups' });  
});
router.route('/:postid')
    .put(function(req, res, next) {
        // set Top
        if(req.body.setTop) {
            request(`${config.server}post/delete_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&floor=${req.params.repliyid}`,
                function(error, httpResponse, body) {
                    if (!error && httpResponse.statusCode == 200) {
                        console.log('delete success!');
                        res.send(JSON.parse(body));
                    } else {
                        res.send({
                            ret: 500,
                            msg:'服务器异常'
                        });
                    }
                }
            );              
        }
      
    })

router.route('/:postid/replies/:repliyid')
    .delete(function(req, res, next) {
        console.log(`apply: ${config.server}post/delete_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&floor=${req.params.repliyid}`)
        request(`${config.server}post/delete_post_reply?user_id=${req.session.user.user_id}&post_id=${req.params.postid}&floor=${req.params.repliyid}`,
            function(error, httpResponse, body) {
                if (!error && httpResponse.statusCode == 200) {
                    console.log('delete success!');
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
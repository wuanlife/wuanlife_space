var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'searchContentMobile' : 'searchContent';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	

});

router.post('/',function(req,res,next){
	//console.log('mdzz',config.server + 'group/search?text=' + req.body.text + '&gnum='+ req.body.gnum +'&pnum=' + req.body.pnum + '&gn=' + req.body.gn + '&pn=' + req.body.pn);
	request(encodeURI(config.server + 'group/search?text=' + req.body.text + '&gnum='+ req.body.gnum +'&pnum=' + req.body.pnum + '&gn=' + req.body.gn + '&pn=' + req.body.pn),
	function optionalCallback(err, httpResponse, body) {
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		//console.log('body',body);
		if (!err && httpResponse.statusCode == 200) {
			var data = JSON.parse(body).data;
			res.send(JSON.parse(body));
		} else{
			err && console.error('Search failed:', err.toString());
			res.send({
				ret:500,
				msg:'服务器异常'
			});
		}
	});	
});


module.exports = router;
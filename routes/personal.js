var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'personalMobile' : 'personal';
		res.render(page, {
			'result': null,
			'user': null,
			'user_id':176,
		});
	} catch(e){
		next(e);
	}
});


router.get('/personalInfo', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'personalInforMobile' : 'personalInfor';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
});

router.get('/invite', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'inviteCodeMobile' : 'inviteCode';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
});


router.get('/myCollection', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'mycollectionsMobile' : 'mycollections';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
});


router.get('/changepassword', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'changepasswordM' : 'changepassword';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
});


router.post('/',function(req,res,next){
	
	request.post({
		url:config.server + 'group/check_status',
		formData:{
			user_id: req.body.userId
		}
	},function(err,httpResponse,body){
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		console.log('err',err);
		console.log('status',httpResponse.statusCode);

		if(!err && httpResponse.statusCode === 200){
			var data = JSON.parse(body).data;
			console.log('data',data);
			console.log('body',body);
			if(data.code === 1){

			}else{

			}

			res.send(JSON.parse(body));
		}else{
			err && console.log('Get inviteCode Failed:',err.toString());
			res.send({
				ret: 500,
				msg: '服务器异常'
			});
		}
	});
});




router.post('/personalInfo', function(req, res, next) {
    request.post({
        url: config.server + 'user/get_user_info',
        formData: {
            user_id: req.body.user_id,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
         	console.log('body',body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage catch exception:', err.toString());
            }

        }

    });
});




router.post('/invite', function(req, res, next) {
    request.post({
        url: config.server + 'user/show_code',
        formData: {
            user_id: req.body.user_id,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage catch exception:', err.toString());
            }

        }

    });
});

router.post('/myCollection', function(req, res, next) {
    request.post({
        url: config.server + 'user/get_collect_post',
        formData: {
            user_id: req.body.user_id,
            pn : req.body.page,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage catch exception:', err.toString());
            }

        }

    });
});

router.post('/changepassword', function(req, res, next) {
    request.post({
        url: config.server + 'user/re_psw',
        formData: {
            user_id: req.body.user_id,
            password:req.body.password,
            psw : req.body.psw,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
             return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get mymessage failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get mymessage catch exception:', err.toString());
            }

        }

    });
});
module.exports = router;
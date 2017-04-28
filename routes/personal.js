var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	console.log('user',req.session.user);
	try{
		var page = agent.Mobile ? 'personalMobile' : 'personal';
		res.render(page, {
			'result': null,
			'user': req.session.user,
		});
	} catch(e){
		next(e);
	}
});


router.get('/login', function(req, res, next) {

	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'loginMobile' : 'login';
		res.render(page, {
			'result': null,
			'user': req.session.user,
		});
	} catch(e){
		next(e);
	}

});

router.get('/personalInfo', function(req, res, next) {
    var result = {};
	if(!req.session.user){
		res.redirect('/personal/login');
		return;
	}
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'personalInforMobile' : 'personalInfor';
        request(config.server + "user/get_user_info?user_id=" + req.session.user.user_id,
            function(error, httpResponse, body) {
                if (!error && httpResponse.statusCode == 200) {
                    console.log('get_user_info success!');
                    result = JSON.parse(body).data;
                    console.log(result.user_name);
                    res.render(page, {
                        'result': result,
                        'user': req.session.user,
                    });
                } else {

                }
            }
        );
	} catch(e){
		next(e);
	}
});

router.get('/invite', function(req, res, next) {
	if(!req.session.user){
		res.redirect('/personal/login');
		return;
	}
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'inviteCodeMobile' : 'inviteCode';
		res.render(page, {
			'result': null,
			'user': req.session.user,
		});
	} catch(e){
		next(e);
	}
});


router.get('/myCollection', function(req, res, next) {
	if(!req.session.user){
		res.redirect('/personal/login');
		return;
	}
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


router.get('/changepassword', function(req, res, next) {
	if(!req.session.user){
		res.redirect('/personal/login');
		return;
	}
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'changepasswordM' : 'changepassword';
		res.render(page, {
			'result': null,
			'user': req.session.user,
		});
	} catch(e){
		next(e);
	}
});

router.get('/logout', function(req, res, next) {

	console.log('logout-req',req);
	req.session.destroy(function(err){
		if(err){
			res.send({
				ret: 500,//尝试
				msg: '退出登录失败',
			});
			return;
		}

		//res.clearCookie(req.session.user.user_name);
		res.redirect('/personal');
	});
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


router.post('/login', function(req, res, next) {
	//如果用户已经用此账号登陆
	if(req.session.user ? req.body.email == req.session.user.user_email:false){
		//重定向到index.
	}else{
		request.post({
			url: config.server + 'user/login',
			formData: {
				user_email: req.body.email,
				password: req.body.password
			}
		}, function optionalCallback(err, httpResponse, body) {
			res.header('Content-type', 'application/json');
			res.header('Charset', 'utf8');
			console.log('err',err);
			console.log('status',httpResponse.statusCode);
			if (!err && httpResponse.statusCode == 200) {
				var data = JSON.parse(body).data;
				console.log('mdzz',data);
				if(data.code == 1) {
					req.session.user = data.info;
					req.session.save(); //保存一下修改后的Session
					console.log('Login successful!  Server responded with:', body);
				} else {
					console.error('Login failed:', body);
				}
				console.log('body',JSON.parse(body));
				res.send(JSON.parse(body));
			} else{
				err && console.error('Login failed:', err.toString());
				res.send({
					ret:500,
					msg:'服务器异常'
				});
			}
		});
	}
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
            user_id: req.body.userId,
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

    request(config.server + 'post/get_collect_post?user_id=' + req.body.userId + '&pn=' + (req.body.page?req.body.page:1), 
    	function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode== 200) {
        	console.log('mdzz',config.server + 'post/get_collect_post?user_id=' + req.body.userId + '&pn=' + (req.body.page?req.body.page:1));
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
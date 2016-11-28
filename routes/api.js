var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var xss = require('xss');

/* GET users listing. */
router.get('/:method', function(req, res, next) {
	var data = {};
    var userID = (req.session.user) ? req.session.user.userID : null;
	switch (req.params.method) {
		case 'getindex':
			var pn = req.param('currentpage');
			request(config.server + '?service=Post.GetIndexPost&pn=' + pn, function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getMyPlanet':
			var pn = req.param('currentpage');
			var id = req.param('userId'); //路由参数，获取相应用户星球展示，非session.user.userid
			request(config.server + '?service=Post.GetMyGroupPost&id=' + id + '&pn=' + pn, function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getPlanetAll':
			var pn = req.param('currentpage');
			request(config.server + '?service=Group.Lists&page=' + pn, function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getPostReplys':
			var pn = req.param('currentpage');
			var post_id = req.param('post_id');
			request(config.server + '?service=Post.GetPostReply&post_id=' + post_id + '&user_id=' + userID +'&pn=' + pn,
			function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getPostOptRight':
			var post_id = req.param('post_id');
			var user_id = userID;
			request(config.server + '?service=Post.GetPostBase&post_id=' + post_id + '&id=' + user_id,
			function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getGroupPlanetShow':
			var pn = req.param('currentpage');
			var groupid = req.param('groupid');
			request(config.server + '?service=Post.GetGroupPost&group_id=' + groupid + '&pn=' + pn + '&user_id=' + userID, function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
        case 'getMessageShow':
            var pn = req.param('currentpage');
            var user_id = req.param('userId');
            var status = req.param('status');
            request(config.server + '?service=User.ShowMessage&user_id=' + user_id + '&pn=' + pn + '&status=' + status, function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            });
            break;            
		case 'getPostForEdit':
			var postid = req.param('postid');
            var user_id = userID;
			request(config.server + '?service=Post.GetPostBase&post_id=' + postid+ '&id=' + user_id,
			function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'isJoinP':
			var groupid = req.param('groupid');
			var userid = userID;
			request(config.server + '?service=Group.GStatus&group_base_id=' + groupid + '&user_id=' + userid, function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getJoindGroup':
			var user_id = req.param('user_id'); //路由参数，获取相应用户星球展示，非session.user.userid
			var pn = req.param('pn');
			request(config.server + '?service=Group.GetJoined&user_id=' + user_id + '&page=' + pn, 
			function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
		case 'getCreateGroup':
			var user_id = req.param('user_id');  //路由参数，获取相应用户星球展示，非session.user.userid
			var pn = req.param('pn');
			request(config.server + '?service=Group.GetCreate&user_id=' + user_id + '&page=' + pn, 
			function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
			});
			break;
        //搜索星球结果
        case 'getSearchGroup':
            var text = encodeURIComponent(xss(req.query.text));
            var gnum = req.query.gnum;
            var gn = req.query.gn;
            request(config.server + '?service=Group.Search&text=' + text + '&gnum=' + gnum + '&gn=' + gn, 
            function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            });
            break;
        //搜索帖子结果
        case 'getSearchPost':
            var text = encodeURIComponent(xss(req.query.text));
            var pnum = req.query.pnum;
            var pn = req.query.pn;
            request(config.server + '?service=Group.Search&text=' + text + '&pnum=' + pnum + '&pn=' + pn, 
            function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        res.send(JSON.parse(body));
                    } 
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            });
            break;
		default:
			res.send('respond with a resource');
			break;
	}
});
router.post('/:method', function(req, res, next) {
    var data = {};
    var userID = (req.session.user) ? req.session.user.userID : null;
    var email = (req.session.user) ? req.session.user.Email : null;
    switch (req.params.method) {
        //帖子置顶
        case 'stickyPost':
            request.post({
                url: config.server + '?service=Post.StickyPost',
                formData: {
                    user_id: userID,
                    post_id: req.body.post_id
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('Sticky failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //帖子取消置顶
        case 'unstickyPost':
            request.post({
                url: config.server + '?service=Post.UnStickyPost',
                formData: {
                    user_id: userID,
                    post_id: req.body.post_id
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('Unsticky failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //帖子收藏
        case 'collectPost':
            request.post({
                url: config.server + '?service=Post.CollectPost',
                formData: {
                    user_id: userID,
                    post_id: req.body.post_id
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('Collect failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //取消帖子收藏
        case 'uncollectPost':
            request.post({
                url: config.server + '?service=Post.DeleteCollectPost',
                formData: {
                    user_id: userID,
                    post_id: req.body.post_id
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('unCollect failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //删除帖子
        case 'deletePost':
            request.post({
                url: config.server + '?service=Post.DeletePost',
                formData: {
                    user_id: userID,
                    post_id: req.body.post_id
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('Delete failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //删除帖子回复
        case 'deletePostReply':
            request.post({
                url: config.server + '?service=Post.DeletePostReply',
                formData: {
                    user_id: userID,
                    post_base_id: req.body.post_id,
                    floor: req.body.floor
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('Delete failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //帖子回复
        case 'postReply':
            request.post({
                url: config.server + '?service=post.Postreply',
                formData: {
                    user_id: userID,
                    post_id: req.param('post_id'),
                    text: xss(req.param('text')),
                    replyfloor: req.body.replyfloor
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        //console.log(JSON.parse(body));
                        return res.send(JSON.parse(body));
                    }
                    console.error('Reply failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        case 'sendAuthCode':
            request.post({
                url: config.server + '?service=User.CheckMail',
                formData: {
                    Email: email
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('Send email failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //锁定帖子
        case 'lockPost':
            request.post({
                url: config.server + '?service=Post.LockPost',
                formData: {
                    post_id: req.body.post_id,
                    user_id: userID
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('lockPost failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //解锁帖子
        case 'unlockPost':
            request.post({
                url: config.server + '?service=Post.UnlockPost',
                formData: {
                    user_id: userID,
                    post_id: req.body.post_id
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('unlockPost failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //加入普通星球
        case 'applyGroup':
            request.post({
                url: config.server + '?service=Group.Join',
                formData: {
                    user_id: userID,
                    group_base_id: req.body.groupid
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('apply failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //加入私密星球
        case 'applyPrivateGroup':
            request.post({
                url: config.server + '?service=Group.PrivateGroup',
                formData: {
                    user_id: userID,
                    group_id: req.body.groupid,
                    text: req.body.applyText
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('apply failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //处理申请加入私密星球
        case 'processApplyPrivateGroup':
            request.post({
                url: config.server + '?service=User.ProcessApp',
                formData: {
                    user_id: userID,
                    message_id: req.body.messageID,
                    mark: req.body.operation
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try {
                    if (!err && httpResponse.statusCode == 200){
                        //console.log(JSON.parse(body));
                        return res.send(JSON.parse(body));
                    }
                    console.error('processApply failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
        break;
        //是否有新消息
        case 'checkNewInfo':
            request.post({
                url: config.server + '?service=User.CheckNewInfo',
                formData: {
                    user_id: userID
                }
            }, function optionalCallback(err, httpResponse, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                    if (!err && httpResponse.statusCode == 200){
                        return res.send(JSON.parse(body));
                    }
                    console.error('check failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
                catch(e){
                    res.send({
                        ret: 500,
                        msg:e.message
                    });
                }
            });
            break;
        //删除星球成员
        case 'deleteMember':
            request.post({
                url:config.server + '?service=Group.DeleteGroupMember',
                formData:{
                    group_id:req.body.groupid,
                    user_id:userID,
                    member_id:req.body.memberid
                    }   
            },function(error, response, body) {
                res.header('Content-type', 'application/json');
                res.header('Charset', 'utf8');
                try{
                   if (!error && response.statusCode == 200) {
                        //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                        return res.send(JSON.parse(body));
                    } 
                    console.error('deleteMember failed:', err);
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(e){
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            });
            break;
        default:
            res.send('respond with a resource');
            break;
    }

});
module.exports = router;
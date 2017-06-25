var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var session = require('express-session');
var MongoStore = require('connect-mongo/es5')(session);//node低于4.0版本使用'connect-mongo/es5'
// var session = require('express-session');
// var RedisStore = require('connect-redis')(session);
var flash = require('connect-flash');

//监控插件
var fundebug = require("fundebug-nodejs");
fundebug.apikey="e76768413a54073546036eff31808236d9ee45db26a32628c931a3556565a708";

var routes = require('./routes/index');

//RESTful api
var api = require('./routes/api/main');
var api_groups = require('./routes/api/groups');
var api_messages = require('./routes/api/messages');
var api_posts = require('./routes/api/posts');
var api_image = require('./routes/api/image');








var createGroup = require('./routes/createGroup');
var allGroup = require('./routes/allGroup');
var logout = require('./routes/logout');
var resetPassword = require('./routes/resetPassword');
var verifyEmail = require('./routes/verifyEmail');
var search = require('./routes/search');
var test = require('./routes/test');
var news = require('./routes/news').router;
var uptoken = require('./routes/uptoken');
var userManage = require('./routes/userManage');
var myCollections = require('./routes/myCollections');
var myself = require('./routes/personal');

var changepassword = require('./routes/changepassword');
var joinPensonalGroup = require('./routes/joinPensonalGroup');


var myPlanet = require('./routes/myPlanet');
//消息
var myMessage = require('./routes/myMessage');
//帖子详情
var postdetails = require('./routes/postdetails');

var inviteCode = require('./routes/inviteCode');
var register = require('./routes/register');
var retrievePwd = require('./routes/retrievePwd');
var modifyPwd = require('./routes/modifyPwd');
var addPlanet = require('./routes/addPlanet');
var addPlanetPrivate = require('./routes/addPlanetPrivate');
var searchContent = require('./routes/searchContent');

var users = require('./routes/users');
var groups = require('./routes/groups');
var discovery = require('./routes/discovery');


var config = require('./config/config');
var mongodb = require('./models/db.js');




var app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');

app.use(flash());

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
	extended: false
}));

// var options = {
//   "host": "50.30.35.9",
//   "port": "3510",
//   "ttl": 60 * 60 * 24 * 30,   //Session的有效期为30天
// };

// // 此时req对象还没有session这个属性
// app.use(session({
//   store: new RedisStore(options),
//   secret: 'wuan is powerful'
// }));

app.use(cookieParser('qefr4MFZAxSm9cdB'));
app.use(express.static(path.join(__dirname, 'public')));

var sessionMiddleware = session({
    secret: config.db.cookieSecret,
    key: config.db.db, //cookie name
    cookie: {
        maxAge: 1000 * 60 * 60 * 24 * 30,
    }, //30 days
    store: new MongoStore({
        url: 'mongodb://localhost/wuanDB'
    })
});
// app.use(session({
//   secret: config.db.cookieSecret,
//   key: config.db.db,//cookie name
//   cookie: {maxAge: 1000 * 60 * 60 * 24 * 30},//30 days
//   store: new MongoStore({
//     url: 'mongodb://localhost/wuanDB'
//   })
// }));
app.use(sessionMiddleware);

//http://stackoverflow.com/questions/23923365/how-to-separate-routes-on-node-js-and-express-4
app.use('/api', api.router);
app.use('/api/groups',api_groups.router);
app.use('/api/messages', api_messages.router);
app.use('/api/posts', api_posts.router);
app.use('/api/image', api_image.router);

app.use('/', routes);
app.use('/discovery', discovery);
app.use('/register',register);
app.use('/createGroup', createGroup);

app.use('/allGroup', allGroup);
app.use('/logout',logout);
app.use('/resetPassword',resetPassword);
app.use('/verifyEmail',verifyEmail);
app.use('/news',news);
app.use('/search',search);
app.use('/test',test);
app.use('/uptoken',uptoken);
app.use('/userManage',userManage);

app.use('/addPlanet',addPlanet);


app.use('/mycollections',myCollections);
app.use('/personal',myself);
app.use('/inviteCode',inviteCode);
app.use('/register',register);

app.use('/retrievepassword',retrievePwd);
app.use('/modifypassword',modifyPwd);
app.use('/addPlanetPrivate',addPlanetPrivate);
app.use('/searchContent',searchContent);

app.use('/myplanet',myPlanet);
app.use('/mymessage',myMessage);
app.use('/changepassword',changepassword);
app.use('/joinPensonalGroup',joinPensonalGroup);
app.use('/posts',postdetails);

app.use('/users', users);
app.use('/groups', groups);





// catch 404 and forward to error handler
app.use(function(req, res, next) {
	var err = new Error('Not Found');
	err.status = 404;
	next(err);
});

// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
	app.use(function(err, req, res, next) {
		res.status(err.status || 500);
		res.render('error', {
			message: err.message,
			error: err
		});
	});
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
	res.status(err.status || 500);
	res.render('error', {
		message: err.message,
		error: {}
	});
});

app.use(fundebug.ExpressErrorHandler);


module.exports = app;
module.exports.sessionMiddleware = sessionMiddleware;
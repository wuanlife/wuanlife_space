var express = require('express'),
	router = express.Router(),
	chance = require('chance').Chance();

router.get('/', function(req, res, next) {
	var totalcount = 108;
	var list = [];
	console.log("begin1");

	console.log('params: ' + JSON.stringify(req.params));
	console.log('body: ' + JSON.stringify(req.body));
	console.log('query: ' + JSON.stringify(req.query));
	console.log("callback:" + req.query.callback);
	console.log("start");
	for (var i = (req.query.pageindex - 1) * req.query.pagesize + 1; i <= (req.query.pageindex * req.query.pagesize); i++) {
		if (i > totalcount)
			break;
		else {
			list.push({
				title: '现场情况非常稳定',
				content: chance.paragraph({
					sentences: 1
				}), //'黔东南消防支队开发区中队消防官兵在当地群众配合下，经两个小时的紧急捕捞，整车散落路面的鲶鱼一条不差的重新装车启运.',
				address: chance.address(), //"北京市朝阳区雍和宫",
				user_name: chance.name({
					middle_initial: true
				}),
				user_img: "../images/user.jpg",
				user_type: "实习记者",
				time: "19:26",
				image: ["../images/img1.jpg", "../images/img2.jpg", "../images/img3.jpg"]
			});
		}
	}
	console.log("build end");
	res.header('Content-type', 'application/json');
	res.header('Charset', 'utf8');
	if (req.query.callback == undefined) {
		res.send(JSON.stringify({
			"list": list,
			"totalcount": totalcount,
			"pageindex": req.query.pageindex,
			"pagesize": req.query.pagesize,
			"pagecount": parseInt(totalcount / req.query.pagesize) + (parseInt(totalcount % req.query.pagesize) > 0 ? 1 : 0)
		}));
	} else {
		res.send(req.query.callback + '(' + JSON.stringify({
			"list": list,
			"totalcount": totalcount,
			"pageindex": req.query.pageindex,
			"pagesize": req.query.pagesize,
			"pagecount": parseInt(totalcount / req.query.pagesize) + (parseInt(totalcount % req.query.pagesize) > 0 ? 1 : 0)
		}) + ');');
	}
});

module.exports = router;
/*
 * Created with Sublime Text 3.
 * github: https://github.com/breakinferno/HJloading
 * Author:吕飞
 * Date: 2017-03-28
 * Time: 20:26:55
 * Contact: 1972952841@qq.com
 */

//可以改进地方：1:读取div模板文件，2:Template[num].css的使用,3:css通过对象传递。

//考虑自动停止还是手动停止lodaing操作

;
(function(root,factory){
	//需要jquery
	root.HJLoading = factory(window.jQuery || $);

})(this,function($){
	//HJLoading对象
	var HJLoading = function(cssPath) {
		this.relativeCssPath = cssPath;
		//加载默认样式文件
		this.linkCSS(cssPath,'HJLoading-default');
	};

	//HJLoadingTPL
	var Template = [{
		css: "",
		content:"<div class='loadingContent'><div class='spinner0'><div class='spinner-container container1'><div class='circle'></div><div class='circle2'></div><div class='circle3'></div><div class='circle4'></div></div><div class='spinner-container container2'><div class='circle1'></div><div class='circle2'></div><div class='circle3'></div><div class='circle4'></div></div><div class='spinner-container container3'><div class='circle1'></div><div class='circle2'></div><div class='circle3'></div><div class='circle4'></div></div></div></div>",		
	},{
		css: "",
		content:"<div class='loadingContent'><div class='spinner1'></div></div>",
	},{
		css: "",
		content:"<div class='loadingContent'><div class='spinner2'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></div></div>",
	},{
		css:"",
		content:"<div class='loadingContent'><div class='spinner3'><div class='dot1'></div><div class='dot2'></div></div></div>",
	},{
		css:"",
		content:"<div class='loadingContent'><div class='spinner4'><div class='cube1'></div><div class='cube2'></div></div></div>",
	},{
		css:"",
		content:"<div class='loadingContent'><div class='spinner5'><div class='double-bounce1'></div><div class='double-bounce2'></div></div></div>",
	},{
		css:"",
		content:"<div class='loadingContent'><div class='spinner6'><div class='rect1'></div><div class='rect2'></div><div class='rect3'></div><div class='rect4'></div><div class='rect5'></div></div></div>"
	}];

	//可以通过此函数来获得目录，也可以适用template的css属性
	function getPath(){
		var js = document.scripts,
			jsPath;
		for(var s=0;s<js.length;s++){
			var path = js[s].src;
			var isCon = new RegExp("\.\\/HJLoadingX\\.js").test(path);
			if(isCon){
				jsPath = js[s];
				break;
			}
		}
		var path =  jsPath.src.substring(0,jsPath.src.lastIndexOf("/js")+1);
		return path;
	}

	//定义loading启动
	function startHJLoading(settings){

		var config = settings || {};
		var that = this;
		//loading 目标
		
		var loading = {};
		
		loading.loadingScale = config.loadingScale || that.loadingScale;
		loading.target = config.target || 'html';
		//当前loading id
		loading.id = config.loadingId || 'HJLoading' + that.loadingCount++;
		//loading 的模板id
		loading.loadingTPLId = config.loadingTPLId || that.HJLoadingTPLId;
        
        loading.loadingTPLId = loading.loadingTPLId < 0 ? 0 : loading.loadingTPLId % Template.length;

		//如果有模板则传模板，没有则根据id设置模板
		loading.loadingTPL = config.loadingTPL ? '<div class="loadingContent">' + config.loadingTPL + '</div>': that.linkTPL(loading.loadingTPLId);
		//设置css样式
		//支持内联css样式和外部css样式表,可以为cssobj，或者为css样式表路径，否则默认适用样式
		loading.loadingCSS = typeof config.loadingCSS !== "undefined" ? (typeof config.loadingCSS === "object"?config.loadingCSS:that.linkCSS(that.relativeCssPath,config.loadingCSS,loading)): that.linkCSS(that.relativeCssPath,loading.loadingTPLId,loading);

		//loading 时间
		loading.loadingTime = config.loadingTime || that.loadingTime;

		//loadingTPLId 和loadingTPL不可同时存在
		if(config.loadingTPL && config.loadingTPLId){
			return;
		}

		loading.loadingObj = $(loading.loadingTPL);
		//载入loading
		$("body").append(loading.loadingObj);

		//设置位置
		that.resetPosition(loading);

		that.bindEvent(loading);
		//loading对象加入
		that.loadings.push(loading);

		//设置定时器
		if(loading.loadingTime){
			setTimeout(function(){
				that.stop(loading.id);
			},loading.loadingTime);
		}
	}

	//定义loading停止
	function stopHJLoading(id){
		//var md = 0;
		var that = this;
		var result = that.searchLoading(id);

		if(result){
			var targetLoading = result.loading;
			var targetIndex = result.index;
			
			targetLoading.loadingObj.remove();
			//考虑停止loading时移除link
			if($('head').find('#'+targetLoading.linkedCssId)){
				$('head').find('#'+targetLoading.linkedCssId).remove();
			}
			//从数组删除
			that.loadings.splice(targetIndex,1);
			that.loadingCount --;
		}else{
			alert('不能停止没有的元素');
		}	


	}

	//定义loading重定位
	function repositionHJLoading(loadingArg){
		var that = loadingArg;
		var width = 0;
		var height = 0;
		var loading = that.loadingObj;
		var targetContent = $(that.target);
		//真实loading
		var ele = $(loading).children();

		var offset = targetContent.offset();

		//设置宽高
		if($(targetContent)[0].tagName === 'HTML'){
			width = Math.max($(targetContent).outerWidth(),$(window).width());
			height = Math.max($(targetContent).outerHeight(), $(window).height());
		}else{
			width = $(targetContent).outerWidth();
			height = $(targetContent).outerHeight();
		}

		loading.css({
			width: width,
			height:height,
		});
		var eleAttr = Math.min(width,height);
		eleAttr = eleAttr * that.loadingScale;
		
		if(that.loadingCSS){
			ele.css(that.loadingCSS);
		}

		ele.css({
			width:eleAttr,
			height: eleAttr,
		});

		//设置loadingContent位置
		$(loading).css({
			top: offset.top,
			left: offset.left,
		});

		var times = 0;
		
		var eleOffset = {};
		if(targetContent[0].tagName === "HTML"){
			h = $(window).height();
			w = $(window).width();
			eleOffset.top = (h - ele.height()) / 2 + $(window).scrollTop();
			eleOffset.left = (w - ele.width()) / 2 + $(window).scrollLeft();
			ele.css({
				top: eleOffset.top,
				left: eleOffset.left,
			});
		}

	}

	HJLoading.prototype = {
		//默认loading模板,可以通过init修改为自定义样式
		HJLoadingTPLId: 0,
		//判断是否loading
		isLoading: false,
		//设置loading时间
		loadingTime : false,
		//默认路径
		//path:getPath(),
		//管理默认id
		loadingCount: 0,
		//管理loading对象
		loadings:[],
		loadingScale : 0.5,
		csspath:'',
		//读取html文件
		readHTML:function(href,name){
			$.ajax({
				async:false,
				url: href + "template/HJLoadingTPL" + name + ".html",
				success:function(result){
					alert(url);
					return result;
				},
			});
		},

		//动态加载loadingTPL
		linkTPL:function(tplname){

			var content = Template[tplname].content;
			return content;
		},
		//动态加载css,需要传入参数css文件夹的相对路径，
		//cssname三种形式,number(0-6),string.css ,string
		linkCSS : function(href,cssname,target){
			var that = this;
			if(!href){
				return;
			}
			var head = $('head')[0];
			var link = document.createElement('link');
			var timeout = 0;

			var id,isdot;
			//判断是否以.css结尾
			var reg = /\.css/;

			if(typeof cssname === "number"){
				id = 'HJLoading' + cssname;
			}else{
				id = cssname.replace(reg,'');
			}

			link.rel = 'stylesheet';
			link.type = 'text/css';
			//对用户输入.css或者没有.css都能正确处理(去除.css)
/*			var reg = /.\.css/;
			if(reg.test(id)){

			}*/

			link.href = (href.lastIndexOf('/') === href.length-1? href : href + '/') + id + ".css";
			link.id = id;

			if(target){
				target.linkedCssId = id;
			}

			if(!$('#' + id)[0]){
				head.appendChild(link);
			}

			if(typeof fn!== 'function'){
				return;
			}

			//轮训css是否加载完毕
			(function poll(){
				if(++timeout > 8*1000/100){
					return window.console && console.error("HJLoading-default.css:Invalid");
				}
				$('head > #'+id)[0] ? fn():setTimeout(poll,100);
			}())
		},
		//开始
		start: startHJLoading,

		//停止
		stop: stopHJLoading,

		resetPosition: repositionHJLoading,

		searchLoading: function(id){
			/*for(i=0;i<this.loadings.length;i++){
				if()
			}*/

			var that = this;
/*			var temp = that.loadings.map(function(item,index,array){
				if(item.id === id){
					return {
							loading:item,
							index: index};
				}
			});*/
			//这里希望用户自觉不要输入相同的id
			var obj;
			for(var i=0;i<that.loadings.length;i++){
				if(that.loadings[i].id === id){
					obj = {
						loading : that.loadings[i],
						index : i,
					};
				}
			}
			return obj;
		},

		bindEvent:function(loadingArg){
			var that = this;
			$(that.target).on('stop',function(){
				that.stop(loadingArg);
			});
			//窗口改变时随之改变位置，只有在整个html文档loading才有效
			$(window).on('resize',function(){
				that.resetPosition(loadingArg);
			})
		},
	};

	return HJLoading;
});
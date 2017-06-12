
'use strict';
(function(win,$){
    //定义扩展函数
    var _e = function(fn){
        var E = window.wuanEditor;
        if (E) {
            //执行传入的函数
            fn(E);
        }
    };
    
    //定义构造函数
    (function(){
        if (win.wuanEditor) {
            alert('一个页面不能重复引用editor.js!');
        }

        var Editor = function(elemId){ 
            var elem = document.getElementById(elemId);
            var nodeName = elem.nodeName;
            if (nodeName != 'DIV') {
                return;
            }
            this.elem = elem;
            this.$elem = $(elem);
            this.$elem.addClass('wuanEditor-container');

            this.init();
        }

        Editor.fn = Editor.prototype;
        Editor.$body = $('body');
        Editor.$document = $(document);
        Editor.$window = $(window);
        win.wuanEditor = Editor;
    })();

    //工具函数
    _e(function(E){

        // 获取随机数
        E.random = function () {
            return Math.random().toString().slice(2);
        };
    })

    // editor api
    _e(function(E){
        // 预定义 ready 事件
        E.fn.ready = function (fn) {

            if (!this.readyFns) {
                this.readyFns = [];
            }

            this.readyFns.push(fn);
        };

        // 处理ready事件
        E.fn.readyHeadler = function () {
            var fns = this.readyFns;

            while (fns.length) {
                fns.shift().call(this);
            }
        };
    })

    //range api
    _e(function(E){
        var supportRange = typeof document.createRange === 'function';
        //获取当前range
        E.fn.getCurrentRange = function(){
            var selection,
                range;
            if(supportRange){
                selection = document.getSelection();
                if (selection.getRangeAt && selection.rangeCount) {
                    range = document.getSelection().getRangeAt(0);
                }
            }else{
                range = document.selection.createRange();
            }
            return range;
        }

        //保存选区数据
        E.fn.saveSelection = function(){
            this.currentRange = this.getCurrentRange();
        }

        //恢复选中区域
        E.fn.restoreSelection = function(){
            if(!this.currentRange){
                return;
            }
            var selection,
                range;
            if(supportRange){
                selection = document.getSelection();
                selection.removeAllRanges();
                selection.addRange(this.currentRange);
            }else{
                range = document.selection.createRange();
                range.setEndPoint('EndToEnd', this.currentRange);
                if(this.currentRange.text.length === 0){
                    range.collapse(false);
                }else{
                    range.setEndPoint('StartToStart', this.currentRange);
                }
                range.select();
            }
        }

        // 获取选区对应的DOM对象
        E.fn.getRangeElem =  function(range){
            range = range || this.getCurrentRange() || this.currentRange;

            if (!range) {
                return;
            }
            if (supportRange) {
                var dom = range.commonAncestorContainer;

                if (dom.nodeType === 1) {
                    return dom;
                } else {
                    return dom.parentNode;
                }
            } else{
                var dom = range.parentElement();

                if (dom.nodeType === 1) {
                    return dom;
                } else {
                    return dom.parentNode;
                }
            }
        }
    });
    // dom selector
    _e(function(E){
        var matchesSelector;

        // matchesSelector hook
        function _matchesSelectorForIE(selector) {
            var elem = this;
            var $elems = $(selector);
            var result = false;

            // 用jquery查找 selector 所有对象，如果其中有一个和传入 elem 相同，则证明 elem 符合 selector
            $elems.each(function () {
                if (this === elem) {
                    result = true;
                    return false;
                }
            });

            return result;
        }

        // 根据条件，查询自身或者父元素，符合即返回
        E.fn.getSelfOrParentByName = function(elem, selector, check){
            if (!elem || !selector) {
                return;
            }

            if (!matchesSelector) {
            // 定义 matchesSelector 函数
                matchesSelector = elem.webkitMatchesSelector || 
                                elem.mozMatchesSelector ||
                                elem.oMatchesSelector || 
                                elem.matchesSelector;
            }

            if (!matchesSelector) {
                // 如果浏览器本身不支持 matchesSelector 则使用自定义的hook
                matchesSelector = _matchesSelectorForIE;
            }

            var txt = this.txt.$txt.get(0);
            while(elem && elem !== txt && $.contains(txt,elem)){
                if (matchesSelector.call(elem, selector)) {
                    // 符合 selector 查询条件
                    if (!check) {
                        // 没有 check 验证函数，直接返回即可
                        console.log(elem);
                        return elem;
                    }

                    if (check(elem)) {
                        // 如果有 check 验证函数，还需 check 函数的确认
                        return elem;
                    }
                }

                // 如果上一步没经过验证，则将跳转到父元素
                elem = elem.parentNode;
            }
            return;
        }
    });
    // 插入文字过滤标签
    _e(function(E){
        E.fn.htmlEscape = function(html){
            return html.replace(/[<>&"]/g,function(c){
                return {'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;'}[c];
            });
        }
    });

    //失去光标进行的操作
    _e(function(E){
        E.fn.command = function(commandName,commandValue){
            this.restoreSelection();
            if(document.selection)
                this.currentRange.pasteHTML(commandValue); 
            else
                document.execCommand(commandName, false,commandValue);
            this.saveSelection();

        }
    });
    //有下拉框的菜单选项
    _e(function(E){
        var DropPanel = function(editor,menuItem,dropOpt){
            this.editor = editor;
            this.$menuItem = menuItem;
            this.$dropOpt = dropOpt;

            this.init();
        }

        DropPanel.fn = DropPanel.prototype;
        E.DropPanel = DropPanel;

        DropPanel.fn.hideEvent = function(){
            var self = this;
            var menuItem = self.$menuItem.get(0);
            var dropOpt = self.$dropOpt.get(0);
            var $dropOpt = self.$dropOpt;

            E.$body.on('click',  function(event) {
                if (!self.isShowing) {
                    return;
                }

                var trigger = event.target;
                if (menuItem === trigger || $.contains(menuItem,trigger)) {
                    return;
                }
                if (dropOpt === trigger || $.contains(dropOpt,trigger)) {
                    return;
                }

                self.hide();
                // $dropOpt.hide();
                // self.isShowing = false;
            });
        }

        DropPanel.fn.toggleEvent = function(){
            var self = this;
            var $menuItem = self.$menuItem;
            var $dropOpt = self.$dropOpt;

            $menuItem.on('click', function(event) {
                if (self.isShowing) {
                    self.hide();
                    // $dropOpt.hide();
                    // self.isShowing = false;
                } else{
                    self.show();
                    // $dropOpt.show();
                    // self.isShowing = true;
                }
            });
        }

        //渲染DropPanel
        DropPanel.fn.render = function(){
            var self = this;
            var editor = self.editor;
            var $dropOpt = self.$dropOpt;

            // 渲染到页面
            editor.$elem.append($dropOpt);

            // 记录状态
            self.rendered = true;
        }

        // 定位
        DropPanel.fn.position = function(){
            var self = this;
            var editor = self.editor;
            var $dropOpt = self.$dropOpt;
            var $menuContainer = editor.menuContainer.$menuContainer;
            var $menuDom = self.$menuItem;
            var menuPosition = $menuDom.position();

            // 取得 menu 的位置、尺寸属性
            var menuTop = menuPosition.top;
            var menuLeft = menuPosition.left;
            var menuHeight = $menuDom.height();
            var menuWidth = $menuDom.width();

            // 取得 dropOpt 的尺寸属性
            var dropOptWidth = $dropOpt.outerWidth();

            // 取得 $elem 的尺寸
            var elemWidth = editor.$elem.outerWidth();

            // 初步计算 dropOpt 位置属性
            var top = menuTop + menuHeight;
            var left = menuLeft + menuWidth/2;
            var marginLeft = 0 - dropOptWidth/2;

            // 如果超出了左边界，则移动回来（要和左侧有10px间隙）
            if ((0 - marginLeft) > (left - 10)) {
                marginLeft = 0 - (left - 10);
            }

            // 如果超出了有边界，则要左移（且和右侧有10px间隙）
            var valWith = (left + dropOptWidth + marginLeft) - elemWidth;
            if (valWith > -10) {
                marginLeft = marginLeft - valWith - 10;
            }

            // 设置样式
            $dropOpt.css({
                top: top,
                left: left,
                'margin-left': marginLeft
            });
        }

        //显示
        DropPanel.fn.show =  function(){
            var self = this;
            if (!self.rendered) {
                // 第一次show之前，先渲染
                self.render();
            }

            if (self.isShowing) {
                return;
            }

            var $dropPanel = self.$dropOpt;
            $dropPanel.show();

            // 定位
            self.position();

            // 记录状态
            self.isShowing = true;
        }

        // 隐藏
        DropPanel.fn.hide = function(){
            var self = this;

            var $dropPanel = self.$dropOpt;
            $dropPanel.hide();

            // 记录状态
            self.isShowing = false;
        }

        DropPanel.fn.init = function(){
            this.hideEvent();
            this.toggleEvent();
        }
    });

    //menuContainer 构造函数
    _e(function(E){
        //定义构造函数
        var MenuContainer = function (editor) {
            this.editor = editor;

            this.init();
        };

        MenuContainer.fn = MenuContainer.prototype;

        E.MenuContainer = MenuContainer;

        MenuContainer.fn.init = function () {
            var self = this;
            var $menuContainer = $('<div class="wuanEditor-menu-container clearfix"></div>');

            self.$menuContainer = $menuContainer;

        };

        MenuContainer.fn.render = function () {
            var $menuContainer = this.$menuContainer;
            var $editorContainer = this.editor.$elem;

            $editorContainer.append($menuContainer);
        };
    });

    // txt 构造函数
    _e(function (E) {

        // 定义构造函数
        var Txt = function (editor) {
            this.editor = editor;

            // 初始化
            this.init();
        };

        Txt.fn = Txt.prototype;

        // 暴露给 E 
        E.Txt = Txt;

        //初始化
        Txt.fn.init = function(){
            var self = this;
            var $txt = $('<div class="wuanEditor-txt" contentEditable="true"></div>');

            self.$txt = $txt;
        }

        // 渲染
        Txt.fn.render = function () {
            var $txt = this.$txt;
            var $editorContainer = this.editor.$elem;
            $editorContainer.append($txt);
        };

        //计算高度
        Txt.fn.initHeight =  function(){
            var editor = this.editor;
            var $txt = this.$txt;
            var $editorContainerHeight = this.editor.$elem.outerHeight();
            var $menuContainerHeight = this.editor.menuContainer.$menuContainer.outerHeight();
            var txtHeight = $editorContainerHeight - $menuContainerHeight;

            // 限制最小为 50px
            txtHeight = txtHeight < 50 ? 50 : txtHeight;

            $txt.outerHeight(txtHeight);
        }
    });

    // 增加menuContainer对象
    _e(function (E) {

        E.fn.addMenuContainer = function () {
            var editor = this;
            editor.menuContainer = new E.MenuContainer(editor);
            editor.menuContainer.render();
        };

    });

    // 增加编辑区域对象
    _e(function (E, $) {

        E.fn.addTxt = function () {
            var editor = this;
            editor.txt = new E.Txt(editor);
            editor.txt.render();

            editor.ready(function(){
                editor.txt.initHeight();
            })
        };

    });

    //menu 构造函数
    _e(function(E){

        var Menu = function(opt){
            this.editor = opt.editor;
            this.id = opt.id;
            this.title = opt.title;
            this.init();
        }

        Menu.fn = Menu.prototype;
        E.Menu = Menu;

        Menu.fn.init = function(){
            var self = this;
            var $menuItem = $('<div class="menu-item clearfix"></div>');
            var $a = $('<a href="#" tabindex="-1" title="'+ self.title +'"></a>');
            var $i = $('<i class="wuaneditor-menu-img-'+ self.id +'"></i>');

            if (self.id == 'img') {

                //设置上传按钮id
                var id = 'upload' + E.random();
                self.editor.imgUploadBtnId = id;
                $i.attr('id', id);
            }

            $a.append($i);
            $menuItem.append($a);
            self.$menuItem = $menuItem;

            self.pushMenu();
        }

        Menu.fn.pushMenu = function(){
            var $menuContainer = this.editor.menuContainer.$menuContainer;
            var $menuItem = this.$menuItem
            $menuContainer.append($menuItem);
        }
    });

    //全局配置
    _e(function(E){
        E.config = {};

        //菜单配置
        E.config.menus = ['link','img'];

        //自定义图片上传初始化事件
        E.config.imgUpload;
    });

    //对象配置
    _e(function(E){
        E.fn.initDefaultConfig = function(){
            var editor = this;
            editor.config = $.extend({}, E.config);
        }
    })

    //菜单功能，目前只有图片上传和插入链接
    _e(function(E){
        // 存储创建菜单的函数
        E.createMenuFns = [];
        E.createMenu = function (fn) {
            E.createMenuFns.push(fn);
        };

        // 创建所有菜单
        E.fn.addMenus = function () {
            var editor = this;
            var menus = this.config.menus;

            //检查是否在配置中存在
            function check(menuOpt){
                if (menus.indexOf(menuOpt) >= 0) {
                    return true;
                }
                return false;
            }
            // 遍历所有的菜单创建函数，并执行
            $.each(E.createMenuFns, function (k, createMenuFn) {
                createMenuFn.call(editor,check);
            });
        };
    });

    _e(function(E){
        E.createMenu(function(check){
            var menuOpt = 'link';
            if (!check(menuOpt)) {
                return;
            }

            var editor = this;
            // var link = editor.menus.link;
            // var $textInput = $('#' + link.linkTextId);
            // var $urlInput = $('#' + link.linkUrlId);
            // var $btnSubmit = $('#' + link.btnSubmit);
            // var $menuItem = $('#' + link.menuItem);
            // var $dropOpt = $('#' + link.dropOpt);
            // 创建 dropPanel
            var $panel = $('<div class="wuanEditor-drop-panel clearfix" style="width: 300px;"></div>');
            var $content = $('<div></div>');
            var $div1 = $('<div style="margin:20px 10px;" class="clearfix"></div>');
            var $div2 = $div1.clone();
            var $div3 = $div1.clone().css('margin', '0 10px');
            var $textInput = $('<input type="text" class="block" placeholder="文本"/>');
            var $urlInput = $('<input type="text" class="block" placeholder="url"/>');
            var $btnSubmit = $('<button class="right">提交</button>');
            var $btnCancel = $('<button class="right gray">取消</button>');

            $div1.append($textInput);
            $div2.append($urlInput);
            $div3.append($btnSubmit).append($btnCancel);
            $content.append($div1).append($div2).append($div3);
            $panel.append($content);

            var menu = new E.Menu({
                editor: editor,
                id: menuOpt,
                title: '链接'
            })
            menu.dropPanel = new E.DropPanel(editor,menu.$menuItem,$panel);

            $btnSubmit.click(function(e) {
                e.preventDefault();
                var rangeElem = editor.getRangeElem();
                var targetElem = editor.getSelfOrParentByName(rangeElem, 'a');
                var text = $.trim($textInput.val());
                var url = $.trim($urlInput.val());
                if (!text) {
                    $textInput.val('请输入文本');
                    return;
                }
                if (!url) {
                    $urlInput.val('请输入url');
                    return;
                }
                text = editor.htmlEscape(text);
                if (targetElem) {
                    targetElem.setAttribute('href', url);
                    targetElem.innerHTML = text;
                } else{
                    var linkHtml = '<a href="' + url + '" target="_blank">' + text + '</a>';
                    editor.command("insertHTML",linkHtml);
                }
                menu.dropPanel.hide();
            });

            $btnCancel.click(function (e) {
                e.preventDefault();
                menu.dropPanel.hide();
            });
        });
    })
    //上传图片
    _e(function(E){
        E.createMenu(function(check){
            var menuOpt = 'img';
            if (!check(menuOpt)) {
                return;
            }

            var editor = this;
            
            var menu = new E.Menu({
                editor: editor,
                id: menuOpt,
                title: '上传图片'
            });

            var imgUpload = editor.config.imgUpload;
            if (!imgUpload) {
                return;
            }
            imgUpload.call(editor);
            // var uploader = Qiniu.uploader({
            //     runtimes: 'html5,flash,html4', //上传模式,依次退化
            //     browse_button: editor.menus.img.btnId, //上传选择的点选按钮，**必需**
            //     uptoken_url: '/uptoken',
            //     domain: 'http://7xlx4u.com1.z0.glb.clouddn.com/',//bucket 域名，下载资源时用到，**必需**
            //     max_file_size: '10mb', //最大文件体积限制
            //     flash_swf_url: '/javascripts/plupload/Moxie.swf', //引入flash,相对路径
            //     filters: {
            //         mime_types: [
            //             //只允许上传图片文件 （注意，extensions中，逗号后面不要加空格）
            //             {
            //                 title: "图片文件",
            //                 extensions: "jpg,gif,png,bmp"
            //             }
            //         ]
            //     },
            //     max_retries: 3, //上传失败最大重试次数
            //     chunk_size: '4mb', //分块上传时，每片的体积
            //     auto_start: true, //选择文件后自动上传，若关闭需要自己绑定事件触发上传
            //     init: {
            //         'FileUploaded': function(up, file, info){
            //             var domain = up.getOption('domain');
            //             var res = $.parseJSON(info);
            //             var sourceLink = domain + res.key; //获取上传成功后的文件的Url
            //             //editor.insertImage(sourceLink);// 插入图片到editor
            //             editor.command("insertImage",sourceLink);
            //         },
            //         'Error': function(up, err, errTip) {
            //             //上传出错时,处理相关的事情
            //             console.log('on Error');
            //         }
            //     }
            // });
        });
    });
    //事件绑定
    _e(function(E){
        // 存储创建菜单的函数
        E.bindEvents = [];
        E.bindEvent = function (fn) {
            E.bindEvents.push(fn);
        };

        // 创建所有菜单
        E.fn.initEvents = function () {
            var editor = this;

            // 遍历所有的菜单创建函数，并执行
            $.each(E.bindEvents, function (k, event) {
                event.call(editor);
            });
        };
    });

    //选区保存
    _e(function(E){
        E.bindEvent(function(){
            var editor = this;
            var eArea = editor.txt.$txt;
            eArea.on('mouseup keyup',function(event) {
                editor.saveSelection();
            });
        });
    });

    //粘贴文本处理
    _e(function(E){
        E.bindEvent(function(){
            var editor = this;
            var eArea = editor.txt.$txt;
            var resultHtml = '';
            eArea.on('paste', function(e) {
                e.preventDefault();
                resultHtml = ''; //清空resultHtml

                var pasteHtml;
                var data = e.clipboardData || e.originalEvent.clipboardData;
                var ieData = window.clipboardData;

                if (data && data.getData) {
                    // w3c
                    pasteHtml = data.getData('text/plain');
                } else if (ieData && ieData.getData){
                    // IE
                    pasteHtml = ieData.getData('text');
                } else{
                    return;
                }

                if (pasteHtml) {
                    var reg = /^https?:\/\/.*?/;
                    if (reg.test(pasteHtml)) {
                        resultHtml = '<a href="' + pasteHtml + '" target="_blank">' + pasteHtml + '</a>';
                    } else{
                        resultHtml = pasteHtml
                    }
                    editor.command('insertHtml', resultHtml);
                }
            });
        });
    });
    //初始化
    _e(function(E){
        E.fn.init = function(){

            // 初始化 editor 默认配置
            this.initDefaultConfig();

            //增加menuContainer
            this.addMenuContainer();

            //增加Txt编辑区域
            this.addTxt();
        }
    });
    //用户自定义后启动
    _e(function(E){
        E.fn.create = function(){
            // 检查 E.$body 是否有值
            // 如果在 body 之前引用了 js 文件，body 尚未加载，可能没有值
            if (!E.$body || E.$body.length === 0) {
                E.$body = $('body');
                E.$document = $(document);
                E.$window = $(window);
            }

            //添加菜单
            this.addMenus();

            //添加事件绑定
            this.initEvents();

            // 处理ready事件
            this.readyHeadler();
        }
    })
})(window,jQuery);
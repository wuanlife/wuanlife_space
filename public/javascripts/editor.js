
'use strict';
(function(win,$){
    var Editor = function(editor){
        this.editId = editor.editId; //编辑区域ID
        this.insertImgBtnId = editor.insertImgBtnId; //图片上传按钮
        this.link = editor.link //插入链接相关组件对象
        this.elem = document.getElementById(editor.editId); //编辑器根节点
        this.$elem = $('#' + editor.editId);
    }
    Editor.fn = Editor.prototype;
    Editor.$body = $('body');
    //定义扩展函数
    var _e = function(fn){
        var E = Editor;
        if (E) {
            //执行传入的函数
            fn(E);
        }
    }
    win.wuanEditor = Editor;
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

            var editorContainer = this.elem;
            while(elem && elem !== editorContainer && $.contains(editorContainer,elem)){
                if (matchesSelector.call(elem, selector)) {
                    // 符合 selector 查询条件
                    if (!check) {
                        // 没有 check 验证函数，直接返回即可
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
        var DropPanel = function(menuItem,dropOpt){
            this.menuItem = menuItem;
            this.dropOpt = dropOpt;

            this.init();
        }

        DropPanel.fn = DropPanel.prototype;
        E.DropPanel = DropPanel;

        DropPanel.fn.hideEvent = function(){
            var self = this;
            var menuItem = self.menuItem.get(0);
            var dropOpt = self.dropOpt.get(0);
            var $dropOpt = self.dropOpt;

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

                $dropOpt.hide();
                self.isShowing = false;
            });
        }

        DropPanel.fn.toggleEvent = function(){
            var self = this;
            var menuItem = self.menuItem;
            var dropOpt = self.dropOpt;

            menuItem.on('click', function(event) {
                if (self.isShowing) {
                    dropOpt.hide();
                    self.isShowing = false;
                } else{
                    dropOpt.show();
                    self.isShowing = true;
                }
            });
        }

        DropPanel.fn.init = function(){
            this.hideEvent();
            this.toggleEvent();
        }
    });
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

            // 遍历所有的菜单创建函数，并执行
            $.each(E.createMenuFns, function (k, createMenuFn) {
                createMenuFn.call(editor);
            });
        };
    });
    _e(function(E){
        E.createMenu(function(){
            var editor = this;
            var link = editor.link;
            var $textInput = $('#' + link.linkTextId);
            var $urlInput = $('#' + link.linkUrlId);
            var $btnSubmit = $('#' + link.btnSubmit);
            var $menuItem = $('#' + link.menuItem);
            var $dropOpt = $('#' + link.dropOpt);

            var menu = new E.DropPanel($menuItem,$dropOpt);

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
                    $linkElem = $(targetElem);
                    $linkElem.attr('href', url);
                    $linkElem.text(text);
                } else{
                    var linkHtml = '<a href="' + url + '" target="_blank">' + text + '</a>';
                    editor.command("insertHTML",linkHtml);
                }
            });
        });
    })
    //上传图片
    _e(function(E){
        E.createMenu(function(){
            var editor = this;
            var uploader = Qiniu.uploader({
                runtimes: 'html5,flash,html4', //上传模式,依次退化
                browse_button: editor.insertImgBtnId, //上传选择的点选按钮，**必需**
                uptoken_url: '/uptoken',
                domain: 'http://7xlx4u.com1.z0.glb.clouddn.com/',//bucket 域名，下载资源时用到，**必需**
                max_file_size: '10mb', //最大文件体积限制
                flash_swf_url: '/javascripts/plupload/Moxie.swf', //引入flash,相对路径
                filters: {
                    mime_types: [
                        //只允许上传图片文件 （注意，extensions中，逗号后面不要加空格）
                        {
                            title: "图片文件",
                            extensions: "jpg,gif,png,bmp"
                        }
                    ]
                },
                max_retries: 3, //上传失败最大重试次数
                chunk_size: '4mb', //分块上传时，每片的体积
                auto_start: true, //选择文件后自动上传，若关闭需要自己绑定事件触发上传
                init: {
                    'FileUploaded': function(up, file, info){
                        var domain = up.getOption('domain');
                        var res = $.parseJSON(info);
                        var sourceLink = domain + res.key; //获取上传成功后的文件的Url
                        //editor.insertImage(sourceLink);// 插入图片到editor
                        editor.command("insertImage",sourceLink);
                    },
                    'Error': function(up, err, errTip) {
                        //上传出错时,处理相关的事情
                        console.log('on Error');
                    }
                }
            });
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
            var eArea = editor.$elem;
            eArea.on('mouseup keyup',function(event) {
                editor.saveSelection();
            });
        });
    });
    //初始化
    _e(function(E){
        E.fn.init = function(){
            this.addMenus();
            this.initEvents();
        }
    });
})(window,jQuery);
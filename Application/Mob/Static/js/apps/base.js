/**
 * Created by Administrator on 2015-5-23.*/
$(function(){
    follower.bind_follow();
    goback();//绑定后退事件
});


//弹窗评论
var comment = function () {
    $('.atcomment').magnificPopup({
        type: 'ajax',
        overflowY: 'scroll',
        modal: true,
        callbacks: {
            ajaxContentAdded: function () {
                console.log(this.content);
            }
        }
    });
}

var addcomment = function () {

    $('#cancel').click(function () {
        $('.mfp-close').click();
    });
    $('#confirm').click(function () {
        var data = $("#at_comment").serialize();
        var url = $("#at_comment").attr('data-url');

        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                $('.mfp-close').click();
                $(".addmore").prepend(msg.html);
                toast.success('评论成功!');
                del();
                comment();
            } else {
                toast.error(msg.info);
            }
        }, 'json');
    })
};


//以下都是表情包
var insertFace = function (obj) {
    var url = obj.attr('data-url');
    $('.XT_insert').css('z-index', '1000');
    $('.XT_face').remove();
    var html = '<div class="XT_face  XT_insert"><div class="triangle sanjiao"></div><div class="triangle_up sanjiao"></div>' +
        '<div class="XT_face_main"><div class="XT_face_title"><span class="XT_face_bt" style="float: left">常用表情</span>' +
        '<a onclick="close_face()" class="XT_face_close">X</a></div><div id="face" style="padding: 10px;"></div></div></div>';
    obj.parents('.weibo_post_box').find('#emot_content').html(html);
    getFace(obj.parents('.weibo_post_box').find('#emot_content'), 'miniblog', url);
};
var face_chose = function (obj) {
    var textarea = obj.parents('.weibo_post_box').find('textarea');

    if(textarea.attr('disabled') == 'disabled'){
        return false;
    }

    textarea.focus();
    textarea.val(textarea.val() + '[' + obj.attr('title') + ']');

    var pos = getCursortPosition(textarea[0]);
    var s = textarea.val();
    if (obj.attr('data-type') == 'miniblog') {
        textarea.val(s.substring(0, pos) + '[' + obj.attr('title') + ']' + s.substring(pos));
        setCaretPosition(textarea[0], pos + 2 + obj.attr('title').length);
    } else {
        textarea.val(s.substring(0, pos) + '[' + obj.attr('title') + ':' + obj.attr('data-type') + ']' + s.substring(pos));
        setCaretPosition(textarea[0], pos + 3 + obj.attr('title').length + obj.attr('data-type').length);
    }


}
var getFace = function (obj, miniblog, url) {
    $.post(url, {pkg: 'miniblog'}, function (res) {
        var expression = res.expression;
        var _imgHtml = '';
        if (miniblog.length > 0) {
            for (var k in expression) {
                _imgHtml += '<a href="javascript:void(0)" data-type="' + expression[k].type + '" title="' + expression[k].title + '" onclick="face_chose($(this))";><img src="' + expression[k].src + '" width="24" height="24" /></a>';
            }
            _imgHtml += '<div class="c"></div>';
        } else {
            _imgHtml = '获取表情失败';
        }
        obj.find('#face').html(_imgHtml);


    }, 'json');
};
var close_face = function () {
    $('.XT_face').remove();
};

//上传单张图片
var add_one_img = function () {
    $('#fileloadone').fileupload({
        done: function (e, result) {
            var $fileInputone = $(this);
            var src = result.result.data.file.path;
            var ids = $('#one_img_id').val(result.result.data.file.id);


            if (!ids == null) {
                $('.show_cover').hide();
            } else {
                $('.show_cover').show();
            }

            $("#cover_url").html('');
            $("#cover_url").html('<img src="' + src + '"style="width:72px;height:72px"  data-role="issue_cover" >');
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        }
    });
};


//阅读消息
    var Notify = {
        'readMessage': function (obj, message_id) {
            var url = $(obj).attr('data-url');
            var hrefurl = $(obj).attr('href')
                $.post(url, {message_id: message_id}, function (msg) {
                    location.href = hrefurl;
                }, 'json');
        },
        /**
         * 将所有的消息设为已读
         */
        'setAllReaded': function (obj) {
            var url = $(obj).attr('data-url');
            $.post(url,{}, function () {
                 toast.success('操作成功');
                setTimeout(function () {
                    window.location.reload();
                }, 1000);

            });
        }
    };

var follower = {
    'bind_follow': function () {
        $('[data-role="follow"]').unbind('click')
        $('[data-role="follow"]').click(function () {
            var $this = $(this);
            var uid = $this.attr('data-follow-who');
            $.post(U('Core/Public/follow'), {uid: uid}, function (msg) {
                if (msg.status) {
                    $this.attr('class', $this.attr('data-before'));
                    $this.attr('data-role', 'unfollow');
                    $this.html('已关注');
                    follower.bind_follow();
                    toast.success(msg.info, '温馨提示');
                } else {
                    toast.error(msg.info, '温馨提示');
                }
            }, 'json');
        })

        $('[data-role="unfollow"]').unbind('click')
        $('[data-role="unfollow"]').click(function () {
            var $this = $(this);
            var uid = $this.attr('data-follow-who');
            $.post(U('Core/Public/unfollow'), {uid: uid}, function (msg) {
                if (msg.status) {
                    $this.attr('class', $this.attr('data-after'));
                    $this.attr('data-role', 'follow');
                    $this.html('关注');
                    follower.bind_follow();
                    toast.success(msg.info, '温馨提示');
                } else {
                    toast.error(msg.info, '温馨提示');
                }
            }, 'json');
        })
    }
}
var goback=function(){
    $('#goback').click(function () {
        var need_confirm=$(this).attr('need-confirm');
        if(need_confirm){
            var confirm_info=$(this).attr('confirm-info');
            if (confirm(confirm_info))
            {
                history.go(-1);
            }
        }else{
            history.go(-1);
        }
    });
}




function U(url, params, rewrite) {


    if (window.Think.MODEL[0] == 2) {

        var website = _ROOT_ + '/';
        url = url.split('/');

        if (url[0] == '' || url[0] == '@')
            url[0] = APPNAME;
        if (!url[1])
            url[1] = 'Index';
        if (!url[2])
            url[2] = 'index';
        website = website + '' + url[0] + '/' + url[1] + '/' + url[2];

        if (params) {
            params = params.join('/');
            website = website + '/' + params;
        }
        if (!rewrite) {
            website = website + '.html';
        }

    } else {
        var website = _ROOT_ + '/index.php';
        url = url.split('/');
        if (url[0] == '' || url[0] == '@')
            url[0] = APPNAME;
        if (!url[1])
            url[1] = 'Index';
        if (!url[2])
            url[2] = 'index';
        website = website + '?s=/' + url[0] + '/' + url[1] + '/' + url[2];
        if (params) {
            params = params.join('/');
            website = website + '/' + params;
        }
        if (!rewrite) {
            website = website + '.html';
        }
    }

    if (typeof (window.Think.MODEL[1]) != 'undefined') {
        website = website.toLowerCase();
    }
    return website;
}




$(function(){
    local_comment_page_count = 2;
    bind_local_comment();
})


var bind_local_comment = function(){

    $('[data-role="do_local_comment"]').unbind('click');
    $('[data-role="do_local_comment"]').click(function(){
        var $this = $(this);
        var $textarea = $this.closest('.weibo_post_box').find('textarea');
        var url = $this.attr('data-url');
        var this_url =$this.attr('data-this-url');
        var path = $this.attr('data-path');
        var content = $textarea.val();
        var extra = $this.attr('data-extra');
        $.post(url, {content: content,path:path,this_url:this_url,extra:extra}, function (res) {
            if(res.status){
                $textarea.val('');
                $('.localcomment_list').prepend(res.data);
                toast.success('发布成功');
            }else{
                toast.error('发布失败');
            }
            bind_local_comment();
        },'json');
    })


    $('[data-role="reply_local_comment"]').unbind('click');
    $('[data-role="reply_local_comment"]').click(function(){
        var $this = $(this);
        var $textarea = $('.weibo_post_box').find('textarea');
        var nickname = $this.attr('data-nickname');
        if($textarea.attr('disabled') == 'disabled'){
            $textarea.val("")
        }else{
            $textarea.val("").focus().val('回复 @' + nickname + ' ：');
        }

        bind_local_comment();
    })

    $('[data-role="del_local_comment"]').unbind('click');
    $('[data-role="del_local_comment"]').click(function(){
        var $this = $(this);
        var id = $this.attr('data-id');

        var count_model = $this.attr('data-count-model');
        var count_field = $this.attr('data-count-field');

        var url = U('mob/base/dellocalcomment');

        $.post(url, {id: id,count_model:count_model,count_field:count_field}, function (res) {
            if(res.status){
                $this.closest('.comment-item').fadeOut();
                toast.success('删除成功');
            }else{
                toast.error('删除失败');
            }
        },'json');
        bind_local_comment();
    })


    $('[data-role="show_more_localcomment"]').unbind('click');
    $('[data-role="show_more_localcomment"]').click(function(){
        var $this = $(this);
        var path = $this.attr('data-path');
        var url = U('mob/base/getLocalCommentList');
        $.post(url, {path: path,page:local_comment_page_count}, function (res) {
            if(res){
                $('.localcomment_list').append(res);
                local_comment_page_count++;
            }else{
                toast.error('没有更多了');
            }

        },'json');
        bind_local_comment();
    })


}

//多图上传图片
function add_img() {
    $('#fileupload').fileupload({
        dataType: 'json',
        autoUpload: true,
        acceptFileTypes:  /(\.|\/)(gif|jpe?g|png)$/i,
        maxNumberOfFiles :15,
        maxFileSize: 5000000,

        add: function (e, result) {
            var $fileInput = $(this);
            var file = result.files;

            create(file, $fileInput);
            result.submit();
        },

        done: function (e, result) {

            $('.waitbox').hide();
            var $fileInput = $(this);
            if (result.result.status == 0) {
                alert('上传失败')
            }
            var src = result.result.data.file.path;
            console.log(src);

            var ids = $('#img_ids').val();





            upAttachVal('add', result.result.data.file.id, $('#img_ids'));

            var ids = ids.split(',');

            if (ids.length >= 9) {
                $('#fileupload').attr("disabled", "disabled");

                alert('最多发送九张');
            }
            if ($.inArray(result.result.data.file.id, ids) >= 0) {
                alert('暂不能重复发送')
            } else {
                createLi(result, $fileInput);
            }

        }
    })
    $('#fileupload').unbind('click');
    $('#fileupload').click(function () {
        var $fileInput = $(this);
        Box($fileInput);
        var ids = $('#img_ids').val();
        var ids = ids.split(',');
        if (ids.length >= 9) {
            $('#fileupload').attr("disabled", "disabled");
            alert('最多发送九张');
        }
    });
    function removeLi($li, file_id) {
        upAttachVal('remove', file_id, $('#img_ids'));
        if ($li.siblings('li').length <= 0) {
            $li.parents('.parentFileBox').remove();
        } else {
            $li.remove();
        }
        var ids = $('#img_ids').val();

        var ids = ids.split(',');

        if (ids.length <= 9) {
            $('#fileupload').removeAttr("disabled");
        }
    }
    function removeBox($li, file_id) {
        upAttachVal('remove', file_id, $('#img_ids'));
        if ($li.siblings('li').length <= 0) {
            $li.parents('.parentFileBox').remove();
        } else {
            $li.remove();
        }
        var ids = $('#img_ids').val();

        var ids = ids.split(',');

        if (ids.length <= 9) {
            $('#fileupload').removeAttr("disabled");
        }
    }
    function create(file, $fileInput) {
        var $parentFileBox = $fileInput.parents('.fileupload-buttonbar').find('.parentFileBox');


        var div = ' <li class="waitbox">\
            <img src= ' + _LOADING_ + '  style="width:90px,hight:90px"> \
        </li> ';
        $parentFileBox.children('.fileBoxUl').append(div);



    }
    function Box($fileInput){
        var $parentFileBox = $fileInput.parents('.fileupload-buttonbar').find('.parentFileBox');
        if ($parentFileBox.length <= 0) {

            var div = '<div class="parentFileBox"style="z-index: 1000">\
        <ul class="fileBoxUl" style="padding: 0px;"></ul>\
    </div>';
            $fileInput.after(div);
            $parentFileBox = $fileInput.next('.parentFileBox');

        }
    }
    //创建文件操作div;
    function createLi(result, $fileInput) {
        var file_id = result.result.data.file.id;
        var $parentFileBox = $fileInput.parents('.fileupload-buttonbar').find('.parentFileBox');
        var src = result.result.data.file.path;

        //添加子容器;

        var li = '<li id="fileBox_' + file_id + '" class="diyUploadHover am-fl" > \
        <div class="viewThumb">\
            <a class="del-btn am-icon-close" style="position:absolute;right: 5px;top: -5px;color: red"></a>		\
            <img src="' + src + '">\
        </div> \
    </li>';

        $parentFileBox.children('.fileBoxUl').append(li);
        //父容器宽度;
        var $width = $('.fileBoxUl>li').length * 180;
        var $maxWidth = $fileInput.parent().width();

        $width = $maxWidth > $width ? $width : $maxWidth;
        $parentFileBox.width($width);

        var $fileBox = $parentFileBox.find('#fileBox_' + file_id);

        var $Cancel = $fileBox.find('.del-btn').click(function () {

            var $li = $(this).parents('li');
            removeLi($li, file_id);
        });
    }
    var upAttachVal = function (type, attachId, obj) {
        var $attach_ids = obj;
        var attachVal = $attach_ids.val();
        var attachArr = attachVal.split(',');
        var newArr = [];

        for (var i in attachArr) {
            if (attachArr[i] !== '' && attachArr[i] !== attachId.toString()) {
                newArr.push(attachArr[i]);
            }
        }
        type === 'add' && newArr.push(attachId);

        if (newArr.length <= 9) {
            $attach_ids.val(newArr.join(','));
            return newArr;
        } else {
            return false;
        }

    }
};
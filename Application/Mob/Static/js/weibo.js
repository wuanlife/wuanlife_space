$(document).ready(function () {
    forward();
    del();
    doforward();
    submit();
    support();
    bigimg();
    bind_page();
    show_video();
});

//转发的弹窗
function forward() {
    $('.forward').magnificPopup({
        type: 'ajax',
        overflowY: 'scroll',
        modal: true,
        callbacks: {
            ajaxContentAdded: function () {
                console.log(this.content);
            }
        }
    })
}
//转发微博
var doforward = function () {
    $('#cancel').click(function () {
        $('.mfp-close').click();
    });

    $('#conf').click(function () {
        var data = $("#forward").serialize();
        var url = $("#forward").attr('data-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                $('.mfp-close').click();
                $(".ulclass").prepend(msg.html);
                toast.success('转发成功!');
                forward();
                support();
            } else {
                toast.error(msg.info);
            }
        }, 'json');
    })
}


//图片轮播
var bigimg = function () {
    $('.img-content').each(function () {
        $(this).magnificPopup({
            delegate: 'div',
            type: 'image',
            overflowY: 'scroll',
            overflowX: 'scroll',
            tLoading: '正在载入 #%curr%...',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1]
            },
            image: {
                tError: '<a href="%url%">图片 #%curr%</a> 无法被载入.',

                verticalFit: true
            }
        });
    });
};

//点赞
var support = function () {
    $('.support').unbind('click');
    $('.support').click(function () {
        var weibo_id = $(this).attr('weibo_id');
        var user_id = $(this).attr('user_id');
        var url = $(this).attr('url');
        var that = $(this);
        $.post(url, {id: weibo_id, uid: user_id}, function (msg) {
            if (msg.status == 1) {
                toast.success('谢谢您的支持!');
                that.parent().find('span').html(parseInt(that.parent().find('span').html()) + 1);
                that.find('i').removeClass('am-icon-thumbs-o-up');
                that.find('i').addClass('am-icon-thumbs-up');
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}

//微博页面评论
var submit = function () {
    $('.submit').unbind('click');
    $('.submit').click(function () {
        var weibo_conetnet = $(this).parent('#comment').find('.content').val();
        var weibo_Id = $(this).attr('weiboId');
        var url = $(this).attr('url');
        $.post(url, {weiboId: weibo_Id, weibocontent: weibo_conetnet}, function (msg) {
            if (msg.status == 1) {
                $(".addmore").prepend(msg.html);
                toast.success('评论成功!');
                $('#comment_content_text').val('');
                del();
                comment();

            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}

//删除评论
var del = function () {
    $('.delete').unbind('click');
    $('.delete').click(function () {
        if (confirm("你确定要删除此评论吗？")) {
            var comment_id = $(this).attr('comment-id');
            var weibo_id = $(this).attr('weibo-id');
            var url = $(this).attr('url');
            $.post(url, {commentId: comment_id, weiboId: weibo_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                    toast.success('删除成功!');

                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
//查看更多微博
var page = 1;
function bind_page() {
    $('#getmore').unbind('click');
    $('#getmore').click(function () {
        var url = $(this).attr('data-url');

        $("#getmore").html("查看更多" + '&raquo;');
        $.post(url, {page: page + 1}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
                forward();
                support();
                bigimg();
            } else {
                $("#getmore").html("全部加载完成！");
                $(".look-more").delay(3000).hide(0);
                bigimg();
            }
        }, 'json');
    });
    $('#getmorefocus').unbind('click');
    $('#getmorefocus').click(function () {
        var url = $(this).attr('data-url');

        $("#getmorefocus").html("查看更多" + '&raquo;');
        $.post(url, {page: page + 1}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
                forward();
                support();
                bigimg();
            } else {
                $("#getmorefocus").html("全部加载完成！");
                $(".look-more").delay(3000).hide(0);
                bigimg();
            }
        })
    });
    $('#getmorehotweibo').unbind('click');
    $('#getmorehotweibo').click(function () {
        var url = $(this).attr('data-url');

        $("#getmorehotweibo").html("查看更多" + '&raquo;');
        $.post(url, {page: page + 1}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
                forward();
                support();
                bigimg();
            } else {
                $("#getmorehotweibo").html("全部加载完成！");
                $(".look-more").delay(3000).hide(0);
                bigimg();
            }
        })
    });
}

var show_video = function () {
    $('[data-role="show_video"]').click(function () {
        var html = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="257" height="33" >' +
            '<param name="movie" value="' + $(this).attr('data-src') + '"  /> ' +
            '<param name="quality" value="high" />' +
            '<param name="menu" value="false" />' +
            ' <param name="wmode" value="transparent" />' +
            '<param name="allowScriptAccess" value="always" />' +
            ' <embed  src="'+ $(this).attr('data-src')+'" play="true" allowScriptAccess="always" quality="high" wmode="transparent" menu="false" pluginspage="http://www.adobe.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="100%" height="330"></embed >' +
            '</object>';
        $(this).html(html).removeAttr('style');
    });
}



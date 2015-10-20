/**
 * Created by Administrator on 2015-5-23.
 */
$(document).ready(function () {
    bind_page();
    submit();
    support();
    del();
    comment();

});

//查看更多专辑
var page = 1;
function bind_page() {
    $('#getmore').unbind('click');
    $('#getmore').click(function(){
        var url=  $(this).attr('data-url');
        $("#getmore").html("查看更多...");
        $.post(url, {page: page + 1}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
                forward();
                support();

            } else {
                $("#getmore").html("全部加载完成！");
                $(".look-more").delay(3000).hide(0);
            }
        });
    });

    $('#getmoreclass').unbind('click');
    $('#getmoreclass').click(function(){
        var url=  $(this).attr('data-url');
        var issue_class=  $(this).attr('data-url');
            $("#getmoreclass").html("查看更多...");
            $.post(url, {page: page + 1,id:issue_class}, function (msg) {
                if (msg.status) {
                    $(".ulclass").prepend(msg.html);
                    page++;
                    forward();
                    support();
                } else {
                    $("#getmoreclass").html("全部加载完成！");
                    $(".look-more").delay(3000).hide(0);
                }
            })
    });
}
//专辑评论
var submit=function() {
    $('.submit').unbind('click');
    $('.submit').click(function () {
        var issue_conetnet = $(this).parent('#comment').find('.content').val();
        var issue_id = $(this).attr('issue_id');
        var uid = $(this).attr('uid');
        var url = $(this).attr('url');
        $.post(url, {content: issue_conetnet, issueId: issue_id,uid:uid}, function (msg) {
            if (msg.status == 1) {
                $(".addmore").prepend(msg.html);
                $('#comment_content_text').val('');
                toast.success('评论成功!');
                del();
                comment();
            } else {
                toast.error(msg.info);
            }
        }, 'json')

    });
}
//删除评论
var del=function() {
    $('.delete').unbind('click');
    $('.delete').click(function () {
        if (confirm("你确定要删除此评论吗？")) {
            var comment_id = $(this).attr('comment_id');
            var issue_id = $(this).attr('issue_id');
            var url = $(this).attr('url');
            $.post(url, {commentId: comment_id, issueId: issue_id}, function (msg) {
                if (msg.status) {
                    toast.success('删除成功!');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);

                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
//点赞
var support=function() {
    $('.support').unbind('click');
    $('.support').click(function () {
        var weibo_id = $(this).attr('weibo_id');
        var user_id = $(this).attr('user_id');
        var url = $(this).attr('url');
        var that=$(this);
        $.post(url, {id: weibo_id, uid: user_id}, function (msg) {
            if (msg.status == 1) {
                toast.success('谢谢您的支持!');
                that.parent().find('span').html(parseInt(that.parent().find('span').html())+1);
                that.find('i').removeClass('am-icon-heart-o');
                that.find('i').addClass('am-icon-heart');
            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}



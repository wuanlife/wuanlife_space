$(document).ready(function () {
    bind_page();
    /*    submit();*/
    collection();
    delcomment();
    dellzlcomment();
    support();
    delpost();
});


//查看更多帖子
var page = 1;
function bind_page() {
    $('#getmorepost').unbind('click');
    $('#getmorepost').click(function () {
        $("#getmorepost").html("查看更多...");
        var order=$(this).attr('data-order');
        $.post(U('Mob/forum/addMoreForum'), {page: page + 1,order:order}, function (msg) {
            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmorepost").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })

    $('#getmoreblockpost').unbind('click');
    $('#getmoreblockpost').click(function () {
        $("#getmoreblockpost").html("查看更多...");
        var forum_class = $(this).attr('data-role');
        var order=$(this).attr('data-order');
        $.post(U('Mob/forum/addMorePostSectionDetail'), {page: page + 1, id: forum_class,order:order}, function (msg) {
            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmoreblockpost").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })
}
/*//回复帖子
 var submit=function() {
 $('.submit').click(function () {
 var forum_conetnet = $(this).parent('#comment').find('.content').val();
 var forum_Id = $(this).attr('weiboId');
 var url = $(this).attr('url');

 $.post(url, {forumId: forum_Id, forumcontent: forum_conetnet}, function (msg) {
 if (msg.status == 1) {
 $(window).scrollTop('#pid');
 toast.success('回复成功!');
 $('#comment_content_text').val('');
 } else {
 toast.error(msg.info);
 }
 }, 'json')
 });
 }*/
//收藏
var collection = function () {
    $('.collection').click(function () {
        var post_id = $(this).attr('post_id');
        var add = $(this).attr('add');
        var url = $(this).attr('url');
        $.post(url, {add: add, post_id: post_id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 200);
            } else {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 200);
            }
        }, 'json')
    });
}



//回复弹窗
/*function reply_post() {
    $('.reply_post').magnificPopup({
        type: 'ajax',
        overflowY: 'scroll',
        modal: true,
        callbacks: {
            ajaxContentAdded: function () {
                console.log(this.content);
            }
        }
    })
}*/

//楼中楼at用户弹窗
function lzl_at_user() {
    $('.lzl_at_user').unbind();
    $('.lzl_at_user').magnificPopup({
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
//删除LZL评论
function dellzlcomment() {
    $('.dellzlreply').unbind('click');
    $('.dellzlreply').click(function () {
        if (confirm("你确定要删除此评论吗？")) {
            var lzlreply_id = $(this).attr('comment-id');
            var url = $(this).attr('url');
            $.post(url, {lzlreplyId: lzlreply_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                    toast.success('删除成功!');

                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
//删除帖子评论
function delcomment() {
    $('.delreply').unbind('click');
    $('.delreply').click(function () {
        if (confirm("你确定要删除此评论吗？")) {
            var reply_id = $(this).attr('comment-id');
            var url = $(this).attr('url');
            $.post(url, {replyId: reply_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                    toast.success('删除成功!');
                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
//点赞
var support = function () {
    $('.support').unbind('click');
    $('.support').click(function () {
        var post_id = $(this).attr('post_id');
        var user_id = $(this).attr('user_id');
        var url = $(this).attr('url');
        var that = $(this);
        $.post(url, {id: post_id, uid: user_id}, function (msg) {
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

//删除帖子
function delpost() {
    $('.delpost').unbind('click');
    $('.delpost').click(function () {
        if (confirm("你确定要删除此贴吗？")) {
            var post_id = $(this).attr('post-id');
            var url = $(this).attr('url');
            var index_url=$(this).attr('index-url');
            $.post(url, {id: post_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.href = index_url;
                    }, 500);
                    toast.success('删除成功!');
                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
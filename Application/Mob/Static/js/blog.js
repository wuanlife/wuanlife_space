$(document).ready(function () {
    submit();
    del();
    bind_page();
});



var submit=function() {
    $('.submit').unbind('click');
    $('.submit').click(function () {
        var blog_conetnet = $(this).parent('#comment').find('.content').val();
        var blog_id = $(this).attr('blog_id');
        var uid = $(this).attr('uid');
        var url = $(this).attr('url');
        $.post(url, {content: blog_conetnet, blogId: blog_id,uid:uid}, function (msg) {
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
var del=function() {
    $('.delete').unbind('click');
    $('.delete').click(function () {
        if (confirm("你确定要删除此评论吗？")) {
            var comment_id = $(this).attr('comment_id');
            var blog_id = $(this).attr('blog_id');
            var url = $(this).attr('url');
            $.post(url, {commentId: comment_id, blogId: blog_id}, function (msg) {
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






//查看更多资讯
var page = 1;
function bind_page() {
    $('#getmorehotblog').unbind('click');
    $('#getmorehotblog').click(function(){
        var url=  $(this).attr('data-url');
        $("#getmorehotblog").html("查看更多...");
        $.post(url, {page: page + 1}, function (msg) {
            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmorehotblog").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        });
    });

    $('#getmorecallblog').unbind('click');
    $('#getmorecallblog').click(function(){
        var url=  $(this).attr('data-url');
        $("#getmorecallblog").html("查看更多...");
        $.post(url, {page: page + 1,mark:1}, function (msg) {
            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmorecallblog").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        })
    });

    $('#getmorecmyblog').unbind('click');
    $('#getmorecmyblog').click(function(){
        var url=  $(this).attr('data-url');
        $("#getmorecmyblog").html("查看更多...");
        $.post(url, {page: page + 1,mark:2}, function (msg) {
            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmorecmyblog").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        })
    });

    $('#getmorecclassblog').unbind('click');
    $('#getmorecclassblog').click(function(){
        var url=  $(this).attr('data-url');
        var title_id=  $(this).attr('data-role');
        $("#getmorecclassblog").html("查看更多...");
        $.post(url, {page: page + 1,mark:3,titleid:title_id}, function (msg) {
            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmorecclassblog").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        })
    });
}




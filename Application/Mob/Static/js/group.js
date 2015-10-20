$(document).ready(function () {
    lzl_at_user();
    attend();              //加入组
    quit();                  //退出组
    invitationFriend();   //邀请好友
    saveType();//新建分类
    bind_group_cate();//帖子分类编辑和删除
    addNotic();//添加公告
    removeGroupMember();//移出群组
    receiveMember();//人员审核通过
    dellzlcomment();//删除楼中楼回复
    delcomment();//删除评论
    collect();//收藏帖子
    unCollect();//取消收藏
    support();//点赞
    dismiss();//解散群组
    bind_page();//查看更多

});
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

var attend = function () {
    $('#attend').unbind('click');
    $('#attend').click(function () {
        var group_id = $(this).attr('data-group-id');
        var url = $(this).attr('url');
        $.post(url, {group_id: group_id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
var quit = function () {
    $('#quit').unbind('click');
    $('#quit').click(function () {
        var group_id = $(this).attr('data-group-id');
        var url = $(this).attr('url');
        $.post(url, {group_id: group_id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);

            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//创建帖子分类
var saveType = function () {
    $('.saveType').unbind('click');
    $('.saveType').click(function () {
        var data = $(".createType").serialize();
        var url = $(".createType").attr('action');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//帖子分类管理编辑删除操作
var bind_group_cate = function(){
    $('[data-role="edit_cate"]').unbind('click');
    $('[data-role="edit_cate"]').click(function () {
        var obj = $(this);
        var title = obj.attr('data-title');
        var html = '<input data-role="save_cate" value="' + title + '">'
        obj.closest('.category').find('.cate_title').html(html);
        bind_group_cate();
        obj.closest('.category').find('.cate_title').find('input').focus();
    })

/*    $('[data-role="save_cate"]').unbind('keypress');
    $('[data-role="save_cate"]').keypress(function (e) {
        if (e.which == 13 || e.which == 10) {
            $.post(U('Mob/Group/editCate'), {group_id: group_id, cate_id: $(this).closest('.category').attr('data-id'), title: $(this).val()}, function (res) {
                handleAjax(res);
            })
        }
    });*/
    $('[data-role="save_cate"]').unbind('blur');
    $('[data-role="save_cate"]').blur(function (e) {
        $.post(U('Mob/Group/editCate'), {group_id: group_id, cate_id: $(this).closest('.category').attr('data-id'), title: $(this).val()}, function (res) {
            handleAjax(res);
        })
    });

    $('[data-role="del_cate"]').unbind('click');
    $('[data-role="del_cate"]').click(function () {
        $.post(U('Mob/Group/delCate'), {group_id: group_id, cate_id: $(this).parents('.category').attr('data-id')}, function (res) {
            handleAjax(res);
        })
    })
}

//回复弹窗
function invitationFriend() {
    $('.invitationFriend').magnificPopup({
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
//创建帖子分类
var addNotic = function () {
    $('.addNotic').unbind('click');
    $('.addNotic').click(function () {
        var data = $(".notic").serialize();
        var url = $(".notic").attr('action');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}

//移除用户
var removeGroupMember= function() {
    $('.remove').unbind('click');
    $('.remove').click(function () {
        if (confirm("你确定要移除此用户吗？")) {
            var uid = $(this).attr('data-uid');
            var group_id = $(this).attr('group-id');
            var url = $(this).attr('url');
            $.post(url, {uid:uid,group_id: group_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                    toast.success(msg.info);
                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
//审核群组人员
var receiveMember = function () {
    $('.receive').unbind('click');
    $('.receive').click(function () {
        var uid = $(this).attr('data-uid');
        var group_id = $(this).attr('group-id');
        var url = $(this).attr('url');
        $.post(url, {uid:uid,group_id: group_id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
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
                    toast.success(msg.info);

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
                    toast.success(msg.info);
                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}

//删除帖子
function delpost() {
    $('.delpost').unbind('click');
    $('.delpost').click(function () {
        if (confirm("你确定要删除此贴吗？")) {
            var post_id = $(this).attr('post-id');
            var url = $(this).attr('url');
            $.post(url, {post_id: post_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.href = document.referrer
                    }, 500);
                    toast.success('删除成功!');
                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
//收藏
var collect = function () {
    $('.collect').unbind('click');
    $('.collect').click(function () {
        var post_id = $(this).attr('post-id');
        var flag = $(this).attr('flag');
        var url = $(this).attr('url');
        $.post(url, {flag: flag, post_id: post_id}, function (msg) {
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
//取消收藏
var unCollect = function () {
    $('.unCollect').unbind('click');
    $('.unCollect').click(function () {
        var post_id = $(this).attr('post-id');
        var url = $(this).attr('url');
        $.post(url, {post_id: post_id}, function (msg) {
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
//解散群组
//删除帖子
var dismiss=function () {
    $('.dismiss').unbind('click');
    $('.dismiss').click(function () {
        if (confirm("你确定要解散此群组吗？")) {
            var group_id = $(this).attr('group-id');
            var url = $(this).attr('url');
            $.post(url, {group_id: group_id}, function (msg) {
                if (msg.status) {
                    setTimeout(function () {
                        window.location.href = document.referrer
                    }, 500);
                    toast.success(msg.info);
                } else {
                    toast.error(msg.info);
                }
            }, 'json')
        }
    });
}
var page = 1;
var bind_page=function(){
    $('#getmorepostlist').unbind('click');
    $('#getmorepostlist').click(function () {
        $("#getmorepostlist").html("查看更多...");
        var mark=$(this).attr('data-mark');
        $.post(U('Mob/group/addMoreIndex'), {page: page + 1,mark:mark}, function (msg) {
            if (msg.status) {
                $(".ulpost").append(msg.html);
                page++;
            } else {
                $("#getmorepostlist").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })

    $('#getmoregrouplist').unbind('click');
    $('#getmoregrouplist').click(function () {
        $("#getmoregrouplist").html("查看更多...");
        var typeId=$(this).attr('data-type-id');
        var mark=$(this).attr('data-mark');
        $.post(U('Mob/group/addMoreIndex'), {page: page + 1,typeId:typeId,mark:mark}, function (msg) {
            if (msg.status) {
                $(".ulgroup").append(msg.html);
                page++;
            } else {
                $("#getmoregrouplist").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })

    $('#getmorereply').unbind('click');
    $('#getmorereply').click(function () {
        $("#getmorereply").html("查看更多...");
        var group_id=$(this).attr('data-group-id');
        $.post(U('Mob/group/addMoreReply'), {page: page + 1,group_id:group_id}, function (msg) {
            if (msg.status) {
                $(".ulreply").append(msg.html);
                page++;
            } else {
                $("#getmorereply").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })
}

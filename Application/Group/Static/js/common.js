$(function () {
    group_bind();
    bind_attend_group();
    bind_quit_group();
});


var group_bind = function () {
    ucard();
    bind_bookmark();
    bind_set_top();
    bing_send_lzl_reply();
    bind_del_reply();
    bind_single_line();
    show_lzl();
    bind_lzl_btn();
    bind_del_lzl_reply();
    bind_del_post();
}

var getArgs = function (uri) {
    if (!uri) return {};
    var obj = {},
        args = uri.split("&"),
        l, arg;
    l = args.length;
    while (l-- > 0) {
        arg = args[l];
        if (!arg) {
            continue;
        }
        arg = arg.split("=");
        obj[arg[0]] = arg[1];
    }
    return obj;
};

var switch_visibility = function (obj) {
    if (obj.is(':visible')) {
        obj.hide();
    } else {
        obj.show();
    }
}

var bind_bookmark = function () {
    $('[data-role="doBookmark"]').unbind('click')
    $('[data-role="doBookmark"]').click(function () {
        var button = $(this);
        var url = button.attr('data-url');
        $.post(url, {}, function (a) {
            handleAjax(a)
            if (a.status) {
                switch_visibility($('[data-role="doBookmark"]').eq(0));
                switch_visibility($('[data-role="doBookmark"]').eq(1));

            }
        });

        return false;

    })
}


var bind_set_top = function () {
    $('[data-role="doSetTop"]').unbind('click')
    $('[data-role="doSetTop"]').click(function () {
        var obj = $(this);
        var url = obj.attr('data-url');
        $.post(url, {}, function (a) {
            switch_visibility($('[data-role="doSetTop"]').eq(0));
            switch_visibility($('[data-role="doSetTop"]').eq(1));
            handleAjax(a)
        })
    })

}

var bind_del_post = function () {
    $('[data-role="delPostBtn"]').unbind('click')
    $('[data-role="delPostBtn"]').click(function () {
        var obj = $(this);
        var post_id = obj.attr('data-id');
        if (confirm('确定要删除该贴子么？')) {
            $.post(U('Group/Index/deletePost'), {id:post_id}, function (msg) {
                if(msg.status){
                    toast.success(msg.info);
                    setTimeout(function(){
                        location.href=msg.url;
                    },1500);
                }else{
                    toast.error(msg.info);
                    if(msg.url!=undefined){
                        setTimeout(function(){
                            location.href=msg.url;
                        },1500);
                    }
                }
            })
        }
    })
}


var bind_del_reply = function () {

    $('[data-role="delPostReply"]').unbind('click')
    $('[data-role="delPostReply"]').click(function () {
        var obj = $(this);
        var reply_id = obj.attr('data-id');
        if (confirm('确定要删除该条回复么？')) {
            $.post(U('group/index/delPostReply'), {reply_id: reply_id}, function (a) {
                handleAjax(a)
                obj.closest('.group_reply').fadeOut();
            })
        }

    })
}

var bing_send_lzl_reply = function () {
    $("[data-role='sendLzlReply']").unbind('click');
    $("[data-role='sendLzlReply']").click(function () {
        var $obj = $(this);
        var to_f_reply_id = $obj.attr('data-f-reply-id');
        var to_reply_id = $obj.attr('data-reply-id');
        var content = $obj.closest('.group_reply_textarea').find('textarea').val();
        var url = U('Group/index/doSendLzlReply');
        $.post(url, {to_f_reply_id: to_f_reply_id, to_reply_id: to_reply_id, content: content}, function (msg) {
            if (msg.status) {
                toast.success(msg.info, '温馨提示');
                $obj.closest('.group_reply_textarea').find('textarea').val('');
                close_face();
                var group_lzl_div = $obj.closest('.group_lzl_div');
                if (group_lzl_order == 1) {
                    group_lzl_div.attr('data-lzl-count', parseInt(group_lzl_div.attr('data-lzl-count')) + 1);
                    var count = group_lzl_div.attr('data-lzl-count');
                    group_lzl_page(to_f_reply_id, Math.ceil(count / group_lzl_show_count));
                } else {
                    group_lzl_div.find('.lzl_reply').prepend(msg.html);
                }
                group_bind();

            } else {
                handleAjax(msg)
            }
        }, 'json');
    });
}


var group_lzl_page = function (reply_id, page) {
    $.post(U('group/Index/lzlList'), {reply_id: reply_id, page: page}, function (res) {
        $('#group_reply_' + reply_id).find('.lzl_reply').html(res);
        group_bind();
    }, 'json');
}


var show_comment_textarea = function (obj) {
    var $closetDiv = obj.closest('div');
    $closetDiv.hide();
    $closetDiv.next().show();
    $closetDiv.next().find('textarea').focus();
}


var bind_single_line = function () {
    $('.single_line').unbind('focus');
    $('.single_line').focus(function () {
        show_comment_textarea($(this));
    })
}


var show_lzl = function () {
    $('[data-role="show_lzl"]').unbind('click');
    $('[data-role="show_lzl"]').click(function (e) {
        var reply_id = $(this).attr('data-reply-id');
        var lzlListObject = $('#group_reply_' + reply_id).find('.group_lzl_div')
        if (lzlListObject.is(':visible')) {
            hide_lzl_list(lzlListObject);
        } else {
            show_lzl_list(lzlListObject);
        }
        //取消默认动作
        e.preventDefault();
        return false;
    })
}


var show_lzl_list = function (obj) {

    if (obj.text().trim() == '') {
        var reply_id = obj.closest('.group_reply').attr('data-id');
        $.post(U('group/Index/loadLzl'), {reply_id: reply_id}, function (res) {
            obj.html(res);

            group_bind();
            //  show_comment_textarea(obj.find('.single_line'))
        }, 'json');
    } else {
        obj.show();
    }


}

var hide_lzl_list = function (obj) {
    obj.hide();
}

var bind_lzl_btn = function () {
    $('[data-role="lzl_reply_btn"]').unbind('click');
    $('[data-role="lzl_reply_btn"]').click(function () {
        var $this = $(this);
        var nickname = $this.attr('data-to-nickname');
        var reply_id = $this.attr('data-lzl-id');

        var single = $this.closest('.group_lzl_div').find('.single_line');
        show_comment_textarea(single);

        var textarea = $this.closest('.group_lzl_div').find('textarea');
        textarea.val('回复@' + nickname + ' ：');
        $this.closest('.group_lzl_div').find('[data-role="sendLzlReply"]').attr('data-reply-id', reply_id);

    })

}

var bind_del_lzl_reply = function () {
    $('[data-role="del_lzl_reply"]').unbind('click');
    $('[data-role="del_lzl_reply"]').click(function () {
        if (confirm('确定要删除该回复么？')) {
            var $this = $(this);
            var lzl_id = $this.attr('data-lzl-id');
            var url = U('Group/index/delLzlReply');
            $.post(url, {id: lzl_id}, function (msg) {
                if (msg.status) {
                    toast.success(msg.info, '温馨提示');
                    $this.closest('.group_lzl_reply').fadeOut();

                } else {
                    toast.error(msg.info, '温馨提示');
                }
            });
        }
    });
}

var bind_attend_group = function () {
    $('[data-role="group_attend"]').unbind('click')
    $('[data-role="group_attend"]').click(function () {
        var obj = $(this)
        var group_id = $(this).attr('data-group-id');
        $.post(U('group/index/attend'), {group_id: group_id}, function (res) {
            handleAjax(res);

        })
    })
}

var bind_quit_group = function () {
    $('[data-role="group_quit"]').unbind('click')
    $('[data-role="group_quit"]').click(function () {
        if (confirm('确定要退出该群组么？')) {
            var obj = $(this)
            var group_id = $(this).attr('data-group-id');
            $.post(U('group/index/quit'), {group_id: group_id}, function (res) {
                handleAjax(res);

            })
        }

    })
}


/*manager 管理权限js*/
var bind_group_manager = function(){
    bind_dismiss_group()
}
var bind_dismiss_group = function () {
    $('[data-role="dismiss_group"]').unbind('click')
    $('[data-role="dismiss_group"]').click(function () {
        if(confirm('确定要解散该群组么？')){
            var obj = $(this)
            var group_id = obj.attr('data-group-id');
            $.post(U('group/manage/dismiss'),{group_id:group_id},function(res){
                handleAjax(res);

            })
        }
    })
}


var bind_receive_group = function(){
    $('[data-role="receive_member"]').unbind('click');
    $('[data-role="receive_member"]').click(function(){
        var $this = $(this);
        var uid = $this.attr('data-uid');
        var group_id = $this.attr('data-group-id');
        $.post(U('group/manage/receiveMember'), {uid: uid, group_id: group_id}, function (res) {
            handleAjax(res);
        })
    })
}

var bind_remove_group_member = function(){
    $('[data-role="remove_group_member"]').unbind('click');
    $('[data-role="remove_group_member"]').click(function(){
        if (confirm('确定要移除该成员么？')) {
            var $this = $(this);
            var uid = $this.attr('data-uid');
            var group_id = $this.attr('data-group-id');
            $.post(U('group/manage/removeGroupMember'), {uid: uid, group_id: group_id}, function (res) {
                handleAjax(res);
            })
        }
    });
}


/*manager 管理权限js  end*/









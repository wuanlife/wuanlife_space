$(document).ready(function () {
    bind_page();//查看更多
    collect();          //收藏
    add_info();         //增加信息
    del_info();         //删除信息
    do_all_search();    //关键词搜索
    do_cond_search();//条件搜索
});

var page = 1;
var bind_page=function() {
    $('#getmorelist').unbind('click');
    $('#getmorelist').click(function () {
        $("#getmorelist").html("查看更多...");
        var mark = $(this).attr('data-mark');
        var myText = $(this).attr('data-type');
        $.post(U('Mob/cat/addMoreList'), {page: page + 1, mark: mark,myText:myText}, function (msg) {
            if (msg.status) {
                $(".ullist").append(msg.html);
                page++;
            } else {
                $("#getmorelist").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })
}

var collect=function(){
    $('#collect').unbind('click');
    $('#collect').click(function(){
        var data_id = $(this).attr('data-id');
        $.post(U('Mob/cat/doFav'),{id:data_id},function(msg){
            if(msg.status==1){
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else if(msg.status==2){
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else{
                toast.error(msg.info);
            }
        })
    })
}

var add_info=function(){
    $('.addinfo').unbind('click');
    $('.addinfo').click(function(){
        var data = $(".am-form").serialize();
        var url = $(this).attr('data-url');
        var index_url = $(this).attr('index-url');
        $.post(url,data,function(msg){
            if(msg.status==1){
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            }else if(msg.status==2){
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            }else{
                toast.error(msg.info);
            }
        })
    })
}
var del_info=function(){
    $('.delinfo').unbind('click');
    $('.delinfo').click(function(){
        var url = $(this).attr('url');
        var info_id = $(this).attr('info_id');
        var index_url = $(this).attr('index-url')
        $.post(url,{info_id:info_id},function(msg){
            if(msg.status==1){
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url
                }, 500);
            }else{
                toast.error(msg.info);
            }
        })
    })
}

function do_read(obj,send_id) {
    $.post(U('cat/Center/doRead'), {send_id: send_id}, 'json');
    location.href=$(obj).attr('data-url');
}

var do_all_search=function(){
    $('.doAllSearch').unbind('click');
    $('.doAllSearch').click(function(){
        var data = $(".allsearch").serialize();
        var url = $(this).attr('data-url');
        $.post(url,data,function(msg){
            if (msg.status) {
                $(".ullist").html(msg.html);
                page++;
            } else {
                toast.error(msg.info);
            }

        })
    })
}
var do_cond_search=function(){
    $('.doCondSearch').unbind('click');
    $('.doCondSearch').click(function(){
        var data = $(".condsearch").serialize();

        var url = $(this).attr('data-url');
        $.post(url,data,function(msg){
            if (msg.status) {
                $(".ullist").html(msg.html);
                page++;
            } else {
                toast.error(msg.info);
            }

        })
    })
}

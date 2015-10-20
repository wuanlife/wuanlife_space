$(document).ready(function () {
    get_more_goods_list();
    buy_goods();
});
var page = 1;
var get_more_goods_list=function(){
    $('#getmoregoodslist').unbind('click');
    $('#getmoregoodslist').click(function () {
        var url = $(this).attr('data-url');
        var mark=$(this).attr('data-mark');
        var typeId=$(this).attr('data-type-id');
        $("#getmoregoodslist").html("查看更多...");
        $.post(url, {page: page + 1,mark:mark,typeId:typeId}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmoregoodslist").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        });
    });

    $('#getmoregoodsnotbuy').unbind('click');
    $('#getmoregoodsnotbuy').click(function () {
        var url = $(this).attr('data-url');
        $("#getmoregoodsnotbuy").html("查看更多...");
        $.post(url, {page: page + 1}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmoregoodsnotbuy").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        });
    });

    $('#getmoregoodshasbuy').unbind('click');
    $('#getmoregoodshasbuy').click(function () {
        var url = $(this).attr('data-url');
        $("#getmoregoodshasbuy").html("查看更多...");
        $.post(url, {page: page + 1,mark:1}, function (msg) {

            if (msg.status) {
                $(".ullist").append(msg.html);
                page++;
            } else {
                $("#getmoregoodshasbuy").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        });
    });
}
var buy_goods=function(){
    $('.subbuy').unbind('click');
    $('.subbuy').click(function () {
        var data = $(".buygoods").serialize();
        var url = $(this).attr('data-url');
        var index_url = $(this).attr('index-url')
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}

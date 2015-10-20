$(document).ready(function () {
    bind_page();
    set_best_answer();
    support_up();
    support_down();
});

var page = 1;
function bind_page() {
    $('#getmorequestionlist').unbind('click');
    $('#getmorequestionlist').click(function () {
        var url = $(this).attr('data-url');
        var mark=$(this).attr('data-mark');
        var typeId=$(this).attr('data-type-id');
        $("#getmorequestionlist").html("查看更多...");
        $.post(url, {page: page + 1,mark:mark,typeId:typeId}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
            } else {
                $("#getmorequestionlist").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        });
    });

    $('#getmorequestioncommentlist').unbind('click');
    $('#getmorequestioncommentlist').click(function () {
        var url = $(this).attr('data-url');
        var mark=$(this).attr('data-mark');
        $("#getmorequestioncommentlist").html("查看更多...");
        $.post(url, {page: page + 1,id:mark}, function (msg) {

            if (msg.status) {
                $(".ulclass").append(msg.html);
                page++;
                set_best_answer();
                support_up();
                support_down();
            } else {
                $("#getmorequestioncommentlist").html("全部加载完成！");
                $(".look-more").delay(1000).hide(0);
            }
        });
    });
}

function set_best_answer(){
    $('.set-best').unbind('click');
    $('.set-best').click(function () {
        var url=  $(this).attr('data-url');
        var question_id=  $(this).attr('data-question-id');
        var answer_id=$(this).attr('data-answer-id');
        $.post(url, {question_id: question_id, answer_id: answer_id}, function (msg) {
            if (msg.status) {
                setTimeout(function () {
                    window.location.reload();
                }, 500);
                toast.success('设置成功!');

            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}

function support_up(){
    $('.support_up').unbind('click');
    $('.support_up').click(function () {
        var url=  $(this).attr('data-url');
        var answer_id=  $(this).attr('data-role');
        var that = $(this);
        $.post(url, {answer_id: answer_id}, function (msg) {
            if (msg.status) {
                toast.success('谢谢您的支持!');
                that.parent().find('.up').html(parseInt(that.parent().find('.up').html()) + 1);
                that.removeClass('am-icon-thumbs-o-up');
                that.addClass('am-icon-thumbs-up');
            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}
function support_down(){
    $('.support_down').unbind('click');
    $('.support_down').click(function () {
        var url=  $(this).attr('data-url');
        var answer_id=  $(this).attr('data-role');
        var that = $(this);
        $.post(url, {answer_id: answer_id,type:0}, function (msg) {
            if (msg.status) {
                toast.success('反对成功!');
                that.parent().find('.down').html(parseInt(that.parent().find('.down').html()) + 1);
                that.removeClass('am-icon-thumbs-o-down');
                that.addClass('am-icon-thumbs-down');
            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}


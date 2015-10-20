$(document).ready(function () {
    bigimg();
});


$(function(){
    p = 2;
    $('[data-role="show_more"]').click(function(){
        var $this = $(this);
        var url = $this.attr('data-url');
        $.post(url,{page:p},function(res){
            if(res){
                $('.event-list').append(res);
                p++;
            }else{
                toast.error('没有更多了');
            }
        })

    })


    $("[data-role='del_event']").click(function(){
      if(confirm('确定要删除该活动么？')){
        var $this = $(this);
        var url = U('mob/event/doDelEvent');
        var id = $this.attr('data-id');
        $.post(url,{event_id:id},function(res){
            if(res.status){
                location.href = U('mob/event/index');
                toast.success(res.info);
            }else{
                toast.error(res.info);
            }
        })
        }
    })


    $("[data-role='end_event']").click(function(){
        if(confirm('确定要提前结束该活动么？')){
            var $this = $(this);
            var url = U('mob/event/doEndEvent');
            var id = $this.attr('data-id');
            $.post(url,{event_id:id},function(res){
                if(res.status){
                    location.reload();
                    toast.success(res.info);
                }else{
                    toast.error(res.info);
                }
            })
        }
    })



    $('[data-role="do_comment"]').click(function () {
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



    $('[data-role="verify"]').click(function(){
        var $this = $(this);
        var uid = $this.attr('data-uid');
        var event_id = $this.attr('data-id');
        var tip =$this.attr('data-tip');
        $.post(U('Mob/event/shenhe'), {uid: uid, event_id: event_id, tip: tip}, function (res) {
            if (res.status) {
                toast.success(res.info);
                if(tip == 1){
                    $this.attr('data-tip','0');
                    $this.find('span').eq(0).css('color','#0CC').text('已审核');
                    $this.find('span').eq(1).attr('class','am-icon-close');
                }else{
                    $this.attr('data-tip','1');
                    $this.find('span').eq(0).css('color','red').text('待审核');
                    $this.find('span').eq(1).attr('class','am-icon-check');
                }
            }
            else {
                toast.error(res.info);
            }
        }, 'json');
    })

    $('[data-role="unsign"]').click(function(){
        if (confirm('确定要取消报名么？')) {
            var event_id = $(this).attr('data-eventID');
            $.post(U('Mob/event/unSign'), {event_id: event_id}, function (res) {
                if (res.status) {
                    toast.success(res.info);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
                else {
                    toast.error(res.info);
                }
            }, 'json');
        }
    })






});
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
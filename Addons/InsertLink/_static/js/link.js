/**
 * Created by Administrator on 15-4-30.
 */


var bind_search_link = function () {
    $('[data-role="search_link"]').unbind('click');
    $('[data-role="search_link"]').click(function () {
        link.search_link($(this));
    })
}



var link = {

    html: '<div class="link">' +
        '<a onclick="link.close($(this))" class="XT_face_close"><i class="icon icon-remove"></i></a><dl id="music_input"><dt>网页地址：</dt><dd>' +
        '<input class="form-control pull-left" type="text" id="link_url" style="width:400px"/>' +
        '<input type="button" class="btn btn-default" onclick="" value="搜索" data-role="search_link">' +
        '</dd></dl><div class="link_s_r"></div><input name="feed_type" value="link" type="hidden">' +
        '<input name="title" class="extra" value="" type="hidden"><input name="keywords" class="extra" value="" type="hidden">' +
        '<input name="description" class="extra" value="" type="hidden"><input name="img" class="extra" value="" type="hidden"><input name="site_link" class="extra" value="" type="hidden"></div> ',

    show_box: function () {
        $('#hook_show').html(this.html);
        bind_search_link();
    },
    search_link: function ($this) {
        toast.showLoading();
        var url = $('#insert_link_search_url').val();
        var link = $("#link_url").val();
        $.post(url, {url:link }, function (res) {
                $('input[name=title]').val(res.title);
                var $hook_show = $this.closest('#hook_show');
                var $content = $this.closest('.weibo_post_box').find('#weibo_content');
                $content.val(' #网页分享# ' + res.title +' '+ link);
                $hook_show.find('input[name=title]').val(res.title);
                $hook_show.find('input[name=keywords]').val(res.keywords);
                $hook_show.find('input[name=description]').val(res.description);
                $hook_show.find('input[name=img]').val(res.img);
            $hook_show.find('input[name=site_link]').val(link);
                toast.success('获取成功');

            var des =res.description;
            if(res.description.length>100){
                des =  res.description.substring(0,100)+'...';
            }

                $('.link_s_r').html('<div class="show_link"><div class="pull-left left"><img src="'+res.img+'"></div>' +
                    '<div class="pull-left right"><a class="text-more" title="'+res.title+'" href="'+link+'">'+res.title+'</a>' +
                    '<div class="des">'+des+'</div></div></div>');

            toast.hideLoading();
        },'json')
    },
    close:function(obj){
        if(confirm('是否确定取消发布网页链接？')){
            obj.parents('#hook_show').html('');
            clear_weibo()
        }
    }


}